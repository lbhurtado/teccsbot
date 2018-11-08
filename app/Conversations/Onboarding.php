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
        $question = Question::create(trans('onboarding.stay_updated', [
            'name' => config('app.name')
        ]))
        ->fallback(trans('onboarding.stay_updated.error'))
        ->callbackId('onboarding.sentinel.stay_updated')
        ->addButtons([
            Button::create(trans('onboarding.input.yes'))->value('yes'),
            Button::create(trans('onboarding.input.no'))->value('no')
        ]);

        $this->ask($question, function (Answer $answer) {
            switch ($answer->getText()) {
                case 'no':
                    // Subscriber::deleteUserIfGiven($this->bot->getUser()->getId());
                    $this->say('Ok, no problem.');

                    return $this->showGeneralInfo();
                case 'yes':
                    // Subscriber::storeFromBotManUser($this->bot->getDriver()->getName(), $this->bot->getUser());
                    $this->getMessenger()->turnOnNotifications();
                    $this->say('Perfect 👍');

                    return $this->showGeneralInfo();
                default:
                    $this->say('I am not sure what you meant. Can you try again?');

                    return $this->repeat();
            }
        });
    }

    protected function showGeneralInfo()
    {
        $this->say("These are some examples sentences that you can use:\n
        - \"Show me the speakers.\"
        - \"Who is sponsoring this year?\"
        ");
    }
}
