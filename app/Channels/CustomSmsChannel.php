<?php

declare(strict_types = 1);

namespace App\Channels;

use App\Models\Communication;
use App\Models\ServiceRate;
use App\Models\UnitTransaction;
use App\Services\SmsWalletService;
use App\Contracts\SmsProviderInterface;
use App\Contracts\CustomSmsNotificationInterface;
use App\Enum\CommunicationType;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CustomSmsChannel
{
    public function __construct(
        protected SmsWalletService $walletService
    ) {}

    public function send($notifiable, Notification $notification): void
    {
        // 1. Validate the Notification implements our interface
        if (!$notification instanceof CustomSmsNotificationInterface) {
            return;
        }

        $data = $notification->toCustomSms($notifiable);

        if (empty($data)) {
            return;
        }

        // 2. Resolve Network and Rate
        $network = $this->getNetwork($data['to']);
        $rate = ServiceRate::where('identifier', $network)->first() 
                ?? ServiceRate::where('identifier', 'other')->first();

                
        $cost = $rate ? (float) $rate->unit_cost : 4.00; // Fallback to 4 units
                
        // 3. Check Global Balance (Hospital Wallet)
        $currentBalance = (float) UnitTransaction::latest('id')->value('running_balance') ?? 0;

        Log::info('current Balance', ['notifiable' => $notifiable, 'current balance' => $currentBalance]);

        if ($currentBalance < $cost) {
            Log::warning("SMS Failed: Insufficient Hospital Balance. Cost: {$cost}, Balance: {$currentBalance}");
            Communication::create([
                        'recipient_name'    => $data['name'],
                        'recipient_contact' => $data['to'],
                        'type_id'           => CommunicationType::SMS,
                        'network'           => $network,
                        'message_type'      => $data['messageType'],
                        'units_deducted'    => 0.00,
                        'message'           => $data['message'],
                        'status'            => 'Failed (Insufficient balance)',
                    ]);
            return; 
        }

        // 4. Resolve the Provider (ChurchPlus, etc.)
        $providerKey = config('services.sms.default');
        $providerClass = config("services.sms.providers.{$providerKey}");
        
        /** @var SmsProviderInterface $provider */
        $provider = new $providerClass;
        
        // 5. Execute the Send
        $sent = $provider->send($data['to'], $data['message']);
        
        if ($sent) {
            // 6. Create Communication Log (Snapshotting the cost)
            $log = Communication::create([
                'recipient_name'    => $data['name'],
                'recipient_contact' => $data['to'],
                'type_id'           => CommunicationType::SMS,
                'network'           => $network,
                'message_type'      => $data['messageType'],
                'units_deducted'    => $cost,
                'message'           => $data['message'],
                'status'            => 'sent',
            ]);

            // 7. Deduct from Global Ledger
            $this->walletService->deductForSms($cost, (string) $log->id);
        } else {
            Log::error("SMS Gateway Error for number: " . $data['to']);
        }
    }

    protected function getNetwork(string $phone): string 
    {
        // 1. Clean the string (remove +, spaces, dashes, etc.)
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // 2. Extract the last 10 digits 
        // Example: 2348031234567 -> 8031234567
        // Example: 08031234567   -> 8031234567
        $last10 = substr($clean, -10);

        // 3. Ensure we actually have 10 digits before proceeding
        if (strlen($last10) < 10) {
            return 'other';
        }

        // 4. Create the 4-digit prefix by adding back the leading '0'
        // 8031... -> 0803
        $prefix = '0' . substr($last10, 0, 3);

        $map = [
            'mtn'     => ['0803', '0806', '0813', '0816', '0810', '0814', '0903', '0906', '0703', '0706', '0913', '0916'],
            'airtel'  => ['0802', '0808', '0812', '0701', '0708', '0902', '0907', '0901', '0904', '0912'],
            'glo'     => ['0805', '0807', '0811', '0815', '0705', '0905', '0915'],
            '9mobile' => ['0809', '0817', '0818', '0908', '0909'],
        ];

        foreach ($map as $network => $prefixes) {
            if (in_array($prefix, $prefixes)) {
                return $network;
            }
        }

        return 'other';
    }
}