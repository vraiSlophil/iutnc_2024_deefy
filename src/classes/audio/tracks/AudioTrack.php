<?php

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;

class AudioTrack {
    private string $titre;
    private int $duree;

    public function __construct(string $titre, int $duree=0) {
        if ($duree < 0) {
            throw new InvalidPropertyValueException('duree', $duree);
        }
        $this->titre = $titre;
        $this->duree = $duree;
    }

    public function __get(string $name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new InvalidPropertyNameException($name);
    }
}