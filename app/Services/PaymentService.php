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

    // public function create(Request $data, User $user): Payment
    // {
    //     return DB::transaction(function () use($data, $user) {
            
    //         if (!$data->patientId || !$data->visitId ){

    //             if ($data->walkInId){
    //                 $payment = $user->payments()->create([
    //                     'amount_paid'   => $data->amount,
    //                     'pay_method_id' => $data->payMethod,
    //                     'comment'       => $data->comment,
    //                     'walk_in_id'    => $data->walkInId,
    //                 ]);
                    
    //                 $walkIn = $payment->walkIn;
    //                 $totalPayments = $walkIn->totalPayments();
    //                 $prescriptions = $walkIn->prescriptions;

    //                 $this->noSponsorPaymentSeive($totalPayments, $prescriptions);

    //                 $walkIn->update([
    //                     'total_bill'    => $walkIn->totalHmsBills(),
    //                     'total_paid'    => $walkIn->totalPaidPrescriptions() ?? $totalPayments,
    //                 ]);

    //                 return $payment;
    //             }

    //             if ($data->mortuaryServiceId){
    //                 $payment = $user->payments()->create([
    //                     'amount_paid'           => $data->amount,
    //                     'pay_method_id'         => $data->payMethod,
    //                     'comment'               => $data->comment,
    //                     'mortuary_service_id'   => $data->mortuaryServiceId,
    //                 ]);

    //                 $mortuaryService = $payment->mortuaryService;
    //                 $totalPayments = $mortuaryService->totalPayments();
    //                 $prescriptions = $mortuaryService->prescriptions;

    //                 $this->noSponsorPaymentSeive($totalPayments, $prescriptions);

    //                 $mortuaryService->update([
    //                     'total_bill'    => $mortuaryService->totalHmsBills(),
    //                     'total_paid'    => $mortuaryService->totalPaidPrescriptions() ?? $totalPayments,
    //                 ]);


    //                 return $payment;
    //             }

    //             if ($data->backdate){
    //                 $payment = $user->payments()->create([
    //                     'amount_paid'   => $data->amount,
    //                     'pay_method_id' => $data->payMethod,
    //                     'comment'       => $data->comment,
    //                     'created_at'    => $data->backdate,
    //                 ]);
    
    //                 return $payment;
    //             }

    //             $payment = $user->payments()->create([
    //                 'amount_paid'   => $data->amount,
    //                 'pay_method_id' => $data->payMethod,
    //                 'comment'       => $data->comment,
    //             ]);

    //             return $payment;
    //         }

    //         $payment = $user->payments()->create([
    //             'amount_paid'   => $data->amount,
    //             'pay_method_id' => $data->payMethod,
    //             'comment'       => $data->comment,
    //             'patient_id'    => $data->patientId,
    //             'visit_id'      => $data->visitId,
    //         ]);

    //         $visit = $payment->visit;

    //         $totalPayments = $visit->totalPayments();

    //         $prescriptions = $visit->prescriptions;

    //         if ($visit->sponsor->sponsorCategory->name == 'NHIS'){
    //             $this->prescriptionsPaymentSeiveNhis($totalPayments, $prescriptions);
    //         } else {
    //             $this->prescriptionsPaymentSeive($totalPayments, $prescriptions);
    //         }
            
    //         $visit->update([
    //             'total_paid'        => $visit->sponsor->category_name == 'HMO' ? $visit->totalPaidPrescriptions() : $totalPayments,
    //             'total_hms_bill'    => $visit->totalHmsBills(),
    //             'total_nhis_bill'   => $visit->sponsor->category_name == 'NHIS' ? $visit->totalNhisBills() : 0,
    //         ]);
            
    //         return $payment;
    //     });
    // }

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

        // if ($relationKey && $relatedModel) {
        //      $paymentData[$relationKey] = $relatedModel->id;
        // }
        
        // if ($data->patientId && $data->visitId) {
        //     $paymentData['patient_id'] = $data->patientId;
        //     $paymentData['visit_id'] = $data->visitId;
        // }
        
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
    // public function applyPaymentsWaterfall($model, float $totalPayments): void
    // {
    //     $modelId = $model->id;
    //     $relation = $model instanceof Visit ? 'visit_id' : ($model instanceof WalkIn ? 'walk_in_id' : 'mortuary_service_id');

    //     // 1. Force zero on non-billable
    //     DB::table('prescriptions')
    //         ->where($relation, $modelId)
    //         ->where('qty_billed', '<', 1)
    //         ->update(['paid' => 0, 'paid_at' => null]);

    //     // 2. No money → zero everything
    //     if ($totalPayments <= 0) {
    //         DB::table('prescriptions')
    //             ->where($relation, $modelId)
    //             ->update(['paid' => 0, 'paid_at' => null]);
    //         return;
    //     }

    //     // 3. Main waterfall
    //    DB::statement("SET @remaining = ?", [$totalPayments]);
    //     DB::statement("SET @target_id = ?", [$model->id]);

    //     DB::statement("
    //         WITH ordered AS (
    //             SELECT 
    //                 id,
    //                 COALESCE(CASE WHEN approved THEN nhis_bill ELSE hms_bill END, 0) AS bill,
    //                 ROW_NUMBER() OVER (ORDER BY created_at ASC, id ASC) AS rn,
    //                 COUNT(*) OVER() AS total_rows
    //             FROM prescriptions
    //             WHERE $relation = @target_id
    //             AND qty_billed >= 1
    //             AND COALESCE(CASE WHEN approved THEN nhis_bill ELSE hms_bill END, 0) > 0
    //         ),
    //         waterfall AS (
    //             SELECT 
    //                 id,
    //                 bill,
    //                 rn,
    //                 total_rows,
    //                 @remaining := CASE WHEN rn = total_rows THEN 0 ELSE GREATEST(@remaining - bill, 0) END AS remaining_after,
    //                 CASE WHEN rn = total_rows THEN @remaining + bill ELSE LEAST(bill, @remaining + bill) END AS payable
    //             FROM ordered
    //         )
    //         UPDATE prescriptions p
    //         JOIN waterfall w ON p.id = w.id
    //         SET 
    //             p.paid = w.payable,
    //             p.paid_at = CASE WHEN w.payable > 0 THEN NOW() ELSE NULL END
    //     ");
    // }

    // public function applyPaymentsWaterfall($model, float $totalPayments): void
    // {
    //     $relation = $model instanceof Visit ? 'visit_id' : 
    //             ($model instanceof WalkIn ? 'walk_in_id' : 'mortuary_service_id');
    //     $id = $model->id;

    //     // 1. Force zero on non-billable
    //     DB::table('prescriptions')
    //         ->where($relation, $id)
    //         ->where('qty_billed', '<', 1)
    //         ->update(['paid' => 0, 'paid_at' => null]);

    //     if ($totalPayments <= 0) {
    //         DB::table('prescriptions')
    //             ->where($relation, $id)
    //             ->update(['paid' => 0, 'paid_at' => null]);
    //         return;
    //     }

    //     // 2. ONE SINGLE STATEMENT — THIS IS THE ONLY WAY
    //    DB::statement("
    //         WITH billable AS (
    //             SELECT 
    //                 id,
    //                 COALESCE(CASE WHEN approved THEN nhis_bill ELSE hms_bill END, 0) AS bill
    //             FROM prescriptions
    //             WHERE $relation = ?
    //             AND qty_billed >= 1
    //             AND COALESCE(CASE WHEN approved THEN nhis_bill ELSE hms_bill END, 0) > 0
    //             ORDER BY created_at ASC, id ASC
    //         ),
    //         ordered AS (
    //             SELECT 
    //                 id,
    //                 bill,
    //                 ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) AS rn,
    //                 COUNT(*) OVER () AS total_rows
    //             FROM billable
    //         ),
    //         waterfall AS (
    //             SELECT 
    //                 id,
    //                 bill,
    //                 rn,
    //                 total_rows,
    //                 @remaining := CASE WHEN rn = 1 THEN ? ELSE GREATEST(@remaining - bill, 0) END AS remaining_after,
    //                 CASE WHEN rn = total_rows THEN @remaining + bill ELSE LEAST(bill, @remaining + bill) END AS payable
    //             FROM ordered
    //             CROSS JOIN (SELECT @remaining := ?) AS init
    //         )
    //         UPDATE prescriptions p
    //         JOIN waterfall w ON p.id = w.id
    //         SET p.paid = w.payable, p.paid_at = CASE WHEN w.payable > 0 THEN NOW() ELSE NULL END
    //     ", [$id, $totalPayments, $totalPayments]);
    // }


    /**
     * Applies the total payment amount to associated prescriptions in a waterfall sequence.
     * This method is robust, database-agnostic, and avoids the N+1 update problem.
     *
     * @param mixed $model An Eloquent model (Visit, WalkIn, or MortuaryService).
     * @param float $totalPayments The total amount of money to be applied.
     * @return void
     */
    // public function applyPaymentsWaterfall(mixed $model, float $totalPayments, SponsorCategoryDto $dto): void
    // {
    //     // Determine the foreign key relationship based on the model instance
    //     $relation = match (true) {
    //         $model instanceof Visit => 'visit_id',
    //         $model instanceof WalkIn => 'walk_in_id',
    //         $model instanceof MortuaryService => 'mortuary_service_id',
    //         default => throw new \InvalidArgumentException("Unsupported model type for payments waterfall."),
    //     };
    //     $id = $model->id;
    //     $now = Carbon::now()->toDateTimeString();

    //     ## 1. Initial Cleanup for Non-Billable Items
    //     // Zero out items that have no quantity billed, regardless of the payment status.
    //     DB::table('prescriptions')
    //         ->where($relation, $id)
    //         ->where('qty_billed', '<', 1)
    //         ->update(['paid' => 0, 'paid_at' => null]); // 1 Query

    //     // Handle case where no money is paid (resets all other paid status)
    //     if ($totalPayments <= 0) {
    //         DB::table('prescriptions')
    //             ->where($relation, $id)
    //             ->update(['paid' => 0, 'paid_at' => null]); // 1 Query (if needed)
    //         return;
    //     }

    //         // --- Dynamic Bill Selection Logic (SQL) ---
    //     if ($dto->isNhis) {
    //         // If NHIS, choose between nhis_bill (if approved) or hms_bill (if not approved)
    //         $billExpression = 'CASE WHEN approved THEN nhis_bill ELSE hms_bill END';
    //     } elseif ($dto->isHmo) {
    //         $billExpression = 'hmo_bill';
    //     } elseif ($dto->isRetainership) {
    //         $billExpression = 'hms_bill';
    //     } else {
    //         // If Non-NHIS, bill is 0 if approved, otherwise use hms_bill
    //         $billExpression = 'CASE WHEN approved THEN 0 ELSE hms_bill END';
    //     }
        
    //     // Wrap with COALESCE to handle NULLs and ensure a non-negative value
    //     $billSelectionSql = DB::raw("COALESCE({$billExpression}, 0) AS bill");
        
    //     ## 2. Fetch Prescriptions (Single SELECT Query)
    //     $prescriptions = DB::table('prescriptions')
    //         ->where($relation, $id)
    //         ->where('qty_billed', '>=', 1)
    //         ->orderBy('created_at')
    //         ->orderBy('id')
    //         ->select(['id', $billSelectionSql])
    //         ->get(); // 1 Query

    //     if ($prescriptions->isEmpty()) {
    //         return;
    //     }

    //     ## 3. Payments Waterfall Calculation (In PHP)
    //     $remaining = $totalPayments;
    //     $idList = [];
    //     $paidCases = [];
    //     $paidAtCases = [];
    //     $paidBindings = [];
    //     $paidAtBindings = [];
    //     $count = $prescriptions->count();

    //     foreach ($prescriptions as $index => $p) {
    //         $bill = (float)$p->bill; // The `bill` column already holds the calculated, correct bill value
    //         $pay = 0.0;
            
    //         if ($index === $count - 1) {
    //             // LAST ONE: Dumping ground logic
    //             $pay = max(0, $remaining); 
    //         } else {
    //             // ALL OTHERS: Pay the lesser of the current bill or the remaining funds.
    //             $payableAmount = min($bill, $remaining);
    //             $pay = max(0, $payableAmount);
    //             $remaining = max(0, $remaining - $pay);
    //         }

    //         // --- Build CASE statement components ---
            
    //         $paidCases[] = "WHEN id = ? THEN ?";
    //         $paidBindings[] = $p->id;
    //         $paidBindings[] = $pay;

    //         $paidAtValue = ($pay > 0) ? $now : null;
    //         $paidAtCases[] = "WHEN id = ? THEN ?";
    //         $paidAtBindings[] = $p->id;
    //         $paidAtBindings[] = $paidAtValue;

    //         $idList[] = $p->id;
    //     }

    //     if (empty($idList)) {
    //         return;
    //     }
        
    //     $idPlaceholders = implode(',', array_fill(0, $count, '?'));
    //     $idBindings = $idList;

    //     ## 4. Bulk Update (Single UPDATE Query)
    //     $paidSql = implode(' ', $paidCases);
    //     $paidAtSql = implode(' ', $paidAtCases);

    //     $sql = "UPDATE prescriptions 
    //             SET 
    //                 paid = (CASE {$paidSql} ELSE 0 END),
    //                 paid_at = (CASE {$paidAtSql} END)
    //             WHERE id IN ({$idPlaceholders})";

    //     DB::update($sql, array_merge($paidBindings, $paidAtBindings, $idBindings)); // 1 Query
    // }

    public function applyPaymentsWaterfall(mixed $model, float $totalPayments, SponsorCategoryDto $dto, ?int $userId = null): void
    {
        $relation = match (true) {
            $model instanceof Visit => 'visit_id',
            $model instanceof WalkIn => 'walk_in_id',
            $model instanceof MortuaryService => 'mortuary_service_id',
            default => throw new \InvalidArgumentException("Unsupported model type for payments waterfall."),
        };
        $id = $model->id;
        $now = Carbon::now()->toDateTimeString();

        ## 1. Initial Cleanup (Now including paid_by)
        DB::table('prescriptions')
            ->where($relation, $id)
            ->where('qty_billed', '<', 1)
            ->update(['paid' => 0, 'paid_at' => null, 'paid_by' => null]);

        if ($totalPayments <= 0) {
            DB::table('prescriptions')
                ->where($relation, $id)
                ->update(['paid' => 0, 'paid_at' => null, 'paid_by' => null]);
            return;
        }

        // --- Bill Selection Logic ---
        $billExpression = match (true) {
            $dto->isNhis => 'CASE WHEN approved THEN nhis_bill ELSE hms_bill END',
            $dto->isHmo => 'hmo_bill',
            $dto->isRetainership => 'hms_bill',
            default => 'CASE WHEN approved THEN 0 ELSE hms_bill END',
        };
        
        $billSelectionSql = DB::raw("COALESCE({$billExpression}, 0) AS bill");
        
        ## 2. Fetch Prescriptions (Now selecting paid_at and paid_by)
        $prescriptions = DB::table('prescriptions')
            ->where($relation, $id)
            ->where('qty_billed', '>=', 1)
            ->orderBy('created_at')
            ->orderBy('id')
            // We MUST fetch these to handle the "Don't overwrite" and "Optional" logic
            ->select(['id', 'paid_at', 'paid_by', $billSelectionSql]) 
            ->get();

        if ($prescriptions->isEmpty()) return;

        ## 3. Payments Waterfall Calculation
        $remaining = $totalPayments;
        $idList = $paidCases = $paidAtCases = $paidByCases = [];
        $paidBindings = $paidAtBindings = $paidByBindings = [];
        $count = $prescriptions->count();

        foreach ($prescriptions as $index => $p) {
            $bill = (float)$p->bill;
            
            if ($index === $count - 1) {
                $pay = max(0, $remaining); 
            } else {
                $payableAmount = min($bill, $remaining);
                $pay = max(0, $payableAmount);
                $remaining = max(0, $remaining - $pay);
            }

            // --- THE LOGIC ---
            $idList[] = $p->id;

            // 1. Paid Amount
            $paidCases[] = "WHEN id = ? THEN ?";
            $paidBindings[] = $p->id;
            $paidBindings[] = $pay;

            // 2. Paid At (Sticky Logic)
            // If money is allocated and it already has a date, KEEP IT. Otherwise, set it to $now.
            $paidAtValue = ($pay > 0) ? ($p->paid_at ?? $now) : null;
            $paidAtCases[] = "WHEN id = ? THEN ?";
            $paidAtBindings[] = $p->id;
            $paidAtBindings[] = $paidAtValue;

            // 3. Paid By (Optional Logic)
            // If money is allocated AND a $userId was provided, update it.
            // Otherwise, keep the existing value (even if it's null).
            $paidByValue = ($pay > 0) ? ($userId ?? $p->paid_by) : null;
            $paidByCases[] = "WHEN id = ? THEN ?";
            $paidByBindings[] = $p->id;
            $paidByBindings[] = $paidByValue;
        }

        ## 4. Bulk Update (Still a single Query!)
        $paidSql = implode(' ', $paidCases);
        $paidAtSql = implode(' ', $paidAtCases);
        $paidBySql = implode(' ', $paidByCases);
        $idPlaceholders = implode(',', array_fill(0, $count, '?'));

        $sql = "UPDATE prescriptions 
                SET 
                    paid = (CASE {$paidSql} ELSE 0 END),
                    paid_at = (CASE {$paidAtSql} END),
                    paid_by = (CASE {$paidBySql} END)
                WHERE id IN ({$idPlaceholders})";

        DB::update($sql, array_merge($paidBindings, $paidAtBindings, $paidByBindings, $idList));
    }

    // public function prescriptionsPaymentSeive(mixed $totalPayments, mixed $prescriptions): void
    // {
    //     $dateTime = Carbon::now();
    //     $filteredPrescriptions = $prescriptions->reject(function (object $value) {
    //                 $value->update(['paid' => 0]);
    //         return $value->qty_billed < 1;
    //     });

    //     array_reduce([$filteredPrescriptions], function($carry, $prescription) use($dateTime) {

    //         foreach($prescription as $p){
    //             $bill = $p->approved ? 0 : $p->hms_bill;

    //             if ($carry >= $bill){
    //                 $p->update(['paid' => $p->id == $prescription->last()->id ? $carry : $bill, 'paid_at' => $dateTime]);
    //             }
                
    //             if ($carry < $bill && $carry > 0){
    //                 $p->update(['paid' => $carry, 'paid_at' => $dateTime]);
    //             }
                
    //             if ($carry <= 0 && $bill > 0){
    //                 $p->update(['paid' => 0 ]);
    //             }

    //             $carry = $carry - $bill;
    //         }
    //         return $carry;

    //     }, $totalPayments);
    // }
    
    // public function prescriptionsPaymentSeiveNhis(mixed $totalPayments, mixed $prescriptions): void
    // {
    //     $dateTime = Carbon::now();
    //     $filteredPrescriptions = $prescriptions->reject(function (object $value) {
    //                 $value->update(['paid' => 0]);
    //         return $value->qty_billed < 1;
    //     });
    //     array_reduce([$filteredPrescriptions], function($carry, $prescription) use($dateTime) {

    //         foreach($prescription as $p){
    //             $bill = $p->approved ? $p->nhis_bill : $p->hms_bill;
                
    //             if ($carry >= $bill){
    //                 $p->update(['paid' => $p->id == $prescription->last()->id ? $carry : $bill, 'paid_at' => $dateTime]);
    //             }

    //             if ($carry < $bill && $carry > 0){
    //                 $p->update(['paid' => $carry, 'paid_at' => $dateTime]);
    //             }

    //             if ($carry <= 0 && $bill > 0){
    //                 $p->update(['paid' => 0 ]);
    //             }

    //             $carry = $carry - $bill;
    //         }
    //         return $carry;

    //     }, $totalPayments);
    // }

    // public function prescriptionsPaymentSeiveHmo(mixed $totalPayments, mixed $prescriptions): void
    // {
    //     $dateTime = Carbon::now();
    //     array_reduce([$prescriptions], function($carry, $prescription) use($dateTime) {

    //         foreach($prescription as $p){
    //             $bill = $p->hmo_bill;
    //             if ($carry >= $bill){    
    //                 $p->update(['paid' => $p->id == $prescription->last()->id ? $carry : $bill, 'paid_at' => $dateTime]);
    //             }

    //             if ($carry < $bill && $carry > 0){
    //                 $p->update(['paid' => $carry, 'paid_at' => $dateTime]);
    //             }

    //             if ($carry <= 0 && $bill > 0){
    //                 $p->update(['paid' => 0]);
    //             }

    //             $carry = $carry - $bill;
    //         }
    //         return $carry;

    //     }, $totalPayments);
    // }

    // public function prescriptionsPaymentSeiveRetanership(mixed $totalPayments, mixed $prescriptions): void
    // {
    //     $dateTime = Carbon::now();
    //     array_reduce([$prescriptions], function($carry, $prescription) use($dateTime) {

    //         foreach($prescription as $p){
    //             $bill = $p->hms_bill;
    //             if ($carry >= $bill){
    //                 $p->update(['paid' => $p->id == $prescription->last()->id ? $carry : $bill, 'paid_at' => $dateTime]);
    //             }

    //             if ($carry < $bill && $carry > 0){
    //                 $p->update(['paid' => $carry, 'paid_at' => $dateTime ]);
    //             }

    //             if ($carry <= 0 && $bill > 0){
    //                 $p->update(['paid' => 0]);
    //             }

    //             $carry = $carry - $bill;
    //         }
    //         return $carry;

    //     }, $totalPayments);
    // }

    // public function destroyPayment(Payment $payment, bool $softDelete, $data)
    // {
    //     return DB::transaction(function () use($payment, $softDelete, $data) {

    //         //delete payments not associated with a patient or visit
    //         if (!$payment->patient_id || !$payment->visit_id ){
                
    //             if ($softDelete){
    //                 $paymentToDestroy = $payment->update(['amount_paid' => 0, 'comment' => $data->user()->username . ' removed N'. $payment->amount_paid. ' - ' .$data->deleteReason]);
                    
    //             } else {
    //                 $paymentToDestroy = $payment->destroy($payment->id);
    //             }

    //             if ($payment->walkIn){
    //                 $walkIn = $payment->walkIn;
    //                 $totalPayments = $walkIn->totalPayments();
    //                 $prescriptions = $walkIn->prescriptions;

    //                 $this->noSponsorPaymentSeive($totalPayments, $prescriptions);

    //                 $walkIn->update([
    //                     'total_bill'    => $walkIn->totalHmsBills(),
    //                     'total_paid'    => $walkIn->totalPaidPrescriptions() ?? $totalPayments,
    //                 ]);
    //             }

    //             if ($payment->mortuaryService){
    //                 $mortuaryService = $payment->mortuaryService;
    //                 $totalPayments = $mortuaryService->totalPayments();
    //                 $prescriptions = $mortuaryService->prescriptions;

    //                 $this->noSponsorPaymentSeive($totalPayments, $prescriptions);

    //                 $mortuaryService->update([
    //                     'total_bill'    => $mortuaryService->totalHmsBills(),
    //                     'total_paid'    => $mortuaryService->totalPaidPrescriptions() ?? $totalPayments,
    //                 ]);
    //             }

    //             return $paymentToDestroy;
    //         }
            
    //         //custom soft delete mechanism for non admins
    //         if ($softDelete){
    //             $response = $payment->update(['amount_paid' => 0, 'comment' => $data->user()->username . ' removed N'. $payment->amount_paid. ' - ' .$data->deleteReason]);
    //         } else {
    //             $response = $payment->destroy($payment->id); //actual delete for admins
    //         }

    //         $totalPayments  = $payment->visit->totalPayments();
    //         $visit          = $payment->visit;

    //         $visit->update([
    //             'total_paid'        => $visit->sponsor->category_name == 'HMO' ? $visit->totalPaidPrescriptions() : $totalPayments,
    //             'total_hms_bill'    => $visit->totalHmsBills(),
    //             'total_nhis_bill'   => $visit->sponsor->category_name == 'NHIS' ? $visit->totalNhisBills() : 0,
    //         ]);

    //         $prescriptions = $visit->prescriptions;

    //         if ($visit->sponsor->category_name == 'NHIS'){
    //             $this->prescriptionsPaymentSeiveNhis($totalPayments, $prescriptions);
    //         } else {
    //             $this->prescriptionsPaymentSeive($totalPayments, $prescriptions);
    //         }

    //         return $response;
    //     });
    
    // }

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

    // public function noSponsorPaymentSeive(mixed $totalPayments, mixed $prescriptions): void
    // {
    //     $dateTime = Carbon::now();
    //     $filteredPrescriptions = $prescriptions->reject(function (object $value) {
    //                 $value->update(['paid' => 0]);
    //         return $value->qty_billed < 1;
    //     });

    //     array_reduce([$filteredPrescriptions], function($carry, $prescription) use($dateTime) {

    //         foreach($prescription as $p){
    //             $bill = $p->approved ? 0 : $p->hms_bill;

    //             if ($carry >= $bill){
    //                 $p->update(['paid' => $p->id == $prescription->last()->id ? $carry : $bill, 'paid_at' => $dateTime]);
    //             }
                
    //             if ($carry < $bill && $carry > 0){
    //                 $p->update(['paid' => $carry, 'paid_at' => $dateTime]);
    //             }
                
    //             if ($carry <= 0 && $bill > 0){
    //                 $p->update(['paid' => 0 ]);
    //             }

    //             $carry = $carry - $bill;
    //         }
    //         return $carry;

    //     }, $totalPayments);
    // }

    public function getCashPaymentsByDate($data)
    {
        $currentDate    = new CarbonImmutable();
        $query          = DB::table('payments')
                                ->selectRaw('SUM(payments.amount_paid) as totalCash, pay_methods.id as id')
                                ->leftJoin('pay_methods', 'payments.pay_method_id', '=', 'pay_methods.id')
                                ->where('pay_methods.name', 'Cash')
                                ->where(function ($q) {
                                    $q->whereNotNull('visit_id')
                                    ->orWhereNotNull('walk_in_id')
                                    ->orWhereNotNull('mortuary_service_id');
                                })
                                ->groupBy('id');

        if ($data->accessor){
            if ($data->date){
                $date = new CarbonImmutable($data->date);
                return $query->whereMonth('payments.created_at', $date->month)
                            ->whereYear('payments.created_at', $date->year)
                            ->first();
            }

            return $query->whereMonth('payments.created_at', $currentDate->month)
                            ->whereYear('payments.created_at', $currentDate->year)
                            ->first();
        }
        if ($data->date){
            return $query->whereDate('payments.created_at', $data->date)
                            ->first();
        }

        return $query->whereDate('payments.created_at',  $currentDate->format('Y-m-d'))
                 ->first();
    }

    public function totalYearlyIncomeFromCashPatients($data)
    {
        $currentDate = new Carbon();

        if ($data->year){

            return DB::table('payments')
                            ->selectRaw('SUM(amount_paid) as cashPaid, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
                            ->whereYear('created_at', $data->year)
                            ->groupBy('month_name', 'month')
                            ->orderBy('month')
                            ->get();
        }

        return DB::table('payments')
                        ->selectRaw('SUM(amount_paid) as cashPaid, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
                        ->whereYear('created_at', $currentDate->year)
                        ->groupBy('month_name', 'month')
                        ->orderBy('month')
                        ->get();
    }
}