<?php

namespace iutnc\deefy\audio\lists;

use Exception;
use Iterator;
use iutnc\deefy\audio\tracks\AudioTrack;

class AudioList implements Iterator
{
    protected string $name;

    protected int $trackNumber;
    protected int $totalDuration;

    protected array $track;
    private int $position = 0;
    public function __construct(string $nom, array $pistes = [])
    {
        $this->name = $nom;
        $this->track = $pistes;
        $this->trackNumber = count($pistes);
        $this->totalDuration = array_reduce($pistes, function ($carry, AudioTrack $track) {
            return $carry + $track->__get('duree');
        }, 0);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getTotalDuration(): int
    {
        return $this->totalDuration;
    }

    /**
     * @return array
     */
    public function getTrack(): array
    {
        return $this->track;
    }

    /**
     * @return int
     */
    public function getTrackNumber(): int
    {
        return $this->trackNumber;
    }

    public function current(): mixed
    {
        return $this->track[$this->position];
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
        return isset($this->track[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}
