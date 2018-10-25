<?php

namespace Tests\BotMan;

use Tests\TestCase;

class RegisterTest extends TestCase
{
    /** @test */
    public function register_inputs()
    {
        $this->bot
            ->receives('register')
            ->assertQuestion('Please enter mobile number.') 
            ->receives('09178251991')
            ->assertQuestion('Please enter your code.') 
            ->receives('operator')
            ->assertQuestion('Please enter your PIN.') 
            ->receives('123456')
            ->assertReply('Thank you.')
            ;
    }
}
