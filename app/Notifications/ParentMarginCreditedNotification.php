<?php

namespace App\Notifications;

use App\Enums\NotificationPreference;
use App\Models\AgentMarginLog;
use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ParentMarginCreditedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Application $application,
        public AgentMarginLog $marginLog
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
        $subAgentName = $this->application->subAgent ? $this->application->subAgent->name : 'Team Member';

        return (new MailMessage)
            ->subject('🎉 Extra Margin Accrued - ₹'.number_format((float) $this->marginLog->margin_amount, 2).' for Application #'.$this->application->id)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your team member **'.$subAgentName.'** has submitted an application with payment confirmed.')
            ->line('**Application ID:** #'.$this->application->id)
            ->line('**Service:** '.$this->application->service->name)
            ->line('**Amount Paid by Sub-Agent:** ₹'.number_format((float) $this->marginLog->sub_agent_paid, 2))
            ->line('**Company Share:** ₹'.number_format((float) $this->marginLog->company_retained, 2))
            ->line('**Your Extra Margin (Profit):** ₹'.number_format((float) $this->marginLog->margin_amount, 2))
            ->action('View Margin Earnings', route('agent.margin-ledger.index'))
            ->line('This extra margin has been recorded as accrued and will be disbursed in your next payout.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'parent_margin_accrued',
            'application_id' => $this->application->id,
            'sub_agent_id' => $this->application->sub_agent_id,
            'sub_agent_name' => $this->application->subAgent?->name ?? 'Team Member',
            'margin_amount' => (float) $this->marginLog->margin_amount,
            'service_name' => $this->application->service?->name ?? 'Service',
            'message' => 'Extra margin of ₹'.number_format((float) $this->marginLog->margin_amount, 2).' accrued for App #'.$this->application->id,
        ];
    }
}
