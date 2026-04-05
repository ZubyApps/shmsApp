<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\SmsWalletFunding;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SmsWalletFundingService
{
    public function __construct(private readonly SmsWalletFunding $smsWalletFunding, private readonly SmsWalletService $smsWalletService)
    {
    }

    public function create(Request $data, User $user): SmsWalletFunding
    {
        return $user->smsWalletFundings()->create([
            'amount_paid'           => $data->amount,
            'units_added'           => $data->units,
            'payment_method'        => 'Bank Transfer',
        ]);
    }

    public function getPaginatedSmsWalletFundings(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->smsWalletFunding->select('id', 'user_id', 'amount_paid', 'units_added', 'payment_method', 'status', 'created_at')
                        ->with(['user:id,username']);

        if (! empty($params->searchTerm)) {
            $query->where('payment_method', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (SmsWalletFunding $smsWalletFunding) {
            return [
                'id'            => $smsWalletFunding->id,
                'payMethod'     => $smsWalletFunding->payment_method,
                'amount'        => $smsWalletFunding->amount_paid,
                'units'         => $smsWalletFunding->units_added,
                'status'        => $smsWalletFunding->status,
                'statusLabel'   => $smsWalletFunding->status_label,
                'createdBy'     => $smsWalletFunding->user->username,
                'createdAt'     => (new Carbon($smsWalletFunding->created_at))->format('d/m/Y g:ia'),
                'admin'         => auth()->user()->designation->access_level > 5,
            ];
         };
    }

    public function updatePaymentStatus($request, SmsWalletFunding $smsWalletFunding, User $user)
    {
        
         $status = match ($request->paymentStatus) {
                'pending' => SmsWalletFunding::STATUS_PENDING,
                'paid'    => SmsWalletFunding::STATUS_PAID,
                'failed'  => SmsWalletFunding::STATUS_FAILED,
                default   => SmsWalletFunding::STATUS_PENDING, // Fallback safety
            };

        if ($status === SmsWalletFunding::STATUS_PAID){
            $transaction = $this->smsWalletService->creditWithFunding($smsWalletFunding, $user->username);
            if (!$transaction){
                return response()->json(['error' => [
                    "message" => "Wallet not credited, please refresh, check and try again"
                ]], 400);
            }
            return response()->json(["message" => 
            "Wallet has been credited with {$transaction->amount} successfully. New wallet balance is {$transaction->running_balance}"
            ], 200);  
        }

        $smsWalletFunding->update([
                'status'      => $status,
                'approved_by' => $user->username
            ]);
        
        return response()->json(["message" => 
            "Status has been updated successfully. Wallet balance is same"
            ], 200);
        
    }
}
