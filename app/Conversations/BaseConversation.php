<?php

namespace App\Conversations;

use App\Messenger;
use BotMan\BotMan\Messages\Conversations\Conversation;

class BaseConversation extends Conversation
{
    public function run(){}

    protected function getMessenger()
    {
        return Messenger::instance($this->getDriver(), $this->getChannelId());
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
