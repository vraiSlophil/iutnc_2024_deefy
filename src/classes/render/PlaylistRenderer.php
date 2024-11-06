<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\Playlist;

class PlaylistRenderer implements Renderer
{
    private Playlist $playlist;

    public function __construct(Playlist $playlist)
    {
        $this->playlist = $playlist;
    }

    public function render(): string
    {
        return $this->renderWithoutButton(false);
    }

    public function renderWithoutButton(bool $withoutButton = true): string
    {
        $output = '<div class="tracklist">
        <h1>' . htmlspecialchars($this->playlist->getName()) . '</h1>
        <ul>';

        foreach ($this->playlist as $piste) {
            $trackRenderer = new TrackRenderer($piste);
            $output .= '<li>' . $trackRenderer->render() . '</li>';
        }

        $output .= "</ul>
            <p>Nombre de pistes : " . htmlspecialchars($this->playlist->getTrackNumber()) . "</p>
            <p>Durée totale : " . htmlspecialchars($this->playlist->getTotalDuration()) . " secondes</p>" . ($withoutButton ? '' : "
            <form method='get' action='' class='form-index'>
                <input type='hidden' name='id' value='" . $this->playlist->getId() . "'>
                <button type='submit' name='action' value='add-track'>Add Podcast Track Action</button>
            </form>
            ") . "</div>";

        return $output;
    }
}