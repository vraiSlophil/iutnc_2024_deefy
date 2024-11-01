<?php

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyValueException;

class AudioTrack {
    private string $title;
    private string $artitst;
    private string $year;
    private string $genre;
    private int $duration;
    private string $url;

    public function __construct(string $title, int $duration) {
        if ($duration < 0) {
            throw new InvalidPropertyValueException('duration', $duration);
        }
        $this->title = $title;
        $this->artist = '';
        $this->year = 0;
        $this->genre = '';
        $this->duration = $duration;
        $this->url = '';
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getArtist(): string
    {
        return $this->artist;
    }

    public function setArtist(string $artist): void
    {
        $this->artist = $artist;
    }

    public function getArtitst(): string
    {
        return $this->artitst;
    }

    public function setArtitst(string $artitst): void
    {
        $this->artitst = $artitst;
    }

    public function getYear(): string
    {
        return $this->year;
    }

    public function setYear(string $year): void
    {
        $this->year = $year;
    }

    public function getGenre(): string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }
}