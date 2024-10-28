<?php

namespace iutnc\deefy\exception;

class InvalidPropertyNameException extends \Exception {
    public function __construct($property) {
        parent::__construct("Invalid property: $property");
    }
}