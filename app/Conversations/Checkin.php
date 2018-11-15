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
		$this->askForLocation(trans('checkin.input.location'), function (Location $location) {
		    // $this->latitude = $location->getLatitude();
		    // $this->longitude = $location->getLongitude();
    		$this->bot->reply(trans('checkin.processing'));
			$this->getUser()->checkin(5.1, 6.2);
			// $this->getUser()->checkin($location->getLongitude(), $location->getLatitude());
		    // return $this->process($location->getLongitude(), $location->getLatitude());
    		$this->bot->reply(trans('checkin.processed'));
		});
    }

    // protected function process($longitude, $latitude)
    // {
    // 	$this->bot->reply(trans('checkin.processing'));
    // 	// $this->getUser()->checkins()->create(compact('longitude', 'latitude'));
    // 	$this->getUser()->checkin(compact('longitude', 'latitude'));
    // 	$this->bot->reply(trans('checkin.processed'));
    // }
}
