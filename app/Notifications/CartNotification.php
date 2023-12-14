<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CartNotification extends Notification
{
    use Queueable;
private $notificationData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notificationData)
    {
        $this->notificationData = $notificationData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // return ['mail','database'];
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = env('APP_URL') . '/cart';
        str_replace('//', '/', $url);

        return (new MailMessage)
            ->greeting("Hi " . $this->notificationData['name'])
            ->line($this->notificationData['body'])
            ->action('Go to Cart', $url)
            ->line($this->notificationData['thanks']);
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
            'body' => $this->notificationData['body']
        ];
    }
}
