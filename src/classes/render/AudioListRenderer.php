<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\AudioList;

require_once 'Renderer.php';

class AudioListRenderer implements Renderer
{
    private AudioList $audioList;

    public function __construct(AudioList $audioList)
    {
        $this->audioList = $audioList;
    }

    public function render(): string
    {
        return $this->renderWithoutButton(false);
    }

    public function renderWithoutButton(bool $withoutButton = true): string
    {
        $output = '<div class="tracklist">
        <h1>' . htmlspecialchars($this->audioList->getName()) . '</h1>
        <ul>';

        foreach ($this->audioList as $piste) {
            $trackRenderer = new TrackRenderer($piste);
            $output .= '<li>' . $trackRenderer->render() . '</li>';
        }

        $output .= "</ul>
            <p>Nombre de pistes : " . htmlspecialchars($this->audioList->getTrackNumber()) . "</p>
            <p>Durée totale : " . htmlspecialchars($this->audioList->getTotalDuration()) . " secondes</p>" . ($withoutButton ? '' : "
            <form method='get' action='' class='form-index'>
                <input type='hidden' name='name' value='" . $this->audioList->getName() . "'>
                <button type='submit' name='action' value='add-track'>Add Podcast Track Action</button>
            </form>
            ") . "</div>";

        return $output;
    }
}