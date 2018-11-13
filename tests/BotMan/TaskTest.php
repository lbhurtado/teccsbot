<?php

namespace Tests\BotMan;

use Tests\TestCase;
use App\{User, Worker, Messenger, Phone};
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/task';
    private $user;
    private $channel_id;
    private $messenger;
    private $tasks;

    function setUp()
    {
        parent::setUp();
        $this->faker = $this->makeFaker('en_PH');
        // $this->withoutEvents();

        $this->user = factory(Worker::class)->create(['mobile' => Phone::number('09189362340')]);
        $this->channel_id = $this->faker->randomNumber(8);
        $this->messenger = Messenger::create([
            'driver' => 'Telegram', 
            'channel_id' => $this->channel_id
        ]);

        $this->messenger->user()->associate($this->user);
        $this->messenger->save();

        $this->tasks = $this->user->tasks()->available()->orderBy('rank', 'asc')->get();
    }

    /** @test */
    public function task_successful_run()
    {
        $task = $this->tasks->first();

        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('task.introduction', ['count' => $this->tasks->count()]))
            ->assertQuestion(trans('task.choose.task'))
            ->receivesInteractiveMessage($task->id)
            ->assertQuestion(trans('task.read.optional'))
            ->receivesInteractiveMessage('yes')
            ->assertReply(trans('task.read.instructions', ['instructions' => $task->instructions]))
            ->assertQuestion(trans('task.read.continue'))
            ->receives('kahit ano')
            ->assertQuestion(trans('task.accept.question', ['title' => $task->title]))
            ->receivesInteractiveMessage('yes')
            ->assertReply(trans('task.accept.accepted', ['title' => $task->title]))
            ->assertQuestion(trans('task.start.question', ['title' => $task->title]))
            ->receivesInteractiveMessage('yes')
            ->assertReply(trans('task.start.accepted', ['title' => $task->title]))
            ->assertQuestion(trans('task.end.question', ['title' => $task->title]))
            ->receivesInteractiveMessage('yes')
            ->assertReply(trans('task.end.completed', ['title' => $task->title]))
            ->assertReply(trans('task.finished'))
            ;

        $task->refresh();
        $this->assertTrue($task->isAccepted());
        $this->assertTrue($task->hasStarted());
        $this->assertTrue($task->hasCompleted());
    }

    /** @test */
    public function task_no_instructions()
    {
        $task = $this->tasks->first();

        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('task.introduction', ['count' => $this->tasks->count()]))
            ->assertQuestion(trans('task.choose.task'))
            ->receivesInteractiveMessage($task->id)
            ->assertQuestion(trans('task.read.optional'))
            ->receivesInteractiveMessage('no')
            ->assertQuestion(trans('task.accept.question', ['title' => $task->title]))
            ->receivesInteractiveMessage('yes')
            ->assertReply(trans('task.accept.accepted', ['title' => $task->title]))
            ->assertQuestion(trans('task.start.question', ['title' => $task->title]))
            ->receivesInteractiveMessage('yes')
            ->assertReply(trans('task.start.accepted', ['title' => $task->title]))
            ->assertQuestion(trans('task.end.question', ['title' => $task->title]))
            ->receivesInteractiveMessage('yes')
            ->assertReply(trans('task.end.completed', ['title' => $task->title]))
            ->assertReply(trans('task.finished'))
            ;

        $task->refresh();
        $this->assertTrue($task->isAccepted());
        $this->assertTrue($task->hasStarted());
        $this->assertTrue($task->hasCompleted());
    }

    /** @test */
    public function task_no_instructions_not_accept()
    {
        $task = $this->tasks->first();

        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('task.introduction', ['count' => $this->tasks->count()]))
            ->assertQuestion(trans('task.choose.task'))
            ->receivesInteractiveMessage($task->id)
            ->assertQuestion(trans('task.read.optional'))
            ->receivesInteractiveMessage('no')
            ->assertQuestion(trans('task.accept.question', ['title' => $task->title]))
            ->receivesInteractiveMessage('no')
            ->assertReply(trans('task.accept.declined', ['title' => $task->title]))
            ->assertReply(trans('task.finished'))
            ;

        $task->refresh();
        $this->assertTrue(! $task->isAccepted());
        $this->assertTrue(! $task->hasStarted());
        $this->assertTrue(! $task->hasCompleted());
    }

    /** @test */
    public function task_no_instructions_not_start()
    {
        $task = $this->tasks->first();

        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('task.introduction', ['count' => $this->tasks->count()]))
            ->assertQuestion(trans('task.choose.task'))
            ->receivesInteractiveMessage($task->id)
            ->assertQuestion(trans('task.read.optional'))
            ->receivesInteractiveMessage('no')
            ->assertQuestion(trans('task.accept.question', ['title' => $task->title]))
            ->receivesInteractiveMessage('yes')
            ->assertReply(trans('task.accept.accepted', ['title' => $task->title]))
            ->assertQuestion(trans('task.start.question', ['title' => $task->title]))
            ->receivesInteractiveMessage('no')
            ->assertReply(trans('task.start.declined', ['title' => $task->title]))
            ->assertReply(trans('task.finished'))
            ;

        $task->refresh();
        $this->assertTrue(  $task->isAccepted());
        $this->assertTrue(! $task->hasStarted());
        $this->assertTrue(! $task->hasCompleted());
    }

    /** @test */
    public function task_no_instructions_defer()
    {
        $task = $this->tasks->first();

        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('task.introduction', ['count' => $this->tasks->count()]))
            ->assertQuestion(trans('task.choose.task'))
            ->receivesInteractiveMessage($task->id)
            ->assertQuestion(trans('task.read.optional'))
            ->receivesInteractiveMessage('no')
            ->assertQuestion(trans('task.accept.question', ['title' => $task->title]))
            ->receivesInteractiveMessage('yes')
            ->assertReply(trans('task.accept.accepted', ['title' => $task->title]))
            ->assertQuestion(trans('task.start.question', ['title' => $task->title]))
            ->receivesInteractiveMessage('yes')
            ->assertReply(trans('task.start.accepted', ['title' => $task->title]))
            ->assertQuestion(trans('task.end.question', ['title' => $task->title]))
            ->receivesInteractiveMessage('no')
            ->assertReply(trans('task.end.deferred', ['title' => $task->title]))
            ->assertReply(trans('task.finished'))
            ;

        $task->refresh();
        $this->assertTrue(  $task->isAccepted());
        $this->assertTrue(  $task->hasStarted());
        $this->assertTrue(! $task->hasCompleted());
    }

    /** @test */
    public function task_no_instructions_abandon()
    {
        $task = $this->tasks->first();

        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('task.introduction', ['count' => $this->tasks->count()]))
            ->assertQuestion(trans('task.choose.task'))
            ->receivesInteractiveMessage($task->id)
            ->assertQuestion(trans('task.read.optional'))
            ->receivesInteractiveMessage('no')
            ->assertQuestion(trans('task.accept.question', ['title' => $task->title]))
            ->receivesInteractiveMessage('yes')
            ->assertReply(trans('task.accept.accepted', ['title' => $task->title]))
            ->assertQuestion(trans('task.start.question', ['title' => $task->title]))
            ->receivesInteractiveMessage('yes')
            ->assertReply(trans('task.start.accepted', ['title' => $task->title]))
            ->assertQuestion(trans('task.end.question', ['title' => $task->title]))
            ->receivesInteractiveMessage('never')
            ->assertReply(trans('task.end.abandoned', ['title' => $task->title]))
            ->assertReply(trans('task.finished'))
            ;

        $task->refresh();
        $this->assertTrue(  $task->isAccepted());
        $this->assertTrue(  $task->hasStarted());
        $this->assertTrue(! $task->hasCompleted());
        $this->assertTrue(  $task->isAbandoned());
    }
}
