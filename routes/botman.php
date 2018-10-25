<?php

use BotMan\BotMan\BotMan;
use App\Http\Controllers\BotManController;
use App\Http\Conversations\TestConversation;
use App\Conversations\Register;

$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});

$botman->hears('Start conversation', BotManController::class.'@startConversation');

$botman->hears('test', function (BotMan $bot) {
    $bot->startConversation(new TestConversation());
})->stopsConversation();

$botman->hears('stop|/stop|\s', function(BotMan $bot) {
	$bot->reply('stopped');
})->stopsConversation();

$botman->hears('register', function (BotMan $bot) {
    $bot->startConversation(new Register());
})->stopsConversation();