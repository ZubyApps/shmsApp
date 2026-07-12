<?php

declare(strict_types = 1);

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use App\DataObjects\DataTableQueryParams;
use App\Models\Resource;
use App\Models\Sponsor;
use Illuminate\Database\Eloquent\Builder;

class HelperService
{
    public function twoPartDiffInTimePast($date): String
    {
        return str_replace(['a', 'g', 'o'], '', (new Carbon($date))->diffForHumans(['other' => null, 'parts' => 2, 'short' => true]), );
    }

    public function twoPartDiffInTimeToCome($date): String
    {
        return str_replace(['now', 'from', 'o', 'g', 'a'], '', (new Carbon($date))->diffForHumans(['other' => null, 'parts' => 2, 'short' => true]), );
    }
    
    public function twoPartDiffInTimeToCome1($date): String
    {
        return (new Carbon())->diffForHumans($date, ['other' => null, 'parts' => 2, 'short' => true], true);
    }

    public function flagExpired($date)
    {
        return new Carbon() > new Carbon($date);
    }

    public function nccTextTime()
    {
        $start = new CarbonImmutable('08:00:00');
        $end = $start->addHours(12);
        return Carbon::now()->between($start, $end);
    }

    public function displayWard($visit)
    {
        return $visit?->ward . '-Bed' . $visit?->bed_no;
    }

    public function paginateQuery(Builder $query, DataTableQueryParams $params, string $orderBy = 'consulted', string $orderDir = 'desc')
    {
        return $query->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start) / $params->length));
    }

    public function dateFormater($date)
    {
        return $date ? (new Carbon($date))->format('d/m/Y g:ia') : '' ;
    }

    public function isAirtel($number)
    {
        $airtelPrefixes = [
                            '0802', 
                            '0808', 
                            '0812', 
                            '0701', 
                            '0708', 
                            '0901', 
                            '0902', 
                            '0904', 
                            '0907', 
                            '0911',
                            '0912'
                        ];
        foreach ($airtelPrefixes as $prefix) {
            if (str_starts_with($number, $prefix)) {
                return true;
            }
        }

        return false;
    }

    public function shouldNotify($phone, $patient = null, array $extraChecks = []): bool
    {
        // --- GLOBAL RULES (Apply to everyone) ---

        // 1. Basic Phone check
        if (!$phone) return false;

        // 2. NCC Quiet Hours & Airtel Block
        if (!$this->nccTextTime()) {
            return false;
        }

        // 3. Patient Opt-out check (if applicable)
        if ($patient && method_exists($patient, 'canSms') && !$patient->canSms()) {
            return false;
        }

        // --- LOCAL RULES (Specific to this call) ---

        foreach ($extraChecks as $check) {
            if (is_callable($check)) {
                if (!$check()) {
                    return false;
                }
            }
        }

        return true;
    }

    public function biller(Resource $resource, ?Sponsor $sponsor, string|int $quantity, bool|int $approved = false)
    {
        $sponsorCat = $sponsor->category_name;
        $isFamily = $sponsorCat === 'Family';
        $isNhis   = $sponsorCat === 'NHIS';

        $sellingPrice = $resource->getSellingPriceForSponsor($sponsor);

        $bill = (float) $sellingPrice * $quantity;

        $billArray = ['bill' => $bill, 'nhisBill' => $isNhis ? $bill : 0.0];

        $sponsorResourceCat = $sponsor?->resourceCategories
            ->where('id', $resource->resourceSubCategory->resourceCategory->id)
            ->first();

        if ($sponsorResourceCat){
                $percentage = $sponsorResourceCat->pivot->billable_percentage;
                $percentageBill = (float) ($bill * ($percentage / 100));
               
                if ($isNhis) {
                    // For NHIS: use discounted rate if approved, else full price
                    $billArray['nhisBill'] = $approved ? $percentageBill : $bill;
                    
                } elseif ($isFamily) {
                    // For Family: always use the discounted rate
                    $billArray['bill'] = $percentageBill;
                } else {
                    if ($approved){
                        // For Private/Corporate: use discounted rate if approved, else full price
                        $billArray['bill'] = $percentageBill;
                    }
                    
                }
        }

        return $billArray;
    }
}