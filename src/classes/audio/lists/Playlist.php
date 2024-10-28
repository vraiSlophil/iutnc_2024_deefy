<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;

class Playlist extends AudioList {

    public function ajouterPiste(AudioTrack $piste) {
        $this->pistes[] = $piste;
        $this->nombreDePistes++;
        $this->dureeTotale += $piste->__get('duree');
    }

    public function supprimerPiste(int $index) {
        if (isset($this->pistes[$index])) {
            $this->dureeTotale -= $this->pistes[$index]->__get('duree');
            unset($this->pistes[$index]);
            $this->pistes = array_values($this->pistes);
            $this->nombreDePistes--;
        }
    }

    public function ajouterListeDePistes(array $pistes) {
        foreach ($pistes as $piste) {
            if (!$this->contientPiste($piste)) {
                $this->ajouterPiste($piste);
            }
        }
    }

    private function contientPiste(AudioTrack $piste) {
        foreach ($this->pistes as $existingPiste) {
            if ($existingPiste == $piste) {
                return true;
            }
        }
        return false;
    }
}