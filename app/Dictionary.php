<?php

namespace App;

class Dictionary
{
    public static $colors = [
        'red',
        'orange',
        'yellow',
        'green',
        'blue',
        'indigo',
        'violet',
    ];

    public static $planets = [
    	'mercury',
    	'venus',
    	'earth',
    	'mars',
    	'jupiter',
    	'saturn',
    	'uranus',
    	'neptune',
    	'pluto',
    ];

    public static $phonetics = [
 		'alfa', 
 		'bravo', 
 		'charlie', 
 		'delta', 
 		'echo', 
 		'foxtrot', 
 		'golf', 
 		'hotel', 
 		'india', 
 		'juliett', 
 		'kilo', 
 		'lima', 
 		'mike', 
 		'november', 
 		'oscar', 
 		'papa', 
 		'quebec', 
 		'romeo', 
 		'sierra', 
 		'tango', 
 		'uniform', 
 		'victor', 
 		'whiskey', 
 		'x-ray', 
 		'yankee', 
 		'zulu',
    ];

    public static function generate(...$index)
    {

    	$indices = array_flatten($index);
    	$array = [];
    	foreach ($indices as $source) {
    		$word = '';
    		switch ($source) {
    			case 1:
    				$word = array_random(self::$colors);
    				break;
    			case 2:
    				$word = array_random(self::$planets);
    				break;
    			case 3:
    				$word = array_random(self::$phonetics);
    				break;
    			default:
    				$word = array_random(self::$phonetics);
    				break;
    		}
    		$array [] = $word;
    	}

    	return implode('-', $array);
    }

    public function __create(...$index)
    {
    	$array = array_flatten($index);

    	dd($array);
    }
}
