<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserEventsTest extends TestCase
{
	use RefreshDatabase;

    /** @test */
    function use_has_event_generated_jobs()
    {
        \Queue::fake();

        factory(\App\User::class)->create();

        \Queue::assertNotPushed(\App\Jobs\RegisterAuthyService::class); 
        \Queue::assertPushed(\App\Jobs\GenerateUserTasks::class);        
    }
}
