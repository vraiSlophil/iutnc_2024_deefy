<?php

namespace iutnc\deefy\exception;

class DataInsertException extends \Exception {
    public function __construct($message) {
        parent::__construct("Invalid data for insertion : $message");
    }
}