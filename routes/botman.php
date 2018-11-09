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
    $bot->startConversation(new Onboarding());
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


$botman->hears('^\?([\w-]+(=[\w-]*)?(&[\w-]+(=[\w-]*)?)*)?$', UserController::class.'@set');
$botman->hears('\${key}', UserController::class.'@get');

$botman->hears('^#status\s(\w+)(?:\s*[-:|]\s*(.*))?$', UserController::class.'@setStatus');
$botman->hears('^#status$', UserController::class.'@getStatus');

$botman->hears('#(here|start|hash|reject|stray|tx)\s*(.*)', UserController::class.'@tag');

$botman->hears('#(strength|alert|execute|survey)\s*(.*)', UserController::class.'@dashboard');

$botman->fallback(function (BotMan $bot){
    if ($bot->getMessage()->getExtras('is_new_user')) {
        return $bot->startConversation(new Onboarding());
    }

    return $bot->reply($bot->getMessage()->getExtras('apiReply'));
});


# preg_match('/^\/?(?<tag>start|here)\s*(?<message>.*)$/i', $input_line, $output_array);

        // 'organization' => "/^#?(?<tag>start)\\s*(?<message>.*)$/i",
        // 'deployment' => "/^#?(?<tag>here)\\s*(?<message>.*)$/i",
        // 'hashcode' => "/^#?(?<tag>hash)\\s*(?<message>.*)$/i",
        // 'reject' => "/^#?(?<tag>reject)\\s*(?<message>.*)$/i",
        // 'stray' => "/^#?(?<tag>stray)\\s*(?<message>.*)$/i",
        // 'transmission' => "/^#?(?<tag>tx)\\s*(?<message>.*)$/i",


// • access FAQ details like transportation, speakers, and spon- sors
// • send push notifications for news/schedule changes
// • receive discounts/vouchers from sponsors
// • assemble your schedule
// • leave speaker feedback
// • connect attendees

// • it onboards new users
// • it asks the user about the notification subscription
// • it lets the user subscribe and unsubscribe from a subscrip- tion at any time
// • it can send notifications
// • it understands and replies to the main FAQs (location, date, speakers, etc.)
// Let’s Build a Conference Chatbot for Laracon EU 107
// • it provides a menu for Facebook Messenger
// • it provides commands for Telegram
// • it uses custom templates for Facebook Messenger
// • has a backend which helps with sending out notifications
