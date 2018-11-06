<?php

use BotMan\BotMan\BotMan;

use App\Controllers\UserController;
use BotMan\BotMan\Middleware\ApiAi;
use App\Conversations\{SignUp, Verify, Onboarding, Invite};
use BotMan\BotMan\Middleware\Dialogflow;
use App\Http\Controllers\BotManController;
use App\Http\Middleware\ManagesUsersMiddleware;

$botman = resolve('botman');

$botman->hears('Hi', function (BotMan $bot) {
    $bot->reply('Hello!');
});

// $botman->hears('/start|GET_STARTED', function (BotMan $bot) {
//     $bot->reply(trans('onboarding.welcome', ['name' => config('app.name')]));
// });

$botman->hears('Start conversation|BREAK_SILENCE', BotManController::class.'@startConversation');

$dialogflow = Dialogflow::create('2a7576f8e70d445c89b6db456e0c3555')->listenForAction();
$botman->middleware->received($dialogflow);

$usersMiddleware = new ManagesUsersMiddleware;
$botman->middleware->received($usersMiddleware);
// $botman->middleware->matching($usersMiddleware);

$botman->hears('/stop|\s', function(BotMan $bot) {
	$bot->reply('stopped...');
})->stopsConversation();

$botman->hears('/invite', function (BotMan $bot) {
    $bot->startConversation(new Invite());
})->stopsConversation();

$botman->hears('/start|GET_STARTED', function (BotMan $bot) {
    $bot->startConversation(new Verify());
})->stopsConversation();

$botman->hears('/verify|VERIFY_MOBILE', function (BotMan $bot) {
    $bot->startConversation(new Verify());
})->stopsConversation();

$botman->hears('/signup|SIGN_UP', function (BotMan $bot) {
    $bot->startConversation(new SignUp());
})->stopsConversation();

$botman->hears('/register {attributes}', UserController::class.'@register');

$botman->hears('/placement', UserController::class.'@placement');

$botman->hears('/broadcast {message}', UserController::class.'@broadcast');

$botman->hears('/traverse', UserController::class.'@traverse');

//needs testing :-)
$botman->hears('\${key}\s*=\s*{value}', UserController::class.'@set');
$botman->hears('\${key}', UserController::class.'@get');

$botman->fallback(function (BotMan $bot){
    if ($bot->getMessage()->getExtras('is_new_user')) {
        return $bot->startConversation(new Onboarding());
    }

    return $bot->reply($bot->getMessage()->getExtras('apiReply'));
});


