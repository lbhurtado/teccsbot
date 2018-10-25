<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Conversations\Conversation;

class TestConversation extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->inputMobile();
    }

    public function inputMobile()
    {

        $question = Question::create('input mobile')
            ;

        $this->ask($question, function (Answer $answer) {
        	$this->bot->reply($answer->getText());

            $this->inputCode();
        });
    }

    public function inputCode()
    {
        $question = Question::create('input code')
            ;

        $this->ask($question, function (Answer $answer) {
        	$this->bot->reply($answer->getText());

            $this->inputPIN();
        });
    }

    public function inputPIN()
    {
        $question = Question::create('input PIN')
            ;

        $this->ask($question, function (Answer $answer) {
        	$this->bot->reply($answer->getText());

        });
    }
}
