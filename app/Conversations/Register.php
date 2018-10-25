<?php

namespace App\Conversations;

use App\{Placement, Phone};
use App\Jobs\{RequestOTP, VerifyOTP};
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class Register extends Conversation
{
	protected $mobile;

	protected $code;

    protected $user;

	protected $pin;

    public function inputMobile()
    {
        $question = Question::create("Please enter mobile number.")
            ->fallback('Unable to input mobile.')
            ->callbackId('input_mobile')
            ;

        return $this->ask($question, function (Answer $answer) {
            if (!$this->mobile = Phone::validate($answer->getText()))
                return $this->repeat("Please enter mobile number. STOP or <space> to stop.");
            
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

            optional(Placement::bearing($this->code)->first(), function($placement) {
                $this->user = $placement->wake(['mobile' => $this->mobile]);
                $this->message = $placement->message;
            });

            // if (!$this->user = Placement::activate($this->code, ['mobile' => $this->mobile]))
            if (!$this->user)
                return $this->repeat("Please enter your code. STOP or <space> to stop.");
            
        	$this->inputPIN();
        });
    }

    public function inputPIN()
    {
        RequestOTP::dispatch($this->user);

        $question = Question::create("Please enter your PIN.")
            ->fallback('Unable to input PIN.')
            ->callbackId('input_pin')
            ;

        return $this->ask($question, function (Answer $answer) {
        	$otp = $answer->getText();

            VerifyOTP::dispatch($this->user, $otp);
        	$this->authenticate();
        });
    }    

    public function authenticate()
    {
        $this->user->refresh();

        if (!$this->user->isVerified())
            return $this->inputPIN();

        return $this->bot->reply('Thank you.');
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
