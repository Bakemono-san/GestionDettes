<?php

namespace App\Notifications;

use App\Broadcasting\sms;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Contracts\SmsService;
use App\Services\InfoBipService;
use App\Services\TwilioService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSms extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $recipient;
    protected $type;

    /**
     * Create a new notification instance.
     *
     * @param string $recipient
     * @param string $message
     */
    public function __construct($recipient, $message,$type)
    {
        $this->recipient = $recipient;
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [sms::class,'database'];
    }

    /**
     * Send SMS via the specified service.
     *
     * @param mixed $notifiable
     * @return void
     */
    public function toSms($notifiable)
    {
        // Assuming SmsService is injected using Laravel's Service Container
        app(InfoBipService::class)->sendSms($this->recipient, $this->message);
        app(TwilioService::class)->sendSms($this->recipient, $this->message);
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'recipient' => $this->recipient,
            'type' => $this->type,
        ];
    }
}
