<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ReverseGeocode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $checkin;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($checkin)
    {
        $this->checkin = $checkin;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $location = $this->getGeoCode()['formatted_address'];

        $this->checkin->forceFill(compact('location'))->save();
    }

    protected function getGeoCode()
    {
        return \Geocoder::getAddressForCoordinates(
            $this->checkin->latitude, 
            $this->checkin->longitude
        );
    }
}
