<?php

namespace App;

use Illuminate\Support\Facades\Validator;

class Attributes
{
    protected $attributes;

    public static function filter($attributes)
    {
        return (new static($attributes))->validate();
    }

    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    public function validate()
    {
        return array_filter($this->attributes, function($value, $key) {

            return Validator::make([$key => $value], config('chatbot.rules'))->passes();
        }, ARRAY_FILTER_USE_BOTH);
    }
}
