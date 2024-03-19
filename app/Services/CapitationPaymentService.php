<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\CapitationPayment;
use App\Models\Sponsor;
use App\Models\SponsorCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapitationPaymentService
{
    public function __construct(private readonly CapitationPayment $capitationPayment)
    {
    }

    public function create(Request $data, User $user): CapitationPayment
    {
        return DB::transaction(function () use($data, $user){
            $capitationPayment = $user->capitationPayments()->create([
                'month_paid_for'        => new Carbon($data->monthPaidFor),
                'number_of_lives'       => $data->numberOfLives,
                'amount_paid'           => $data->amountPaid,
                'bank'                  => $data->bank,
                'comment'               => $data->comment,
                'sponsor_id'            => $data->sponsor,
            ]);

            $sponsor = $capitationPayment->sponsor;
            $date    = new Carbon($capitationPayment->month_paid_for);
            $amount  = (float)$capitationPayment->amount_paid;

            
            $this->seiveCapitationPayment($sponsor, $date, $amount);

            
            return $capitationPayment;
        });
    }

    public function seiveCapitationPayment(Sponsor $sponsor, Carbon $date, float $amount = 0): void
    {
        $amount == 0 ? $amount = $sponsor->capitationPayments()->whereMonth('month_paid_for', $date->month)->whereYear('month_paid_for', $date->year)->first()?->amount_paid : '';

        if ($amount > 0 ){
            $prescriptions = $sponsor->through('visits')->has('prescriptions')
                                        ->whereMonth('prescriptions.created_at', $date->month)
                                        ->whereYear('prescriptions.created_at', $date->year)->get();
    
            $pCount = $prescriptions->count();
    
            array_reduce([$prescriptions], function($carry, $prescription) use($pCount, $amount) {
                foreach($prescription as $p){
                    $avgCapitation =  $amount/$pCount;
                    $p->update(['capitation' => $carry >= $avgCapitation ? $avgCapitation : ($carry < $avgCapitation && $carry > 0 ? $carry : null)]);
                    $carry = $carry - $avgCapitation;
                }
                return $carry;
    
            }, $amount);
        }


    }

    public function getCapitationPayments(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $currentdate = new Carbon();

        if (! empty($params->searchTerm)) {
            if($data->startedDate && $data->endDate){
                return $this->capitationPayment
                        ->whereRelation('sponsor.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if($data->date){
                $date = new Carbon($data->date);

                return $this->capitationPayment
                        ->whereRelation('sponsor.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->whereMonth('month_paid_for', $date->month)
                        ->whereYear('month_paid_for', $date->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $this->capitationPayment
                        ->whereRelation('sponsor.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->startedDate && $data->endDate){
            return $this->capitationPayment
                    ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
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
                'prescriptionCount' => $capitationPayment->prescription_count,
                'enteredBy'         => $capitationPayment->user->username,
                'createdAt'         => (new Carbon($capitationPayment->created_at))->format('d/m/Y g:ia'),
            ];
         };
    }
}