<?php

namespace App\Conversations;

use App\Messenger;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class Onboarding extends BaseConversation
{
    public function run()
    {
        $this->welcome()->askToStayUpdated();
    }

    protected function welcome()
    {
        $this->bot->reply(trans('onboarding.welcome', ['name' => config('app.name')]));

        return $this;
    }

    protected function askToStayUpdated()
    {
        $question = Question::create(trans('onboarding.stay_updated.question', [
            'name' => config('app.name')
        ]))
        ->fallback(trans('onboarding.stay_updated.error'))
        ->callbackId('onboarding.stay_updated')
        ->addButtons([
            Button::create(trans('onboarding.stay_updated.input.yes'))->value('yes'),
            Button::create(trans('onboarding.stay_updated.input.no'))->value('no')
        ]);

        $this->ask($question, function (Answer $answer) {
            switch ($answer->getText()) {
                case 'no':
                    // Subscriber::deleteUserIfGiven($this->bot->getUser()->getId());
                    $this->say(trans('onboarding.stay_updated.answer.no'));

                    return $this->showInfo();
                case 'yes':
                    // Subscriber::storeFromBotManUser($this->bot->getDriver()->getName(), $this->bot->getUser());
                    $this->getMessenger()->turnOnNotifications();
                    $this->say(trans('onboarding.stay_updated.answer.yes'));

                    return $this->showInfo();
                default:
                    $this->say(trans('onboarding.stay_updated.answer.duh'));

                    return $this->repeat();
            }
        });
    }

    protected function showInfo()
    {
        $this->say(trans('onboarding.info'));
    }
}
