<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class Status extends BaseConversation
{
	protected $name;

	protected $reason;

    public function run()
    {
        $this->introduction()->inputName();
    }

    protected function introduction()
    {
    	$this->bot->reply(trans('status.introduction'));

    	return $this;
    }

    protected function inputName()
    {
        $question = Question::create(trans('status.input.name'))
        ->fallback(trans('status.name.error'))
        ->callbackId('status_name')
        ;

        $this->ask($question, function (Answer $answer) {
        	if (! $this->name = $this->checkName($answer->getText()))
        		$this->repeat(trans('status.input.name'));
      
            return $this->inputReason();   
        });
    }

    protected function inputReason()
    {
        $question = Question::create(trans('status.input.reason'))
        ->fallback(trans('status.reason.error'))
        ->callbackId('status_reason')
        ;

        $this->ask($question, function (Answer $answer) {
        	if (! $this->reason = $this->checkReason($answer->getText()))
        		$this->repeat(trans('status.input.reason'));

            return $this->process();
        });
    	
    }

    protected function process()
    {
    	$this->getMessenger()->user->setStatus($this->name, $this->reason);

    	return $this->bot->reply(trans('status.set', [
    		'name' => $this->name,
    		'reason' => $this->reason,
    	]));
    }

    protected function checkName($value)
    {
    	return $value ?? false;
    }

    protected function checkReason($value)
    {
    	return $value ?? false;
    }
}
