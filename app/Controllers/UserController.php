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
                $user->invite('Telegram');
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

    public function set(BotMan $bot, $query_string)
    {
        parse_str($query_string, $associative_array);

        $messenger = $this->getMessenger($bot);
        foreach ($associative_array as $key => $value) {
            $messenger->user->extra_attributes->set($key, $value);
        }
        $messenger->user->save();

        $attributes = http_build_query($associative_array);
        $bot->reply(trans('attributes.set', compact('attributes')));
    }

    public function get(BotMan $bot, $key)
    {
        $key = trim($key);

        $messenger = $this->getMessenger($bot);
        $value = $messenger->user->extra_attributes->get($key);

        $attribute = http_build_query([$key => $value]);
        $bot->reply(trans('attributes.get', compact('attribute')));
    }

    protected function getMessenger(BotMan $bot)
    {
        return Messenger::where([
            'driver' => $bot->getDriver()->getName(),
            'channel_id' => $bot->getUser()->getId(),
        ])->first();
    }

}
