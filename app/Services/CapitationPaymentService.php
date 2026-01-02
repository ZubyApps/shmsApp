<?php

declare(strict_types = 1);

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Visit;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use App\Models\CapitationPayment;
use Illuminate\Support\Facades\DB;
use App\Events\CapitationPaymentCreated;
use App\Events\CapitationPaymentDeleted;
use App\DataObjects\DataTableQueryParams;
use Illuminate\Support\Carbon as SupportCarbon;

class CapitationPaymentService
{
    public function __construct(private readonly CapitationPayment $capitationPayment)
    {
    }

    public function create(Request $data, User $user): CapitationPayment
    {
        // --- STEP 1: DATABASE TRANSACTION (Atomic Write) ---
        $capitationPayment = DB::transaction(function () use($data, $user){
            $capitationPayment = $user->capitationPayments()->create([
                'month_paid_for'        => new Carbon($data->monthPaidFor),
                'number_of_lives'       => $data->numberOfLives,
                'amount_paid'           => $data->amountPaid,
                'bank'                  => $data->bank,
                'comment'               => $data->comment,
                'sponsor_id'            => $data->sponsor,
            ]);

            return $capitationPayment;
            
        });

        // --- STEP 2: DISPATCH EVENT ---
        CapitationPaymentCreated::dispatch($capitationPayment);

        return $capitationPayment;
    }

    // public function seiveCapitationPayment(Sponsor $sponsor, Carbon $date, float $amount = 0): void
    // {
    //     DB::transaction(function () use($sponsor, $date, $amount) {
    //         $amount == 0 ? $amount = $sponsor->capitationPayments()->whereMonth('month_paid_for', $date->month)->whereYear('month_paid_for', $date->year)->first()?->amount_paid : 0;
    
    //         if ($amount > 0 ){
    //             $prescriptions  = $this->getPrescriptonsByMonthAndYear($sponsor, $date);
    
    //             $pCount = $prescriptions->count();
        
    //             array_reduce([$prescriptions], function($carry, $prescription) use($pCount, $amount) {
    //                 foreach($prescription as $p){
    //                     $avgCapitation =  $amount/$pCount;
    //                     $p->update(['capitation' => $carry >= $avgCapitation ? $avgCapitation : ($carry < $avgCapitation && $carry > 0 ? $carry : null)]);
    //                     $carry = $carry - $avgCapitation;
    //                 }
    //                 return $carry;
        
    //             }, $amount);
    //         }
    //         $this->recalculateVisitsCapitations($sponsor, $date);
    //     });

    // }

        public function seiveCapitationPayment(Sponsor $sponsor, Carbon $date, ?float $amount = null): void
    {
        // Ensure all heavy logic runs in a single transaction for atomicity
        DB::transaction(function () use($sponsor, $date, $amount) {

            $finalAmount = $amount;
        
            // If the amount is not provided (null), retrieve it from the database.
            if ($finalAmount === null) {
                // Retrieve the amount paid for the specific month/year.
                $finalAmount = $sponsor->capitationPayments()
                    ->whereMonth('month_paid_for', $date->month)
                    ->whereYear('month_paid_for', $date->year)
                    ->sum('amount_paid') ?? 0.0; // Use SUM in case multiple payments were made for the month
            }

            // 1. Calculate the total count of target prescriptions (1 Query)
            // We use the raw DB query builder for maximum efficiency here.
            $targetPrescriptionsQuery = DB::table('prescriptions')
                ->join('visits', 'prescriptions.visit_id', '=', 'visits.id')
                ->where('visits.sponsor_id', $sponsor->id)
                ->whereMonth('prescriptions.created_at', $date->month)
                ->whereYear('prescriptions.created_at', $date->year);

                if ($finalAmount > 0) {
                
                // --- Rest of your optimized logic using $finalAmount ---
                
                // 1. Calculate the total count of target prescriptions (1 Query)
                $targetPrescriptionsQuery = DB::table('prescriptions')
                    ->join('visits', 'prescriptions.visit_id', '=', 'visits.id')
                    ->where('visits.sponsor_id', $sponsor->id)
                    ->whereMonth('prescriptions.created_at', $date->month)
                    ->whereYear('prescriptions.created_at', $date->year);

                $pCount = $targetPrescriptionsQuery->count(); 

                if ($pCount === 0) {
                    $capitationPerPrescription = 0;
                } else {
                    $capitationPerPrescription = round($finalAmount / $pCount, 4); 
                }
                
                // 2. Bulk Update Prescriptions (1 Query)
                $targetPrescriptionsQuery->update([
                    'capitation' => $capitationPerPrescription
                ]);
            }

            // 3. Recalculate and Update Visits (1 Query, replacing the N visit update loop)
            $this->recalculateVisitsCapitations($sponsor, $date);
        });
    }

