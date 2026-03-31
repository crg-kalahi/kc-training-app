<?php

namespace App\Notifications;

use App\Models\Training;
use App\Models\TrainingParticipant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TrainingParticipantRegistered extends Notification
{
    use Queueable;

    public function __construct(
        public TrainingParticipant $participant,
        public Training $training
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $from = Carbon::parse($this->training->date_from)->format('F j, Y');
        $to = Carbon::parse($this->training->date_to)->format('F j, Y');
        $dates = $from === $to ? $from : "{$from} – {$to}";

        $first = Str::title($this->participant->fname);

        return (new MailMessage)
            ->subject(__('Training registration confirmed: :title', ['title' => $this->training->title]))
            ->greeting(__('Hello :name,', ['name' => $first]))
            ->line(__('Thank you for registering. Your details have been recorded for the following training:'))
            ->line('**'.$this->training->title.'**')
            ->line('**'.__('When').':** '.$dates)
            ->line('**'.__('Where').':** '.$this->training->venue)
            ->line(__('Please keep this email for your records. If you need to update your information, contact the training organizer.'))
            ->salutation(__('Best regards'));
    }
}
