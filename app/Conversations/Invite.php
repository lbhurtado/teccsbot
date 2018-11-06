<?php

namespace App\Conversations;

use App\Enum\Role;
use App\{User, Placement, Phone, Messenger};
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class Invite extends Conversation
{
	protected $code;

	protected $mobile;

	protected $messenger;

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
        $question = Question::create(trans('invite.input.code'))
        ->fallback(trans('invite.code.error'))
        ->callbackId('invite_code')
        ;

        $this->ask($question, function (Answer $answer) {
            if (! $this->code = $this->checkCode($answer->getText())) {
            
                return $this->repeat(trans('invite.error.code'));                
            }

            if (! $this->checkPermission()) {

                return $this->repeat(trans('invite.error.permission'));  
            }             

            return $this->inputMobile();
        });
    }

    protected function inputMobile()
    {
        $question = Question::create(trans('invite.input.mobile'))
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

    	if ($user = User::seed($this->getCode(), $this->getMobile(), $this->getUser())) {
    		$user->invite($driver);
            $this->bot->reply(trans('invite.sent'));
    	}
        else
            $this->bot->reply(trans('invite.fail'));   
    	
    }

    protected function checkCode($code)
    {
        $code = strtolower($code);
        if (! in_array($code, array_values(Role::toArray()))) {

            return false;
        }

        return $code;
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

    protected function getUser()
    {
        return $this->messenger->user;
    }
}
