<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserEventsTest extends TestCase
{
	use RefreshDatabase, WithFaker;

	protected $admin;

    function setUp()
    {
        parent::setUp();

        $this->faker = $this->makeFaker('en_PH');

        $this->user = factory(\App\Worker::class)->create();
    }

    /** @test */
    function use_has_event_generated_jobs_upon_creation()
    {
        \Queue::fake();

        factory(\App\User::class)->create();

        \Queue::assertNotPushed(\App\Jobs\RegisterAuthyService::class); 
        \Queue::assertPushed(\App\Jobs\GenerateUserTasks::class);        
    }

    /** @test */
    function use_has_event_generated_jobs_upon_updating()
    {
        \Queue::fake();

        $pin = $this->faker->randomNumber(6);
        $this->user->verifiedBy($pin, false);

        \Queue::assertPushed(\App\Jobs\SendAirtimeCredits::class);      
    }


}
