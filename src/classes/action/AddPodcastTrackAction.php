<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\exception\DataInsertException;
use iutnc\deefy\exception\InvalidAudioValueException;
use iutnc\deefy\exception\InvalidPropertyValueException;
use iutnc\deefy\render\AudioListRenderer;

class AddPodcastTrackAction extends Action
{

    private ?Playlist $playlist = null;

    /**
     * @throws InvalidAudioValueException
     * @throws DataInsertException
     * @throws InvalidPropertyValueException
     */
    public function execute(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $name = $_GET['name'];

            if (!$name) {
                throw new InvalidAudioValueException('name', $name);
            }

            if (!isset($_SESSION['playlists'])) {
                throw new DataInsertException('No playlists found');
            }

            foreach ($_SESSION['playlists'] as $playlist) {
                $playlist = unserialize($playlist);
                if ($playlist->__get('nom') === $name) {
                    $this->playlist = $playlist;
//                    echo $playlist->nom;
                    break;
                }
            }
            if ($this->playlist === null) {
                throw new DataInsertException('Playlist not found');
            }
            return $this->renderForm();
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {


            $this->handleFormSubmission();
            return '<div class="success">Track added successfully !</div>';
        }
        return '';
    }

    private function renderForm(): string
    {
        $renderer = new AudioListRenderer($this->playlist);

        return '
        <form method="post" action="?action=add-track" class="form form-action" enctype="multipart/form-data">
        <section class="playlists">' . $renderer->renderWithoutButton() . '</section>
        <div class="inputs">
            <div class="input-parent">
                <label for="track">Track Name</label>
                <input type="text" id="track" name="track" placeholder="Track Name">
            </div>
            <div class="input-parent">

                <label for="artiste">Artiste</label>
                <input type="text" id="artiste" name="artiste" placeholder="artiste">
            </div>
            <div class="input-parent">
                <label for="genre">Genre</label>
                <input type="text" id="genre" name="genre" placeholder="genre">
            </div>
            <div class="input-parent">
                <label for="duree">Durée</label>
                <input type="number" id="duree" name="duree" placeholder="duree">
            </div>
            <div class="input-parent">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" placeholder="date">
            </div>
            <div class="input-parent">
                <label for="file">Fichier audio</label>
                <input type="file" id="file" name="file" accept="audio/mpeg"/>
            </div>
        </div>
        <button type="submit">Add Track</button>
        </form>
    ';
    }

    /**
     * @throws InvalidAudioValueException
     * @throws InvalidPropertyValueException|DataInsertException
     */
    private function handleFormSubmission(): void
    {
        $playlistName = filter_input(INPUT_POST, 'playlist', FILTER_SANITIZE_SPECIAL_CHARS);
        $trackName = filter_input(INPUT_POST, 'track', FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$playlistName || !$trackName) {
            throw new InvalidAudioValueException('playlist or track name', $playlistName . ' or ' . $trackName);
        }

        if (!isset($_SESSION['playlists'])) {
            throw new DataInsertException('No playlists found');
        }

        foreach ($_SESSION['playlists'] as &$serializedPlaylist) {
            $playlist = unserialize($serializedPlaylist);
            if ($playlist->__get('nom') === $playlistName) {
                $track = new AudioTrack($trackName);
                $playlist->addTrack($track);
                $serializedPlaylist = serialize($playlist);
                return;
            }
        }

        throw new DataInsertException('Playlist not found');
    }
}