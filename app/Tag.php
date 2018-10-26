<?php

namespace App;

class Tag
{
    public static $classes = [
        'operator'   => \App\Operator::class,
        'staff' 	 => \App\Staff::class,
        'subscriber' => \App\Subscriber::class,
        'worker' 	 => \App\Worker::class,
    ];

    protected $regex = "/^(?<type>\S*)\s(?<code>\S*)\s(?<message>.*)$/i";

    protected $matches;

    protected $attributes;

	public static function attributes($arguments)
	{
		return (new static())
			->extractMatchedParameters($arguments)
			->removeNumericIndices()
			->extractPlacementAttributes()	
			->getAttributes()
			;
	}

    protected function extractMatchedParameters($arguments)
    {
		preg_match($this->regex, $arguments, $this->matches);

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

    protected function extractPlacementAttributes()
    {
		extract($this->matches);
			if ($type = $this->getClass($type))
				$this->attributes = compact('code', 'type');	

		return $this;
    }

    protected function getAttributes()
    {
    	return $this->attributes;
    }

    public function getClass($type)
    {
		if (in_array($type, array_keys(self::$classes)))
    		return self::$classes[$type];

    	return false;
    } 
}
