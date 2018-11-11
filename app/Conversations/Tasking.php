<?php

namespace App\Conversations;

use App\Task;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class Tasking extends BaseConversation
{
    public function run()
    {
        $this->introduction()->chooseTask();
    }

    protected function introduction()
    {
    	$this->bot->reply(trans('task.introduction'));

    	return $this;
    }

    protected function chooseTask()
    {
        $question = Question::create(trans('task.choose.title'))
        ->fallback(trans('task.name.error'))
        ->callbackId('task_title')
        ;

    	$tasks = $this->getUser()->tasks;
    	foreach ($tasks as $task) {
    		$question->addButton(Button::create($task->title)->value($task->id));
    	}

        return $this->ask($question, function (Answer $answer) {
        	if ($answer->isInteractiveMessageReply()) {
        		$task = Task::find($answer->getValue());
        		if ($task->accepted_at == null) {
        			return $this->acceptTask($task);
        		}
        		elseif ($task->started_at == null) {
        			return $this->startTask($task);
        		}
        		else 
        			return $this->endTask($task);
        	}
			$this->bot->reply(trans('task.chosen.title'));
        });
    }

    protected function acceptTask(Task $task)
    {
    	$this->bot->reply(trans('task.accept.task'));
    }

    protected function startTask(Task $task)
    {
    	$this->bot->reply(trans('task.start.task'));
    }

    protected function endTask(Task $task)
    {
    	$this->bot->reply(trans('task.end.task'));
    }
}
