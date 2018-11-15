<?php

namespace App\Conversations;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class Checkin extends BaseConversation
{
	protected $user;

	protected $longitude;

	protected $latitude;

    public function run()
    {
        $this->introduction()->inputLocation();
    }

    protected function introduction()
    {
    	$name = $this->user->name;
    	$this->bot->reply(trans('checkin.introduction', compact('name')));

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
    	$this->bot->reply(trans('checkin.processing'));
    	$checkin = $this->getMessenger()->checkin($this->longitude, $this->latitude);
    	 \App\Jobs\ReverseGeocode::dispatch($checkin);

    	$this->bot->reply(trans('checkin.processed'));

    	$this->done();
    }

    protected function done()
    {
    	$this->bot->reply(trans('checkin.finished'));
    }

    public function setBot(BotMan $bot)
    {
        parent::setBot($bot);

        $this->user = $this->getUser();
    }
}
