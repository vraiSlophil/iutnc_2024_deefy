<?php

namespace iutnc\deefy\audio\lists;

class Album extends AudioList
{
    private string $artiste;
    private string $dateDeSortie;

    public function __construct(string $nom, array $pistes, string $artiste, string $dateDeSortie)
    {
        parent::__construct($nom, $pistes);
        $this->artiste = $artiste;
        $this->dateDeSortie = $dateDeSortie;
    }

    public function setArtiste(string $artiste)
    {
        $this->artiste = $artiste;
    }

    public function setDateDeSortie(string $dateDeSortie)
    {
        $this->dateDeSortie = $dateDeSortie;
    }
}