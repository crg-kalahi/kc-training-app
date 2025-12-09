<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MfaQrCodeNotification extends Notification
{
    use Queueable;

    protected $pdfData;
    protected $secret;
    protected $userName;
    protected $isSetup;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($pdfData, $secret, $userName, $isSetup = true)
    {
        $this->pdfData = $pdfData;
        $this->secret = $secret;
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
            ? 'Two-Factor Authentication Setup - QR Code' 
            : 'Two-Factor Authentication Verification Required';

        $greeting = $this->isSetup
            ? "Hello {$this->userName},"
            : "Hello {$this->userName},";

        $body = $this->isSetup
            ? "Please find your Two-Factor Authentication (2FA) QR code below. Scan this QR code with your authenticator app (such as Google Authenticator) to complete the setup process."
            : "You are required to verify your identity using Two-Factor Authentication. Please use the QR code below or enter the secret code manually in your authenticator app.";

        $message = (new MailMessage)
            ->subject($subject)
            ->view('emails.mfa_qr_code', [
                'secret' => $this->secret,
                'userName' => $this->userName,
                'isSetup' => $this->isSetup,
            ]);
        
        // Attach PDF if available
        if ($this->pdfData) {
            $message->attachData($this->pdfData, 'mfa-qr-code.pdf', [
                'mime' => 'application/pdf',
            ]);
        }
        
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

