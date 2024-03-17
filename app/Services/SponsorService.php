<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Consultation;
use App\Models\Prescription;
use App\Models\Sponsor;
use App\Models\SponsorCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SponsorService
{
    public function __construct(private readonly Sponsor $sponsor)
    {
    }

    public function create(Request $data, User $user): Sponsor
    {
        return $user->sponsors()->create([
            'name'                  => $data->name,
            'phone'                 => $data->phone,
            'email'                 => $data->email,
            'registration_bill'     => $data->registrationBill,
            'sponsor_category_id'   => $data->category,
            'category_name'         => SponsorCategory::findOrFail($data->category)->name
        ]);
    }

    public function update(Request $data, Sponsor $sponsor, User $user): Sponsor
    {
       $sponsor->update([
            'name'                  => $data->name,
            'phone'                 => $data->phone,
            'email'                 => $data->email,
            'registration_bill'     => $data->registerationBill,
            'sponsor_category_id'   => $data->category,
            'user_id'               => $user->id

        ]);

        return $sponsor;
    }

    public function getPaginatedSponsors(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->sponsor
                        ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->sponsor
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (Sponsor $sponsor) {
            return [
                'id'                => $sponsor->id,
                'name'              => $sponsor->name,
                'phone'             => $sponsor->phone,
                'email'             => $sponsor->email,
                'category'          => $sponsor->sponsorCategory->name,
                'approval'          => $sponsor->sponsorCategory->approval === 0 ? 'false' : 'true',
                'registrationBill'  => $sponsor->registration_bill,
                'createdAt'         => (new Carbon($sponsor->created_at))->format('d/m/Y'),
                'count'             => $sponsor->patients()->count()
            ];
         };
    }

    // public function getSponsorsWithFilters(DataTableQueryParams $params, $data)
    // {
    //     $orderBy    = 'created_at';
    //     $orderDir   =  'desc';

    //         if (! empty($params->searchTerm)) {
    //             return $this->sponsor
    //                         ->where('id', $data->sponsorId)
    //                         ->where(function (Builder $query) use($params) {
    //                             $query->where('icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                             ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%')
    //                             ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                             ->orWhereRelation('prescriptions.hmoBillBy', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                             ->orWhereRelation('prescriptions.resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
    //                         })
    //                         ->orderBy($orderBy, $orderDir)
    //                         ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //         }

    //     return $this->sponsor
    //             ->where('visit_id', $data->visitId)
    //             ->orderBy($orderBy, $orderDir)
    //             ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    // }

    // public function getprescriptionByConsultationTransformer(): callable
    // {
    //    return  function (Consultation $consultation) {
    //         return [
    //             'id'                    => $consultation->id,
    //             'consultBy'             => $consultation->user->username,
    //             'diagnosis'             => $consultation->icd11_diagnosis ?? 
    //                                        $consultation->provisional_diagnosis ?? 
    //                                        $consultation->assessment, 
    //             'consulted'             => (new Carbon($consultation->created_at))->format('D/m/y g:ia'),                
    //             'conId'                 => $consultation->id,
    //             'sponsor'               => $consultation->visit->sponsor->name,
    //             'sponsorCategoryClass'  => $consultation->visit->sponsor->sponsorCategory->pay_class,
    //             'closed'                => $consultation->visit->closed,
    //             'prescriptions'         => (new Prescription)->forPharmacy($consultation->id)->map(fn(Prescription $prescription)=> [
    //                 'id'                => $prescription->id ?? '',
    //                 'price'             => $prescription->resource?->selling_price ?? '',
    //                 'prescribedBy'      => $prescription->user?->username ?? '',
    //                 'prescribed'        => (new Carbon($prescription->created_at))->format('D/m/y g:ia') ?? '',
    //                 'item'              => $prescription->resource->nameWithIndicators(),
    //                 'stock'             => $prescription->resource->stock_level,
    //                 'category'          => $prescription->resource->category,
    //                 'prescription'      => $prescription->prescription ?? '',
    //                 'qtyBilled'         => $prescription->qty_billed,
    //                 'unit'              => $prescription->resource->unit_description,
    //                 'bill'              => $prescription->hms_bill ?? '',
    //                 'hmsBillBy'         => $prescription->hmsBillBy->username ?? '',
    //                 'billed'            => $prescription->hms_bill_date ? (new Carbon($prescription->hms_bill_date))->format('d/m/y g:ia') : '',
    //                 'approved'          => $prescription->approved, 
    //                 'rejected'          => $prescription->rejected,
    //                 'hmoNote'           => $prescription->hmo_note ?? '',
    //                 'statusBy'          => $prescription->approvedBy?->username ?? $prescription->rejectedBy?->username ?? '',
    //                 'qtyDispensed'      => $prescription->qty_dispensed,
    //                 'dispensedBy'       => $prescription->dispensedBy->username ?? '',
    //                 'dispensed'         => $prescription->dispense_date ? (new Carbon($prescription->dispense_date))->format('d/m/y g:ia') : '',
    //                 'dispenseComment'   => $prescription->dispense_comment ?? '',
    //                 'note'              => $prescription->note ?? '',
    //                 'status'            => $prescription->status ?? '',
    //                 'paid'              => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
    //                 'paidNhis'          => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill/10 && $prescription->visit->sponsor->sponsorCategory->name == 'NHIS',
    //                 'amountPaid'        => $prescription->paid ?? 0,
    //             ]),
    //         ];
    //      };
    // }
}