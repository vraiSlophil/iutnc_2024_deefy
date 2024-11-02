<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;

class Playlist extends AudioList
{
    private int $id;

    public function __construct(string $name, int $id = 0)
    {
        parent::__construct($name);
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function removeTrack(int $index): void
    {
        if (isset($this->track[$index])) {
            $this->totalDuration -= $this->track[$index]->getDuration();
            unset($this->track[$index]);
            $this->track = array_values($this->track);
            $this->trackNumber--;
        }
    }

    public function addTrackArray(array $pistes): void
    {
        foreach ($pistes as $piste) {
            if (!$this->containsTrack($piste)) {
                $this->addTrack($piste);
            }
        }
    }

    private function containsTrack(AudioTrack $piste): bool
    {
        return in_array($piste, $this->track);
    }

    public function addTrack(AudioTrack $piste): void
    {
        $this->track[] = $piste;
        $this->trackNumber++;
        $this->totalDuration += $piste->getDuration();
    }

    public function trackExists(string $title): bool
    {
        foreach ($this->track as $piste) {
            if ($piste->getTitle() === $title) {
                return true;
            }
        }
        return false;
    }

    public function getFormattedTotalDuration(): string
    {
        $hours = floor($this->totalDuration / 3600);
        $minutes = floor(($this->totalDuration % 3600) / 60);
        $seconds = $this->totalDuration % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}