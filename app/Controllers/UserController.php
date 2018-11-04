<?php

namespace App\Controllers;


use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\{RequestOTP, VerifyOTP, SendBotmanMessage, Broadcast};
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
                $user->invite();
                // this is not working
                // $user->challenge();
                return $bot->reply('Invitation sent.');  
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
        // foreach(Messenger::has('user')->get() as $messenger) {
        //     SendBotmanMessage::dispatch($bot, $messenger, $message);
        // }

        $messenger = Messenger::where([
            'driver' => $bot->getDriver()->getName(),
            'channel_id' => $bot->getUser()->getId(),
        ])->first();

        Broadcast::dispatch($messenger->user, $message);

        $bot->reply('Broadcast sent.');
    }

    public function traverse(BotMan $bot)
    {
        $nodes = User::get()->toTree();

        $str = '';
        $traverse = function ($categories, $prefix = '-') use (&$traverse, &$str) {
            foreach ($categories as $category) {
                $str .= PHP_EOL.$prefix.' '.$category->name.' ('.$category->mobile.')';

                $traverse($category->children, $prefix.'-');
            }
        };

        $traverse($nodes);

        // dd($str);

        $bot->reply('Traversed.');
    }
}
