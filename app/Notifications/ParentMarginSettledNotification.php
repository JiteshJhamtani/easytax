<?php

namespace App\Notifications;

use App\Enums\NotificationPreference;
use App\Models\AgentMarginPayout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ParentMarginSettledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public AgentMarginPayout $payout,
        public int $itemsCount = 1
    ) {}

    public function via(object $notifiable): array
    {
        $preference = $notifiable->notification_preference ?? NotificationPreference::ON;

        return match ($preference) {
            NotificationPreference::ON => ['mail', 'database'],
            NotificationPreference::SILENT => ['database'],
            NotificationPreference::OFF => [],
        };
    }

    public function toMail(object $notifiable): MailMessage
    {
        $methodLabel = match ($this->payout->payment_method) {
            'bank_transfer' => 'Bank Transfer (NEFT/RTGS/IMPS)',
            'upi' => 'UPI Transfer',
            'cheque' => 'Cheque',
            default => ucfirst(str_replace('_', ' ', $this->payout->payment_method)),
        };

        return (new MailMessage)
            ->subject('💰 Margin Payout Processed - ₹'.number_format((float) $this->payout->amount, 2).' (Voucher #'.$this->payout->payout_number.')')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your team extra margin payout has been disbursed to your account.')
            ->line('**Voucher No:** '.$this->payout->payout_number)
            ->line('**Amount Disbursed:** ₹'.number_format((float) $this->payout->amount, 2))
            ->line('**Payment Mode:** '.$methodLabel)
            ->line('**Transaction Ref / UTR:** '.$this->payout->transaction_reference)
            ->line('**Payment Date:** '.$this->payout->payment_date->format('d M Y'))
            ->line('**Applications Settled:** '.$this->itemsCount)
            ->action('View Payout History', route('agent.margin-ledger.index'))
            ->line('Thank you for partnering with EasyTax!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'parent_margin_settled',
            'payout_id' => $this->payout->id,
            'payout_number' => $this->payout->payout_number,
            'amount' => (float) $this->payout->amount,
            'transaction_reference' => $this->payout->transaction_reference,
            'payment_date' => $this->payout->payment_date->format('Y-m-d'),
            'message' => 'Margin Payout of ₹'.number_format((float) $this->payout->amount, 2).' processed (UTR: '.$this->payout->transaction_reference.')',
        ];
    }
}
