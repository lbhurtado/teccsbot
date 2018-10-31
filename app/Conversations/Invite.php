<?php

namespace App\Conversations;

use App\{User, Placement, Phone, Messenger};
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Conversations\Conversation;

class Invite extends Conversation
{
	private $code;

	private $mobile;

	private $messenger;

    public function run()
    {
        $this->messenger = Messenger::where([
            'driver' => $this->bot->getDriver()->getName(),
            'channel_id' => $this->bot->getUser()->getId(),
        ])->first();

        $this->introduction()->inputCode();
    }

    protected function introduction()
    {
    	$this->bot->reply(trans('invite.introduction'));

    	return $this;
    }

    protected function inputCode()
    {
        $question = Question::create(trans('invite.input.code'));

        $this->ask($question, function (Answer $answer) {
        	if (array_key_exists($this->code = strtolower($answer->getText()), User::$classes)) {
        		if (! $this->messenger->user->hasPermissionTo("seed {$this->code}")) {
        			$this->bot->reply(trans('invite.error.permission'));
        			return $this->repeat(trans('invite.input.code'));
        		}
        	}
        	else
        		return $this->repeat(trans('invite.input.code'));

            $this->inputMobile();
        });
    }

    protected function inputMobile()
    {
        $question = Question::create(trans('invite.input.mobile'));

        $this->ask($question, function (Answer $answer) {
        	if ($this->mobile = Phone::validate($answer->getText())) {
        		return $this->verify();
        	}

            return $this->repeat(trans('invite.input.mobile'));
        });
    }

    protected function verify()
    {
        $question = Question::create(trans('invite.input.verify', [
        	'code' => $this->code,
        	'mobile' => $this->mobile
        ]));

        $this->ask($question, function (Answer $answer) {
        	if ($answer->getText() == 'No') {
            	$this->inputCode();
        	}
        	$this->process();
        });
    }

    protected function process()
    {
    	if ($user = User::seed($this->code, $this->mobile)) {
    		$user->invite();	
    	}
    	
    }
}
