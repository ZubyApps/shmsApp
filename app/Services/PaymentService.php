<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\SponsorCategoryDto;
use App\Models\User;
use App\Models\Visit;
use App\Models\WalkIn;
use App\Models\Payment;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use App\Events\PaymentCreated;
use Illuminate\Support\Carbon;
use App\Models\MortuaryService;
use App\Events\PaymentDestroyed;
use Illuminate\Support\Facades\DB;

Class PaymentService
{
    public function __construct()
    {       
    }

    public function create(Request $data, User $user): Payment
    {
        // --- STEP 1: PRE-TRANSACTION READS & CONTEXT GATHERING (Reads are fine outside) ---

        $relatedModel = null;
        $relationKey = null;

        // Fetch the related entity (Visit/WalkIn/Mortuary) BEFORE the transaction
        if ($data->walkInId) {
            $relatedModel = WalkIn::find($data->walkInId);
            $relationKey = 'walk_in_id';
        } elseif ($data->mortuaryServiceId) {
            $relatedModel = MortuaryService::find($data->mortuaryServiceId);
            $relationKey = 'mortuary_service_id';
        } elseif ($data->visitId) {
            // Eager load sponsor for the event listeners
            $relatedModel = Visit::with('sponsor')->find($data->visitId); 
            $relationKey = 'visit_id';
        } 

        // --- STEP 2: DATABASE TRANSACTION (Fast, Atomic Write) ---

        $payment = DB::transaction(function () use($data, $user, $relatedModel, $relationKey) {

            // Build Payment Creation Data
            $paymentData = [
                'amount_paid'   => $data->amount,
                'pay_method_id' => $data->payMethod,
                'comment'       => $data->comment,
                'patient_id'    => $data->patientId,
            ];
            
            if($relatedModel){
                $paymentData[$relationKey] = $relatedModel?->id;
            }

            if ($data->backdate) {
                $paymentData['created_at'] = $data->backdate;
            }
            
            // Create Payment (1 Query)
            $payment = $user->payments()->create($paymentData); 
            
            // NOTE: ALL payment recalculation logic is REMOVED from the transaction
            
            return $payment;
        });

        // --- STEP 3: DISPATCH EVENT (After Transaction Commit) ---
        // Dispatch the event with the newly created payment and the related model.
        if ($relatedModel) {
            PaymentCreated::dispatch($payment, $relatedModel);
        }
        
        return $payment;
    }

    public function applyPaymentsWaterfall(mixed $model, float $totalPayments, SponsorCategoryDto $dto, ?int $userId = null): void
    {
        $relation = match (true) {
            $model instanceof Visit => 'visit_id',
            $model instanceof WalkIn => 'walk_in_id',
            $model instanceof MortuaryService => 'mortuary_service_id',
            default => throw new \InvalidArgumentException("Unsupported model type."),
        };
        $id = $model->id;
        $now = Carbon::now()->toDateTimeString();

        // --- 1. SMART CLEANUP ---
        // If totalPayments is 0, reset everything. 
        // If > 0, only reset the ones that shouldn't have bills (qty < 1).
        DB::table('prescriptions')
            ->where($relation, $id)
            ->when($totalPayments <= 0, 
                fn($q) => $q, // No extra where, reset all
                fn($q) => $q->where('qty_billed', '<', 1) // Only reset invalid ones
            )
            ->update(['paid' => 0, 'paid_at' => null, 'paid_by' => null]);

        if ($totalPayments <= 0) return;

        // --- 2. Bill Selection Logic ---
        $billExpression = match (true) {
                $dto->isNhis => 'CASE WHEN approved THEN nhis_bill ELSE hms_bill END',
                $dto->isHmo => 'hmo_bill',
                $dto->isRetainership => 'hms_bill',
            default => 'CASE WHEN approved THEN 0 ELSE hms_bill END',
        };

        // Only select what we absolutely need to calculate the waterfall in PHP
        $prescriptions = DB::table('prescriptions')
            ->where($relation, $id)
            ->where('qty_billed', '>=', 1)
            ->orderBy('created_at')
            ->orderBy('id')
            ->select(['id', DB::raw("COALESCE({$billExpression}, 0) AS bill")]) 
            ->get();

        if ($prescriptions->isEmpty()) return;

        // --- 3. Payments Waterfall Calculation ---
        $remaining = $totalPayments;
        $count = $prescriptions->count();
        
        $paidCases = $paidAtCases = $paidByCases = $ids = [];

        foreach ($prescriptions as $index => $p) {
            $bill = (float)$p->bill;
            $pay = ($index === $count - 1) ? max(0, $remaining) : min($bill, max(0, $remaining));
            $remaining -= $pay;

            $ids[] = $p->id;
            
            // Paid Amount Case
            $paidCases[] = "WHEN id = {$p->id} THEN {$pay}";

            if ($pay > 0) {
                // STICKY LOGIC: If it was already paid, keep the old date, else set to $now
                $paidAtCases[] = "WHEN id = {$p->id} THEN COALESCE(paid_at, '{$now}')";
                // USER LOGIC: If a userId is passed, use it, else keep old paid_by
                $valBy = $userId ?? "paid_by";
                $paidByCases[] = "WHEN id = {$p->id} THEN {$valBy}";
            } else {
                $paidAtCases[] = "WHEN id = {$p->id} THEN NULL";
                $paidByCases[] = "WHEN id = {$p->id} THEN NULL";
            }
        }

        // --- 4. THE POWER UPDATE ---
        $paidSql = implode(' ', $paidCases);
        $paidAtSql = implode(' ', $paidAtCases);
        $paidBySql = implode(' ', $paidByCases);
        $idList = implode(',', $ids);

        DB::statement("
            UPDATE prescriptions 
            SET 
                paid = CASE {$paidSql} ELSE 0 END,
                paid_at = CASE {$paidAtSql} ELSE NULL END,
                paid_by = CASE {$paidBySql} ELSE NULL END
            WHERE id IN ({$idList})
        ");
    }

    public function destroyPayment(Payment $payment, bool $softDelete, $data)
    {
        // --- STEP 1: PRE-TRANSACTION READS & CONTEXT GATHERING ---
        
        // Identify the associated entity (WalkIn, MortuaryService, or Visit)
        if ($payment->walkIn) {
            $relatedModel = $payment->walkIn;
        } elseif ($payment->mortuaryService) {
            $relatedModel = $payment->mortuaryService;
        } elseif ($payment->visit) {
            // Eager load sponsor for the event listener to avoid N+1 issues later
            $relatedModel = $payment->visit()->with('sponsor:id,category_name')->first();
        } else {
            $relatedModel = null;
        }

        // Capture the amount being removed for the comment if soft deleting
        $amountRemoved = $payment->amount_paid;
        $deleteComment = $data->user()->username . ' removed N'. $amountRemoved . ' - ' .$data->deleteReason;

        // --- STEP 2: DATABASE TRANSACTION (Fast, Atomic Write) ---
        
        $response = DB::transaction(function () use($payment, $softDelete, $data, $deleteComment) {
            
            // 1. Determine the Payment Action (Delete or Zero-Out)
            if ($softDelete) {
                // Soft Delete / Zero-out logic (for non-admins or custom flow)
                $payment->update([
                    'amount_paid' => 0, 
                    'comment' => $deleteComment
                ]);
                
                // NOTE: If using Laravel's soft deletes, you would use $payment->delete();
                // Since you are zeroing out the amount, we return the payment model itself.
                return $payment; 
                
            } else {
                // Actual delete (for admins)
                return $payment->delete(); // This returns true/false or null on success
            }
        });

        // --- STEP 3: DISPATCH EVENT (After Transaction Commit) ---
        
        // Dispatch the event only if a billable entity was attached
        if ($relatedModel) {
            // Pass the related model which needs recalculation
            PaymentDestroyed::dispatch($relatedModel);
        }
        
        // Return the response from the transaction (either the Payment model or true/false)
        return $response;
    }

    public function getCashPaymentsByDate(Request $data)
    {
        $dateInput = $data->date;
        $targetDate = $dateInput ? new CarbonImmutable($dateInput) : new CarbonImmutable();

        // 1. Compute high-performance, index-friendly date ranges
        if ($data->accessor) {
            // Target an entire month boundaries
            $startDate = $targetDate->startOfMonth()->toDateTimeString();
            $endDate   = $targetDate->endOfMonth()->toDateTimeString();
        } else {
            // Target a single day boundaries
            $startDate = $targetDate->startOfDay()->toDateTimeString();
            $endDate   = $targetDate->endOfDay()->toDateTimeString();
        }

        // 2. Execute highly indexed aggregate scan
        return DB::table('payments')
            ->selectRaw('SUM(payments.amount_paid) as totalCash, pay_methods.id as id')
            ->join('pay_methods', 'payments.pay_method_id', '=', 'pay_methods.id')
            ->where('pay_methods.name', 'Cash')
            ->where(function ($q) {
                $q->whereNotNull('payments.visit_id')
                ->orWhereNotNull('payments.walk_in_id')
                ->orWhereNotNull('payments.mortuary_service_id');
            })
            ->whereBetween('payments.created_at', [$startDate, $endDate])
            ->groupBy('pay_methods.id')
            ->first();
    }
}