<?php

namespace App\Conversations;

use App\{Placement, Phone, Messenger, User};
use App\Jobs\{RequestOTP, VerifyOTP};
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class SignUp extends Conversation
{
    protected $messenger;

    protected $mobile;

    protected $user;

	protected $message;

    public function run()
    {
        $this->touch()->askMobileNumberOfUser();
    }

    protected function askMobileNumberOfUser()
    {
        $question = Question::create("Please enter mobile number.")
            ->fallback('Unable to input mobile.')
            ->callbackId('input_mobile')
            ;

        return $this->ask($question, function (Answer $answer) {

            if ($this->isNotAValidMobileNumber($answer->getText())) 
                return $this->repeat("Please enter mobile number. STOP or <space> to stop.");
            
            
            if ($this->userAlreadyExistsAsPerMobile()) {
                if ($this->getUser()->isVerificationStale()) {

                    return $this->challengeUser();
                }
                else {

                    return $this->bot->reply('You are still valid.');
                }
            }
        	else {

                return $this->askCodeGivenToUser(); 
            }
        });
    }

    protected function askCodeGivenToUser()
    {
        $question = Question::create("Please enter your code.")
            ->fallback('Unable to input code.')
            ->callbackId('input_code')
            ;

        return $this->ask($question, function (Answer $answer) {
            if (! $this->activateUserIfCodeIsValid($answer->getText())) {

                return $this->repeat("Please enter your code. STOP or <space> to stop.");
            }
            $this->associateUserToMessenger();
        	$this->challengeUser();
        });
    }

    protected function challengeUser()
    {
        $this->sendOTP();

        $question = Question::create("Please enter your PIN.")
            ->fallback('Unable to input PIN.')
            ->callbackId('input_pin')
            ;

        return $this->ask($question, function (Answer $answer) {

            return $this->checkOTPSentToMobile($answer->getText());
        });
    }    

    protected function checkOTPSentToMobile($otp)
    {
        $this->verifyOTP($otp);

        if (! $this->getUser()->isVerified()) {

            return $this->challengeUserAgain();
        }

        return $this->bot->reply($this->getMessageToUser());
    }   

    private function touch()
    {
        $this->messenger = Messenger::where([
            'driver' => $this->bot->getDriver()->getName(),
            'channel_id' => $this->bot->getUser()->getId(),
        ])->first();

        return $this;
    }

    private function getMobileAttribute()
    {
        return ['mobile' =>  $this->mobile];
    }

    private function getUser()
    {
        return $this->user;
    }

    private function getMessageToUser()
    {
        return $this->message;
    }

    private function isNotAValidMobileNumber($input)
    {
        return (!$this->mobile = Phone::validate($input));   
    }

    private function userAlreadyExistsAsPerMobile()
    {
        return ($this->user = User::where($this->getMobileAttribute())->first());
    }

    private function activateUserIfCodeIsValid($code)
    {
        optional(Placement::bearing($code)->first(), function($placement) {
            $this->user = $placement->wake($this->getMobileAttribute());
            $this->message = $placement->message;
        });

        return (! empty($this->user));
    }

    private function associateUserToMessenger()
    {
        $this->messenger->user()
            ->associate($this->getUser())
            ->save()
            ;
    }

    private function sendOTP()
    {
        RequestOTP::dispatch($this->getUser());
    }

    private function verifyOTP($otp)
    {
        VerifyOTP::dispatch($this->getUser(), $otp);

        $this->getUser()->refresh();
    }

    private function challengeUserAgain()
    {
        $this->challengeUser();
    }
}
