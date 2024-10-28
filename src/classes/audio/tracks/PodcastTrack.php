<?php

namespace iutnc\deefy\audio\tracks;

class PodcastTrack extends AudioTrack {
    private string $podcast;

    public function __construct(string $titre, int $duree, string $podcast) {
        parent::__construct($titre, $duree);
        $this->podcast = $podcast;
    }

    public function __get(string $name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return parent::__get($name);
    }
}