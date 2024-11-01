<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AudioTrack;

class TrackRenderer implements Renderer
{
    private AudioTrack $track;

    public function __construct(AudioTrack $track)
    {
        $this->track = $track;
    }

    public function render(): string
    {
        return '<div class="track">
                    <h2>' . htmlspecialchars($this->track->getTitle()) . '</h2>
                    <p>Durée : ' . htmlspecialchars($this->track->getDuration()) . ' secondes</p>
                    <audio controls>
                        <source src="' . htmlspecialchars($this->track->getUrl()) . '" type="audio/mpeg">
                        Votre navigateur ne supporte pas l\'élément audio.
                    </audio>
                </div>';
    }
}