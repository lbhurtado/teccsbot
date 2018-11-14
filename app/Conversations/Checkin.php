<?php

namespace App\Conversations;

// use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class Checkin extends BaseConversation
{
    public function run()
    {
        $this->introduction()->verifyLocation();
    }

    protected function introduction()
    {
    	$this->bot->reply(trans('checkin.introduction'));

    	return $this;
    }

    protected function verifyLocation()
    {
		$this->say('Laracon EU 2018 is located in beautiful Amsterdam.');
		$attachment = new Location(52.3832816, 4.9205266);
		$message = OutgoingMessage::create('')->withAttachment($attachment);
	    $this->say($message, [
	        'title' => 'Laracon EU 2018',
	        'address' => 'Kromhouthal Gedempt Hamerkanaal 231 1021 KP Amsterdam, the Netherlands',
	    ]);
		    $this->say('There is also a map with info about the surrounding: https://snazzymaps.com/embed/69943 ');

		// $this->bot->receivesLocation(function($bot, Location $location) {
		//     $lat = $location->getLatitude();
		//     $lng = $location->getLongitude();
		// });
    }
}
