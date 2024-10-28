<?php

namespace iutnc\deefy\audio\tracks;

class AlbumTrack extends AudioTrack {
    private string $album;

    public function __construct(string $titre, int $duree, string $album) {
        parent::__construct($titre, $duree);
        $this->album = $album;
    }

    public function __get(string $name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return parent::__get($name);
    }
}