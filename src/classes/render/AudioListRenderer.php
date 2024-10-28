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

    public function renderWithoutButton(bool $withoutBoutton = true): string
    {
        $output = "<div class='tracklist'>
        <h1>" . htmlspecialchars($this->audioList->__get('nom')) . "</h1>
        <ul>";
        foreach ($this->audioList as $piste) {
            $output .= "<li class='track court'>
                            <h2>" . htmlspecialchars($piste->__get('titre')) . "</h2>
                            <p>Durée : " . htmlspecialchars($piste->__get('duree')) . " secondes</p>
                            <audio controls>
                                <source src='//' type='audio/mpeg'>
                                Votre navigateur ne supporte pas l'élément audio.
                            </audio>
                        </li>";
        }
        $output .= "</ul>
            <p>Nombre de pistes : " . htmlspecialchars($this->audioList->__get('nombreDePistes')) . "</p>
        <p>Durée totale : " . htmlspecialchars($this->audioList->__get('dureeTotale')) . " secondes</p>"
        . ($withoutBoutton ? '' : "
        <form method='get' action='' class='form-index'>
            <input type='hidden' name='name' value='" . $this->audioList->__get('nom') . "'>
            <button type='submit' name='action' value='add-track'>Add Podcast Track Action</button>
        </form>
        ") .
    "</div>";

        return $output;
    }
}