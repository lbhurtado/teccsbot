<?php

namespace App\Repositories;

abstract class Parameterized
{

	// protected $regex = "/^(?<code>\S*)\s(?<number>\S*)$/i";

    protected $arguments;

    protected $matches;

    protected $attributes;

	public static function attributes($arguments)
	{
		return (new static($arguments))
			->extractMatchedParameters()
			->removeNumericIndices()
			->extractAttributes()	
			->getAttributes()
			;
	}

    public function __construct($arguments)
    {
        $this->arguments = $arguments;
    }

    protected function extractMatchedParameters()
    {
		preg_match($this->regex, $this->arguments, $this->matches);

		return $this;
    }

    protected function removeNumericIndices()
    {

    	foreach ($this->matches as $k => $v) { 
    		if (is_int($k)) { 
    			unset($this->matches[$k]); 
    		} 
    	}

    	return $this;
    }

    protected function extractAttributes()
    {
        $attributes = $this->matches;

        if (method_exists($this, 'processAttributes')) {
            $attributes = $this->processAttributes($attributes);
        }

		$this->attributes = $attributes;	

		return $this;
    }

    protected function getAttributes()
    {
    	return $this->attributes;
    }
}