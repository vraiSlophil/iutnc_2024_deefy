<?php

namespace iutnc\deefy\audio\lists;

use Exception;
use Iterator;
use iutnc\deefy\audio\tracks\AudioTrack;

class AudioList implements Iterator
{
    protected string $nom;
    protected int $nombreDePistes;
    protected int $dureeTotale;
    protected array $pistes;

    private int $position = 0;

    public function __construct(string $nom, array $pistes = [])
    {
        $this->nom = $nom;
        $this->pistes = $pistes;
        $this->nombreDePistes = count($pistes);
        $this->dureeTotale = array_reduce($pistes, function ($carry, AudioTrack $track) {
            return $carry + $track->__get('duree');
        }, 0);
    }

    public function __get(string $name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new Exception("Invalid property: $name");
    }

    public function current(): mixed
    {
        return $this->pistes[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->pistes[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}
