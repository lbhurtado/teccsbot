<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class Register extends Conversation
{
	protected $mobile;

	protected $code;

	protected $pin;

    public function inputMobile()
    {
        $question = Question::create("Please enter mobile number.")
            ->fallback('Unable to input mobile.')
            ->callbackId('input_mobile')
            ;

        return $this->ask($question, function (Answer $answer) {
        	$this->mobile = $answer->getText();
        	$this->inputCode();
        });
    }

    public function inputCode()
    {
        $question = Question::create("Please enter your code.")
            ->fallback('Unable to input code.')
            ->callbackId('input_code')
            ;

        return $this->ask($question, function (Answer $answer) {
        	$this->code = $answer->getText();
        	$this->inputPIN();
        });
    }

    public function inputPIN()
    {
        $question = Question::create("Please enter your PIN.")
            ->fallback('Unable to input PIN.')
            ->callbackId('input_pin')
            ;

        return $this->ask($question, function (Answer $answer) {
        	$this->code = $answer->getText();
        	$this->authenticate();
        });
    }    

    public function authenticate()
    {
    	$this->say('Thank you.');
    }     

    public function run()
    {
        $this->inputMobile();
    }

    public function getMobile()
    {
    	return $this->mobile;
    }
}