    public function getPrescriptonsByMonthAndYear(Sponsor $sponsor, Carbon $date)
    {
        return $sponsor->through('visits')->has('prescriptions')
        ->whereMonth('prescriptions.created_at', $date->month)
        ->whereYear('prescriptions.created_at', $date->year)->get();
    }

    // public function recalculateVisitsCapitations(Sponsor $sponsor, Carbon $date)
    // {
    //     DB::transaction(function () use($sponsor, $date){
    //         $visits = $sponsor->visits()->whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->get();
    
    //         $visits->map(function(Visit $visit){
    //             $visit->update(['total_capitation' => $visit->totalPrescriptionCapitations()]);
    //         });
    //     });
    // }

    public function recalculateVisitsCapitations(Sponsor $sponsor, Carbon $date): void
    {
        // No need for a separate transaction, use the one from the caller.
        $sponsorId = $sponsor->id;

        // Use a single DB::update with a subquery (single trip)
        DB::table('visits')
            ->where('sponsor_id', $sponsorId)
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->update([
                // Calculate SUM of capitation from related prescriptions and update total_capitation
                'total_capitation' => DB::raw("(
                    SELECT  COALESCE(SUM(capitation), 0) 
                    FROM prescriptions 
                    WHERE prescriptions.visit_id = visits.id
                )")
            ]);
    }

    public function getCapitationPayments(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $currentdate = new Carbon();

        if (! empty($params->searchTerm)) {
        
            if($data->startDate && $data->endDate){
                return $this->capitationPayment
                        ->whereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if($data->date){
        
                $date = new Carbon($data->date);

                return $this->capitationPayment
                        ->whereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->whereMonth('month_paid_for', $date->month)
                        ->whereYear('month_paid_for', $date->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
        
            return $this->capitationPayment
                        ->whereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->startDate && $data->endDate){
            return $this->capitationPayment
                    ->whereBetween('month_paid_for', [$data->startDate, $data->endDate])
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->date){
            $date = new Carbon($data->date);

            return $this->capitationPayment
                    ->whereMonth('month_paid_for', $date->month)
                    ->whereYear('month_paid_for', $date->year)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->capitationPayment
                    ->whereMonth('month_paid_for', $currentdate->month)
                    ->whereYear('month_paid_for', $currentdate->year)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (CapitationPayment $capitationPayment) {
            return [
                'id'                => $capitationPayment->id,
                'monthPaidFor'      => (new Carbon($capitationPayment->month_paid_for))->format('M Y'),
                'lives'             => $capitationPayment->number_of_lives,
                'amountPaid'        => $capitationPayment->amount_paid,
                'bank'              => $capitationPayment->bank,
                'comment'           => $capitationPayment->comment,
                'sponsor'           => $capitationPayment->sponsor->name,
                'enteredBy'         => $capitationPayment->user->username,
                'createdAt'         => (new Carbon($capitationPayment->created_at))->format('d/m/Y g:ia'),
            ];
         };
    }

    // public function processDeletion(CapitationPayment $capitationPayment)
    // {
        
    //     return DB::transaction(function () use($capitationPayment) {

    //         $date    = new Carbon($capitationPayment->month_paid_for);
    //         $sponsor = $capitationPayment->sponsor;

    //         $prescriptions = $this->getPrescriptonsByMonthAndYear($sponsor, $date);
    
    //         $prescriptions->map(function ($prescription){
    //             $prescription->update(['capitation' => 0]);
    //         });

    //         $this->recalculateVisitsCapitations($sponsor, $date);

    //         $capitationPayment->destroy($capitationPayment->id);
    //     });
    // }
    public function processDeletion(CapitationPayment $capitationPayment)
    {
        // --- STEP 1: PRE-TRANSACTION READS & CONTEXT GATHERING ---
        $date = new SupportCarbon($capitationPayment->month_paid_for);
        $sponsor = $capitationPayment->sponsor;
        $sponsorId = $sponsor->id;

        // --- STEP 2: DATABASE TRANSACTION (Atomic Write: Deletion) ---
        $deleted = DB::transaction(function () use($capitationPayment) {
            
            // Delete the CapitationPayment record (1 Query)
            return $capitationPayment->delete();
        });

        // --- STEP 3: DISPATCH EVENT (After Transaction Commit) ---
        if ($deleted) {
            // Dispatch event with the context needed for recalculation
            CapitationPaymentDeleted::dispatch($sponsorId, $date);
        }
        
        return $deleted;
    }
}