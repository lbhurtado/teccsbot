<?php

namespace App\Conversations;

use App\Messenger;
use BotMan\BotMan\Messages\Conversations\Conversation;

class BaseConversation extends Conversation
{
    public function run(){}

    protected function getMessenger()
    {
        // return Messenger::instance($this->getDriver(), $this->getChannelId());
        $driver = $this->getDriver();
        $channel_id = $this->getChannelId();
        return Messenger::where(compact('driver', 'channel_id'))->first();
    }

    protected function getUser()
    {
    	return $this->getMessenger()->user;
    }

    protected function getDriver()
    {
    	return $this->bot->getDriver()->getName();
    }

    protected function getChannelId()
    {
    	return $this->bot->getUser()->getId();
    }
}
