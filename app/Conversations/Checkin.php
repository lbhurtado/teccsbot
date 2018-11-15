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
        $this->introduction()->inputLocation();
    }

    protected function introduction()
    {
    	$this->bot->reply(trans('checkin.introduction'));

    	return $this;
    }

    protected function inputLocation()
    {
		return $this->askForLocation(trans('checkin.input.location'), function (Location $location) {
		    $this->latitude = $location->getLatitude();
		    $this->longitude = $location->getLongitude();

		    return $this->process();
		});
    }

    protected function process()
    {
    	$this->bot->reply(trans('checkin.processing.8'));
    	$this->getMessenger()->checkin($this->longitude, $this->latitude);
    	$this->bot->reply(trans('checkin.processed.8'));
    }
}
