<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Broadcasting\MessengerChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Facebook\FacebookChannel;
use NotificationChannels\Facebook\FacebookMessage;
use NotificationChannels\Facebook\Component\Button;
use NotificationChannels\Facebook\Enums\NotificationType;

class OnDemand extends Notification
{
    use Queueable;

    protected $content;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        switch ($notifiable->getDefaultRoute()) {
            case 'Telegram':
                $driver = TelegramChannel::class;
                break;
            case 'Facebook':
                $driver = FacebookChannel::class;
                $break;            
        }

        return [$driver];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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

    public function toTelegram($notifiable)
    {
        return TelegramMessage::create()
            ->content($this->content)
            ; 
    }

    public function toFacebook($notifiable)
    {
        return FacebookMessage::create()
            ->text($this->content)
            ->notificationType(NotificationType::REGULAR)
            ;
    }
}
