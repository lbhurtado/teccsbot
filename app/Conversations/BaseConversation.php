<?php

namespace App\Conversations;

use App\Messenger;
use BotMan\BotMan\Messages\Conversations\Conversation;

class BaseConversation extends Conversation
{
    public function run(){}

    protected function getMessenger()
    {
        return Messenger::instance($this->bot->getDriver()->getName(),$this->bot->getUser()->getId());
    }

    protected function getUser()
    {
    	return $this->getMessenger()->user;
    }

}
