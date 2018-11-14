<?php

namespace App\Conversations;

use App\Enum\Role;
use App\{User, Phone};
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class Invite extends BaseConversation
{
	protected $code;

	protected $mobile;

    protected $codes;

    public function run()
    {
        $this->introduction()->inputCode();
    }

    protected function introduction()
    {
    	$this->bot->reply(trans('invite.introduction'));

    	return $this;
    }

    protected function inputCode()
    {
        $question = Question::create(trans('invite.input.code'))
        ->fallback(trans('invite.code.error'))
        ->callbackId('invite_code')
        ;

        foreach ($this->codes as $code) {
            $question->addButton(Button::create(ucfirst($code))->value($code));
        }

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->code = $answer->getValue();
                if (! $this->checkPermission()) {

                    return $this->repeat(trans('invite.error.permission'));  
                } 

                return $this->inputMobile();
            }
            else 
                return $this->repeat();
        });
    }

    protected function inputMobile()
    {
        $question = Question::create(trans('invite.input.mobile', ['code' => $this->code]))
        ->fallback(trans('invite.mobile.error'))
        ->callbackId('invite_mobile')
        ;

        $this->ask($question, function (Answer $answer) {
        	if (! $this->mobile = $this->checkMobile($answer->getText())) {

                return $this->repeat(trans('invite.input.mobile'));
        	}

            return $this->verify();
        });
    }

    protected function verify()
    {
        $question = Question::create(trans('invite.input.verify', [
        	'code' => $this->getCode(),
        	'mobile' => $this->getMobile()
        ]))
        ->fallback(trans('invite.verify.error'))
        ->callbackId('invite_verify')
        ->addButtons([
            Button::create(trans('invite.input.telegram'))->value('Telegram'),
            Button::create(trans('invite.input.facebook'))->value('Facebook'),
            Button::create(trans('invite.input.no'))->value('No')
        ]);

        $this->ask($question, function (Answer $answer) {
            $driver = 'Facebook';

            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() == 'No') {

                    return $this->inputCode();
                }

                $driver = $answer->getValue();
            }      

            return $this->process($driver);
        });
    }

    protected function process($driver)
    {
        $this->bot->reply(trans('invite.processing'));

        //change seed to invite in the future
    	if ($user = User::seed($this->getCode(), $this->getMobile(), $this->getUser())) {
    		$user->invite($driver);
            $this->getMessenger()->setStatus('invited', trans('invite.reason'));
            $this->bot->reply(trans('invite.sent'));
    	}
        else
            $this->bot->reply(trans('invite.fail'));   
    	
    }

    protected function checkPermission()
    {
        return true;
    }

    protected function getCode()
    {
        return $this->code;
    }

    protected function checkMobile($mobile)
    {
        return Phone::validate($mobile);
    }

    protected function getMobile()
    {
        return $this->mobile;
    }

    public function setBot(BotMan $bot)
    {
        parent::setBot($bot);

        $this->codes = array_values(Role::toArray());
    }
}
