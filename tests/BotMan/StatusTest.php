<?php

namespace Tests\BotMan;

use Tests\TestCase;

class StatusTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $this->bot->receives('Hi')
            ->assertReply('Hello!');
    }
}
