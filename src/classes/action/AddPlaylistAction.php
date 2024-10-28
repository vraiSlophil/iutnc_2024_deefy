<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\exception\DataInsertException;
use iutnc\deefy\exception\InvalidAudioValueException;
use iutnc\deefy\exception\InvalidPropertyValueException;
use iutnc\deefy\render\AudioListRenderer;

class AddPlaylistAction extends Action
{
    /**
     * @throws InvalidAudioValueException
     * @throws DataInsertException
     */
    public function execute(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->renderForm();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $playlist = $this->handleFormSubmission();
            $audioListRenderer = new AudioListRenderer($playlist);
            return $audioListRenderer->render();
        }
        return '';
    }

    private function renderForm(): string
    {
        return '
            <form method="post" action="?action=add-playlist" class="form form-action">
                <div class="input-parent">
                    <label for="name">Playlist Name</label>
                    <input type="text" id="name" name="name" placeholder="Playlist Name">
                </div>
                <button type="submit">Add Playlist</button>
            </form>
        ';
    }

    /**
     * @throws InvalidAudioValueException
     * @throws DataInsertException
     */
    private function handleFormSubmission(): Playlist
    {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        if (!$name) {
            throw new InvalidAudioValueException('name', $name);
        }

        if (!isset($_SESSION['playlists'])) {
            $_SESSION['playlists'] = [];
        }

        if (isset($_SESSION['playlists'])) {
            foreach ($_SESSION['playlists'] as $playlist) {
                $playlist = unserialize($playlist);
                if ($playlist->__get('nom') === $name) {
                    throw new DataInsertException('Playlist already exists');
                }
            }
        }

        $playlist = new Playlist($name);

        $_SESSION['playlists'][] = serialize($playlist);

        return $playlist;

    }
}