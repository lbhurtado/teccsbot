<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;
use Illuminate\Notifications\Messages\MailMessage;

class UserInvitation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TwilioChannel::class];
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return NexmoMessage
     */
    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
                    ->content('Your SMS message content');
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
        $messenger = $notifiable->getDefaultMessenger();

        return [
            'driver' => $messenger->driver,
            'channel_id' => $messenger->channel_id,
            'url' => $this->getURL(),
        ];
    }

    public function toTwilio($notifiable)
    {
        $url = $this->getURL($notifiable);
        $name = $notifiable->parent->name;

        return (new TwilioSmsMessage())
            ->content(trans('invite.notification', compact('url', 'name')))
            // ->from('+13104992907')
            // ->from('MG6cfe25a8cfc5287e5a66055556bfe930')
            ;
    }

    protected function getURL($notifiable)
    {
        return (optional($notifiable->getDefaultMessenger())->driver == 'Telegram') 
                ? 'http://t.me/grassroots_bot'
                : 'http://m.me/dyagwarbot';
    }
}
