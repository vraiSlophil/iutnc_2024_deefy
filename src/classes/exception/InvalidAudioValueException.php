<?php

namespace iutnc\deefy\exception;

class InvalidAudioValueException extends \Exception
{
    public function __construct(string $property, $value)
    {
        parent::__construct("Invalid value for property $property: $value");
    }
}