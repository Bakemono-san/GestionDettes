<?php

namespace App\Broadcasting;

use App\Models\User;
use App\Notifications\SendSms;
use App\Services\InfoBipService;
use App\Services\TwilioService;
use Google\Cloud\Storage\Notification;

class sms
{
    protected $TwilioService;
    protected $InfoBipService;

    public function __construct(TwilioService $TwilioService,InfoBipService $InfoBipService)
    {
        $this->TwilioService = $TwilioService;
        $this->InfoBipService = $InfoBipService;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \App\Notifications\SendSms $notification
     * @return void
     */
    public function send($notifiable, SendSms $notification)
    {
        if (! method_exists($notification, 'toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);
        $recipient = $notifiable->routeNotificationFor('sms');

        if ($recipient) {
            $this->TwilioService->sendSms($recipient, $message);
            $this->InfoBipService->sendSms($recipient, $message);
        }
    }
}
