<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MfaOtpNotification extends Notification
{
    use Queueable;

    protected $otp;
    protected $userName;
    protected $isSetup;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($otp, $userName, $isSetup = true)
    {
        $this->otp = $otp;
        $this->userName = $userName;
        $this->isSetup = $isSetup;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $subject = $this->isSetup 
            ? 'Two-Factor Authentication Setup - Verification Code' 
            : 'Two-Factor Authentication Verification Code';

        $greeting = "Hello {$this->userName},";

        $body = $this->isSetup
            ? "Your verification code for setting up Two-Factor Authentication (2FA) is:"
            : "Your TRAIN-ABCD verification code for login is:";

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($body)
            ->line("**{$this->otp}**")
            ->line('This code will expire in 10 minutes.')
            ->line('If you did not request this code, please ignore this email.');
        
        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

