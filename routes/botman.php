<?php

use BotMan\BotMan\BotMan;
use App\Http\Controllers\BotManController;
use App\Http\Conversations\TestConversation;
use App\Conversations\SignUp;
use App\Http\Middleware\ManagesUsersMiddleware;

$botman = resolve('botman');

$botman->hears('Hi', function (BotMan $bot) {
    $bot->reply('Hello!');
});

$botman->hears('Start conversation', BotManController::class.'@startConversation');

$usersMiddleware = new ManagesUsersMiddleware;
$botman->middleware->received($usersMiddleware);
// $botman->middleware->matching($usersMiddleware);

$botman->hears('test', function (BotMan $bot) {
    $bot->startConversation(new TestConversation());
})->stopsConversation();

$botman->hears('stop|/stop|\s', function(BotMan $bot) {
	$bot->reply('stopped');
})->stopsConversation();

$botman->hears('signup', function (BotMan $bot) {
    $bot->startConversation(new SignUp());
})->stopsConversation();