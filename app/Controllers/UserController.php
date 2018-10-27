<?php

namespace App\Controllers;

use App\Jobs\{RequestOTP, VerifyOTP};
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\{Register, Placement};
use App\Http\Controllers\Controller;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;

class UserController extends Controller
{
    public function register(BotMan $bot, $arguments)
    {
    	if ($attributes = Register::attributes($arguments)) {
            if ($user = Placement::activate(array_pull($attributes,'code'), $attributes)) {
                $user->verify();
                
                return $bot->reply('OTP sent.');  
            }   
    	}

        $bot->reply('Try again.'); 
    }
}
