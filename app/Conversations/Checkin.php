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
	protected $longitude;

	protected $latitude;

    public function run()
    {
    	$this->latitude = 52.3832816;

    	$this->longitude = 4.9205266;

        $this->introduction()->verifyLocation();
    }

    protected function introduction()
    {
    	$this->bot->reply(trans('checkin.introduction'));

    	return $this;
    }

    protected function verifyLocation()
    {
$this->askForLocation("Please Give me your location", function (Location $location) {
            $this->say('Received: '.print_r($location, true));
});

	    // $this->askForLocation(trans('checkin.verify.location'), function (Location $location) {
		   //  $this->latitude = $location->getLatitude();
		   //  $this->longitude = $location->getLongitude();

		   //  $this->bot->reply('here');
	    // });

	    $this->bot->reply('there');
		$attachment = new Location($this->latitude, $this->longitude);
		$message = OutgoingMessage::create(trans('checkin.verify.message'))->withAttachment($attachment);
	    $this->say($message, [
	        'title' => trans('checkin.verify.title'),
	        'address' => trans('checkin.verify.address'),
	    ]);
		// $this->bot->receivesLocation(function($bot, Location $location) {
		//     $lat = $location->getLatitude();
		//     $lng = $location->getLongitude();
		// });
		$this->bot->reply('everywhere');
    }
}
