<?php

namespace App\Services;

class Telerivet
{
    protected $project;

    public function __construct($api_key, $project_id)
    {
        $api = new \Telerivet_API($api_key);
        $this->project = $api->initProjectById($project_id);
    }

    public function getProject()
    {
    	return $this->project;
    }
}
