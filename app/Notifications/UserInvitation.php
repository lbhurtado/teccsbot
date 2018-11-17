<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;
use Illuminate\Notifications\Messages\MailMessage;
use App\Channels\{TelerivetChannel, TelerivetMessage};

class UserInvitation extends Notification
{
    use Queueable;

    protected $driver;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['nexmo'];
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
                    ->content($this->getContent($notifiable));
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
        $name = $notifiable->parent->name;
        $url = $this->getURL($notifiable);

        return (new TwilioSmsMessage())
            ->content(trans('invite.notification', compact('name', 'url')))
            // ->from('+13104992907')
            // ->from('MG6cfe25a8cfc5287e5a66055556bfe930')
            ;
    }

    public function toTelerivet($notifiable)
    {
        return TelerivetMessage::create()
            ->content($this->getContent($notifiable))
            ;
    }

    protected function getContent($notifiable)
    {
        $name = $notifiable->parent->name;
        $url = $this->getURL($notifiable);

        return trans('invite.notification', compact('name', 'url'));
    }

    protected function getURL($notifiable)
    {
        return config('chatbot.links.messenger')[in_array($this->driver, ['Telegram', 'Facebook']) ? $this->driver : 'Facebook'];

        // return (optional($notifiable->getDefaultMessenger())->driver == 'Telegram') 
        //         ? 'http://t.me/grassroots_bot'
        //         : 'http://m.me/dyagwarbot';
    }
}
