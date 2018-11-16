<?php

namespace App\Jobs;

class RegisterTelerivetService extends RegisterService
{
    protected $project;

    public function __construct($user, $project)
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
        return $this->project;
    }
}
