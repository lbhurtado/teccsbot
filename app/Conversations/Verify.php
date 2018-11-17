<?php

namespace App\Conversations;

use App\{Phone, User, Messenger};
use App\Jobs\VerifyOTP;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class Verify extends BaseConversation
{
    public function run()
    {
        $messenger = $this->getMessenger();
        $messenger->update([
            'first_name' => $this->bot->getUser()->getFirstName(),
            'last_name' => $this->bot->getUser()->getLastName()
        ]);

        $messenger->save();

        $this->introduction()->inputName($messenger);
    }

    protected function introduction()
    {
        $this->bot->reply(trans('verify.introduction'));

        return $this;
    }

    protected function inputName($messenger)
    {
        $name = $messenger->name;

        return $this->inputMobile($messenger, $name);     
    }

    protected function inputMobile($messenger, $name)
    {
        $question = Question::create(trans('verify.input.mobile'))
            ->fallback(trans('verify.mobile.error'))
            ->callbackId('verify.input.mobile')
            ;

        return $this->ask($question, function (Answer $answer) use ($messenger, $name) {
            if (!$mobile = $this->checkMobile($answer->getText()))
                return $this->repeat(trans('verify.input.mobile'));

            return $this->verify($messenger, $name, $mobile);
        });
    }

    protected function verify($messenger, $name, $mobile)
    {
        $question = Question::create(trans('verify.input.verify', [
            'name' => $name,
            'mobile' => $mobile
        ]))
        ->fallback(trans('verify.verify.error'))
        ->callbackId('verify.input.verify')
        ->addButtons([
            Button::create(trans('verify.input.yes'))->value('Yes'),
            Button::create(trans('verify.input.no'))->value('No')
        ]);

        return $this->ask($question, function (Answer $answer) use ($messenger, $name, $mobile) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() == 'No') {

                    return $this->inputMobile($messenger, $name);
                }
            }      

            $user = $this->associateMessengerFromMobile($messenger, $name, $mobile);

            $user->challenge();

            return $this->inputPIN($user);
        });
    }

    protected function inputPIN($user)
    {        
        $question = Question::create(trans('verify.input.pin'))
            ->fallback(trans('verify.pin.error'))
            ->callbackId('verify.input.pin')
            ;

        return $this->ask($question, function (Answer $answer) use ($user) {
            $otp = $answer->getText();

            return $this->process($user, $otp);
        });   
    }

    protected function process($user, $otp)
    {
        $user->verify($otp)->refresh();

        if (! $user->isVerified()) {
            $this->bot->reply(trans('verify.fail'));

            return $this->inputPIN($user);
        }
        
        $user->parent->accepted($user); //change this to $user->accept();

        $this->getMessenger()->setStatus('accepted', trans('verify.reason'));

        $user->loadCredits();

        $this->bot->reply(trans('verify.success'));
        $this->bot->reply(trans('verify.continue'));
    }

    protected function checkMobile($mobile)
    {
        return Phone::validate($mobile);
    }

    protected function associateMessengerFromMobile($messenger, $name, $mobile)
    {
        $user = User::withMobile($mobile)->first();
        $user->name = $name;
        $user->save();

        $messenger->user()->associate($user);
        $messenger->save();

        return $user;
    }
}
