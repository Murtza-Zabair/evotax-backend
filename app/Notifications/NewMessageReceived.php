<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewMessageReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Message Received from ' . $this->message->name)
            ->greeting('Hello!')
            ->line('You have received a new message from your website.')
            ->line('**Name:** ' . $this->message->name)
            ->line('**Email:** ' . $this->message->email)
            ->line('**Phone:** ' . ($this->message->phone ?? 'Not provided'))
            ->line('**Message:**')
            ->line($this->message->message)
            ->line('Thank you for using our application!');
    }
}
