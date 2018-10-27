<?php

namespace App;

use App\Repositories\Parameterized;

class Tag extends Parameterized
{
    protected $regex = "/^(?<type>\S*)\s(?<code>\S*)\s(?<message>.*)$/i";

    protected function processAttributes($attributes)
    {
        extract($attributes);

        if ($type = $this->getClass($type))
            $attributes = compact('code', 'type');    

        return $attributes;
    }

    public function getClass($type)
    {
		if (in_array($type, array_keys(self::$classes)))
    		return User::$classes[$type];

    	return false;
    } 
}
