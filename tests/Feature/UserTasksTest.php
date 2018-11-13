<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTasksTest extends TestCase
{
	use RefreshDatabase;

    /** @test */
    function user_children_models_can_have_automatic_tasks()
    {
        $user = factory(\App\Worker::class)->create();

        foreach(config('chatbot.tasks.worker') as $task)
        	$this->assertDatabaseHas('tasks', $task);
    }   
}
