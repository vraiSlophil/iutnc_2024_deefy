<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;

class Playlist extends AudioList {

    public function addTrack(AudioTrack $piste): void
    {
        $this->track[] = $piste;
        $this->trackNumber++;
        $this->totalDuration += $piste->getDuration();
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
        if (in_array($piste, $this->track)) {
            return true;
        }
        return false;
    }
}