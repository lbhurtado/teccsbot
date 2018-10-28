<?php

use BotMan\BotMan\BotMan;

use App\Controllers\UserController;
use App\Http\Controllers\BotManController;
use App\Http\Conversations\TestConversation;
use App\Conversations\{SignUp, Verify};
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

$botman->hears('register {attributes}', UserController::class.'@register');

$botman->hears('verify', function (BotMan $bot) {
    $bot->startConversation(new Verify());
})->stopsConversation();

$botman->hears('placement', UserController::class.'@placement');

$botman->hears('broadcast {message}', UserController::class.'@broadcast');