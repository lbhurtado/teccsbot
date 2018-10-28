<?php

namespace App\Controllers;


use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\{RequestOTP, VerifyOTP};
use BotMan\Drivers\Telegram\TelegramDriver;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use App\{Register, Placement, Messenger, User};

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

    public function placement(BotMan $bot)
    {

        $messenger = Messenger::where([
            'driver' => $bot->getDriver()->getName(),
            'channel_id' => $bot->getUser()->getId(),
        ])->first();

        if ($placements = Placement::by($messenger->user)->get()->sort()) {
            $bot->reply(implode(',', $placements->pluck('code')->toArray()));            
        }

        else
            $bot->reply('');
    }

    public function broadcast(BotMan $bot, $message)
    {
        // dd(User::verified()->get());
        // foreach(User::verified()->get() as $user) {
        //     $bot->say($message, $user->channel_id, TelegramDriver::class);
        // }

        foreach(Messenger::all() as $messenger) {
            $bot->say($message, $messenger->channel_id, TelegramDriver::class);
        }

        $bot->reply('Broadcast sent.');
    }
}
