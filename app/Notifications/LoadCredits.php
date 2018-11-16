<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Channels\{TelerivetChannel, TelerivetMessage};

class LoadCredits extends Notification
{
    use Queueable;

    protected $content;

    public function __construct()
    {
        $this->content = "Yes yes yo!";
    }

    public function via($notifiable)
    {
        return [TelerivetChannel::class];
    }

    public function toTelerivet($notifiable)
    {
        return TelerivetMessage::create()
            ->content($this->content)
            ->load(true)
            ;
    }
}
