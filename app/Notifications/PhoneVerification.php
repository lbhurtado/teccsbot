<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Authy\AuthyChannel;
use NotificationChannels\Authy\AuthyMessage;
use Illuminate\Notifications\Messages\MailMessage;

class PhoneVerification extends Notification
{
    use Queueable;

    /**
     * The notification method (sms/call).
     *
     * @var string
     */
    public $method;

    /**
     * Determine whether to force the notification over cellphone network.
     *
     * @var bool
     */
    public $force;

    /**
     * The notification message action.
     *
     * @var string
     */
    public $action;

    /**
     * The notification message action message.
     *
     * @var string
     */
    public $actionMessage;

    /**
     * Create a notification instance.
     *
     * @param string $method
     * @param bool   $force
     * @param string $action
     * @param string $actionMessage
     *
     * @return void
     */
    public function __construct($method = 'sms', $force = false, $action = null, $actionMessage = null)
    {
        $this->method = $method;
        $this->force = $force;
        $this->action = $action;
        $this->actionMessage = $actionMessage;
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     *
     * @return array|string
     */
    public function via($notifiable)
    {
        return [AuthyChannel::class];
    }

    /**
     * Build the Authy representation of the notification.
     *
     * @return \NotificationChannels\Authy\AuthyMessage
     */
    public function toAuthy()
    {
        $message = AuthyMessage::create()->method($this->method);

        if ($this->force) {
            $message->force();
        }

        if ($this->action) {
            $message->action($this->action);
        }

        if ($this->actionMessage) {
            $message->actionMessage($this->actionMessage);
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
