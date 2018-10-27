<?php

namespace App\Conversations;

use App\{Phone, User, Messenger};
use App\Jobs\VerifyOTP;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Conversations\Conversation;

class Verify extends Conversation
{
    public function run()
    {
    	$this->inputMobile();
    }

    protected function inputMobile()
    {
        $question = Question::create("Please enter mobile number.")
            ->fallback('Unable to input mobile.')
            ->callbackId('input_mobile')
            ;

        $messenger = Messenger::where([
            'driver' => $this->bot->getDriver()->getName(),
            'channel_id' => $this->bot->getUser()->getId(),
        ])->first();

        return $this->ask($question, function (Answer $answer) use ($messenger) {
            if (!$mobile = Phone::validate($answer->getText()))
                $this->repeat();

            $user = User::withMobile($mobile)->first();

            $messenger->user()->associate($user);

            $messenger->save();

            return $this->inputPIN($user);
        });
    }

    protected function inputPIN($user)
    {
        $question = Question::create("Please enter your PIN.")
            ->fallback('Unable to input PIN.')
            ->callbackId('input_pin')
            ;

        return $this->ask($question, function (Answer $answer) use ($user) {
            $otp = $answer->getText();

            return $this->authenticate($user, $otp);
        });   
    }

    protected function authenticate($user, $otp)
    {
        VerifyOTP::dispatch($user, $otp);

        $user->refresh();

        if (! $user->isVerified()) {

            return $this->inputPIN($user);
        }

        $this->bot->reply('Yehey!');
    }
}
