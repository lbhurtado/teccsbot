<?php

namespace App\Conversations;

use App\Messenger;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
// use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class Onboarding extends Conversation
{
    public function run()
    {
        $this->provideGreeting();
    }

    public function provideGreeting()
    {
        $this->bot->reply(trans('onboarding.welcome', ['name' => config('app.name')]));
        sleep(2);

        $this->askToStayUpdated();
    }

    public function askToStayUpdated()
    {
        $question = Question::create(trans('onboarding.stay_updated', ['name' => config('app.name')]));
        // $question->addButton(Button::create('Yes'));
        // $question->addButton(Button::create('No'));

        $messenger = Messenger::where([
            'driver' => $this->bot->getDriver()->getName(),
            'channel_id' => $this->bot->getUser()->getId(),
        ])->first();

        $this->ask($question, function (Answer $answer) use ($messenger) {
            if ($answer->getText() === 'Yes') {
            	$messenger->turnOnNotifications();
            } 
        });
    }
}
