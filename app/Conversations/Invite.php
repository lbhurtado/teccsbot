<?php

namespace App\Conversations;

use App\Enum\Role;
use App\{User, Phone};
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer as BotManAnswer;
use BotMan\BotMan\Messages\Outgoing\Question as BotManQuestion;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use App\{Question, Answer};

class Invite extends BaseConversation
{
	protected $code;

	protected $mobile;

    protected $codes;

    /** @var Question */
    protected $quizQuestions;

    /** @var integer */
    protected $userPoints = 0;

    /** @var integer */
    protected $userCorrectAnswers = 0;

    /** @var integer */
    protected $questionCount = 0; // we already had this one

    /** @var integer */
    protected $currentQuestion = 1;

    public function run()
    {
        $this->introduction()->inputCode();

        // $this->introduction()->survey();
    }

    protected function introduction()
    {
    	$this->bot->reply(trans('invite.introduction'));

    	return $this;
    }

    protected function inputCode()
    {
        $question = BotManQuestion::create(trans('invite.input.code'))
        ->fallback(trans('invite.code.error'))
        ->callbackId('invite_code')
        ;

        foreach ($this->codes as $code) {
            $question->addButton(Button::create(ucfirst($code))->value($code));
        }

        return $this->ask($question, function (BotManAnswer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->code = $answer->getValue();
                if (! $this->checkPermission()) {

                    return $this->repeat(trans('invite.error.permission'));  
                } 

                return $this->inputMobile();
            }
            else 
                return $this->repeat();
        });
    }

    protected function inputMobile()
    {
        $question = BotManQuestion::create(trans('invite.input.mobile', ['code' => $this->code]))
        ->fallback(trans('invite.mobile.error'))
        ->callbackId('invite_mobile')
        ;

        $this->ask($question, function (BotManAnswer $answer) {
        	if (! $this->mobile = $this->checkMobile($answer->getText())) {

                return $this->repeat(trans('invite.input.mobile'));
        	}

            return $this->verify();
        });
    }

    protected function verify()
    {
        $question = BotManQuestion::create(trans('invite.input.verify', [
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

        $this->ask($question, function (BotManAnswer $answer) {
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

        //change seed to invite in the future
    	if ($user = User::seed($this->getCode(), $this->getMobile(), $this->getUser())) {
    		$user->invite($driver);
            $this->getMessenger()->setStatus('invited', trans('invite.reason'));
            $this->bot->reply(trans('invite.sent'));
    	}
        else
            $this->bot->reply(trans('invite.fail'));   

        $this->survey();
    }

    protected function survey()
    {
        $this->quizQuestions = Question::all();
        $this->questionCount = $this->quizQuestions->count();
        $this->quizQuestions = $this->quizQuestions->keyBy('id');

        $this->say(trans('invite.survey.info', ['count' => $this->questionCount]));
        $this->checkForNextQuestion();
    }

    private function checkForNextQuestion()
    {
        if ($this->quizQuestions->count()) {
            return $this->askQuestion($this->quizQuestions->first());
        }

        $this->showResult();
    }

    private function askQuestion(Question $question)
    {
        $this->ask($this->createQuestionTemplate($question), function (BotManAnswer $answer) use ($question) {
            $quizAnswer = Answer::find($answer->getValue());

            if (! $quizAnswer) {
                $this->say(trans('invite.survey.fallback'));
                return $this->checkForNextQuestion();
            }

            $this->quizQuestions->forget($question->id);
            $this->currentQuestion++;

            $this->say("Your answer: {$quizAnswer->text}");
            $this->checkForNextQuestion();
        });
    }

    private function createQuestionTemplate(Question $question)
    {
        $questionTemplate = BotManQuestion::create(trans('invite.survey.question', [
            'current' => $this->currentQuestion,
            'count' => $this->questionCount,
            'text' => $question->text
        ]));

        foreach ($question->answers as $answer) {
            $questionTemplate->addButton(Button::create($answer->text)->value($answer->id));
        }

        return $questionTemplate;
    }

    private function showResult()
    {
        $this->say(trans('invite.survey.finished'));
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

    public function setBot(BotMan $bot)
    {
        parent::setBot($bot);

        $roles = array_values(Role::toArray());
        $role = array_shift($roles);
        
        $this->codes = $roles;
    }
}
