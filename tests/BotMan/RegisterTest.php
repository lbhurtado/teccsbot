<?php

namespace Tests\BotMan;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    function setUp()
    {
        parent::setUp();

        $this->withoutEvents();

        $this->faker = $this->makeFaker('en_PH');

        // InvalidArgumentException: Unknown setter 'date'
        // $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
    }

    /** @test */
    public function register_inputs()
    {
        \Queue::fake();

        $this->bot
            ->receives('register')
            ->assertQuestion('Please enter mobile number.') 
            ->receives('09178251991')
            ->assertQuestion('Please enter your code.') 
            ->receives('operator')
            ->assertQuestion('Please enter your PIN.') 
            ;

        \Queue::assertPushed(\App\Jobs\RequestOTP::class);
    }
}
