<?php

namespace App\Jobs;

use App\Services\Telerivet;

class RegisterTelerivetService extends RegisterService
{
    protected $project;

    public function __construct($user, $project = null)
    {
        parent::__construct($user);

        $this->project = $project;
    }

    public function getId()
    {        
        $contact = $this->getProject()->getOrCreateContact([
            'name' => $this->getUser()->name, 
            'phone_number' => $this->getCountryCode() . $this->getNumber(), 
        ]);

        $this->getuser()->forceFill(['telerivet_id' => $contact->id])->save();

        return $this->getUser()->telerivet_id;
    }

    protected function getProject()
    {
        $config = config('broadcasting.connections.telerivet');

        return $this->project ?? (new Telerivet($config['api_key'], $config['project_id'], $config['service_id']))->getProject();
    }
}
