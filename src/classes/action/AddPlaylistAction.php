<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\exception\AuthException;
use iutnc\deefy\exception\DataInsertException;
use iutnc\deefy\exception\InvalidPropertyValueException;
use iutnc\deefy\render\AudioListRenderer;

class AddPlaylistAction extends Action
{
    /**
     * @throws AuthException
     * @throws InvalidPropertyValueException
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
     * @throws DataInsertException
     * @throws InvalidPropertyValueException
     * @throws AuthException
     * @throws \Exception
     */
    private function handleFormSubmission(): Playlist
    {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);

//        name doit être une chaine avec que des lettres minuscules et/ou majuscules


        if (!preg_match('/^[a-zA-Z]+$/', $name)) {
            throw new \Exception('Le nom de la playlist ne doit contenir que des lettres');
        }

        if (!$name) {
            throw new InvalidPropertyValueException('name', $name);
        }

        if (!isset($_SESSION['user'])) {
            throw new AuthException('User must be logged in');
        }

        $user = unserialize($_SESSION['user']);

        foreach ($user->getPlaylists() as $playlist) {
            if ($playlist->getName() === $name) {
                throw new DataInsertException('Playlist already exists');
            }
        }

        $playlist = new Playlist($name);
        $user->addPlaylist($playlist);

        $_SESSION['user'] = serialize($user);

        return $playlist;
    }
}