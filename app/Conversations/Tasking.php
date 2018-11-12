<?php

namespace App\Conversations;

use App\Task;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class Tasking extends BaseConversation
{
    protected $tasks;

    public function run()
    {
        $this->introduction()->chooseTask();
    }

    protected function introduction()
    {
    	$this->bot->reply(trans('task.introduction', ['count' => $this->tasks->count()]));

    	return $this;
    }

    protected function chooseTask()
    {
        $question = Question::create(trans('task.choose.task'))
        ->fallback(trans('task.choose.error'))
        ->callbackId('task_title')
        ;

    	foreach ($this->tasks as $task) {
    		$question->addButton(Button::create($task->title)->value($task->id));
    	}

        return $this->ask($question, function (Answer $answer) {
        	if ($answer->isInteractiveMessageReply()) {
                $task = $this->tasks->find($answer->getValue());
        		if (! $task->isAccepted()) {
        			return $this->acceptTask($task);
        		}
        		elseif (! $task->hasStarted()) {
        			return $this->startTask($task);
        		}
        		else 
        			return $this->endTask($task);
        	}
            else 
                return $tihs->repeat();
        });
    }

    protected function acceptTask(Task $task)
    {
        $question = Question::create(trans('task.accept.question', ['title' => $task->title]))
        ->fallback(trans('task.accept.error'))
        ->callbackId('task_accept')
        ->addButtons([
            Button::create(trans('task.accept.affirmative'))->value('yes'),
            Button::create(trans('task.accept.negative'))->value('no'),
        ])
        ;

        return $this->ask($question, function (Answer $answer) use ($task) {
            if ($answer->isInteractiveMessageReply()) {
                switch ($answer->getValue()) {
                    case 'yes':
                        $task->forceFill(['accepted_at' => now()])->save(); 
                        $this->bot->reply(trans('task.accept.accepted'));
                        break;
                    case 'no':
                        return $this->bot->reply(trans('task.accept.declined'));
                        break;
                }
            }
            else 
                return $tihs->repeat();

            return $this->startTask($task);
        });
    }

    protected function startTask(Task $task)
    {
        $question = Question::create(trans('task.start.question', ['title' => $task->title]))
        ->fallback(trans('task.start.error'))
        ->callbackId('task_start')
        ->addButtons([
            Button::create(trans('task.start.affirmative'))->value('yes'),
            Button::create(trans('task.start.negative'))->value('no'),
        ])
        ;

        return $this->ask($question, function (Answer $answer) use ($task) {
            if ($answer->isInteractiveMessageReply()) {
                switch ($answer->getValue()) {
                    case 'yes':
                        $task->forceFill(['started_at' => now()])->save(); 
                        $this->bot->reply(trans('task.start.accepted'));
                        break;
                    case 'no':
                        $this->bot->reply(trans('task.start.declined'));
                        break;
                }
            }
            else 
                return $tihs->repeat();

            return $this->endTask($task);
        });
    }

    protected function endTask(Task $task)
    {
        $question = Question::create(trans('task.end.question', ['title' => $task->title]))
        ->fallback(trans('task.end.error'))
        ->callbackId('task_end')
        ->addButtons([
            Button::create(trans('task.end.affirmative'))->value('yes'),
            Button::create(trans('task.end.negative'))->value('no'),
            Button::create(trans('task.end.abandon'))->value('never'),
        ])
        ;

        return $this->ask($question, function (Answer $answer) use ($task) {
            if ($answer->isInteractiveMessageReply()) {
                switch ($answer->getValue()) {
                    case 'yes':
                        $task->forceFill(['completed_at' => now()])->save(); 
                        $this->bot->reply(trans('task.end.completed'));
                        break;
                    case 'no':
                        $this->bot->reply(trans('task.end.deferred'));
                        break;
                    case 'never':
                        $task->forceFill(['abandoned_at' => now()])->save(); 
                        $this->bot->reply(trans('task.end.abandoned'));
                        break;
                }
            }
            else 
                return $tihs->repeat();

        });
    	$this->bot->reply(trans('task.finished'));
    }

    public function setBot(BotMan $bot)
    {
        parent::setBot($bot);
        $this->tasks = $this->getUser()->tasks()->available()->orderBy('rank', 'asc')->get();
    }
}
