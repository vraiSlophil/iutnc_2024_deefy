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

            if (!isset($_SESSION['user'])) {
                throw new DataInsertException('No user found');
            }

            $user = unserialize($_SESSION['user']);
            $playlists = $user->getPlaylists();

            foreach ($playlists as $playlist) {
                if ($playlist->getName() === $name) {
                    $_SESSION['current_playlist'] = serialize($playlist);
                    $this->playlist = $playlist;
                    break;
                }
            }

            if (!isset($_SESSION['current_playlist'])) {
                throw new DataInsertException('Playlist not found');
            }

            return $this->renderForm();
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleFormSubmission();
            return '<div class="success">Track added successfully!</div>';
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
                <input type="text" id="name" name="name" placeholder="Track Name">
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
     * @throws InvalidPropertyValueException
     * @throws DataInsertException
     */
    private function handleFormSubmission(): void
    {
        if (!isset($_SESSION['current_playlist'])) {
            throw new DataInsertException('No playlist found in session');
        }

        $playlist = unserialize($_SESSION['current_playlist']);
        $trackName = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $artist = filter_input(INPUT_POST, 'artiste', FILTER_SANITIZE_SPECIAL_CHARS);
        $genre = filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_SPECIAL_CHARS);
        $duration = filter_input(INPUT_POST, 'duree', FILTER_VALIDATE_INT);
        $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_SPECIAL_CHARS);
        $file = $_FILES['file'];

        if (!$trackName || !$artist || !$genre || !$duration || !$date || !$file) {
            throw new InvalidAudioValueException('track details', 'One or more fields are missing or invalid');
        }

        if (!isset($_SESSION['user'])) {
            throw new DataInsertException('No user found');
        }

        $user = unserialize($_SESSION['user']);
        $playlists = $user->getPlaylists();

        // Handle file upload
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $fileExtension;
        $projectPath = realpath($_ENV['PROJECT_SOURCE_PATH']);
        $uploadDir = $projectPath . DIRECTORY_SEPARATOR . 'public/tracks';

        if (!$uploadDir || !is_dir($uploadDir)) {
            throw new DataInsertException('Upload directory does not exist or is invalid');
        }

        $uploadFile = $uploadDir . DIRECTORY_SEPARATOR . $newFileName;
        if (!move_uploaded_file($file['tmp_name'], $uploadFile)) {
            throw new DataInsertException('Failed to upload file');
        }

        // Create relative path using 'public/tracks'
        $relativePath = 'public/tracks/' . $newFileName;

        foreach ($playlists as $userPlaylist) {
            if ($userPlaylist->getName() === $playlist->getName()) {
                $track = new AudioTrack($trackName, $duration);
                $track->setArtist($artist);
                $track->setGenre($genre);
                $track->setYear($date);
                $track->setUrl($relativePath); // Use the relative path here
                $user->addTrackToPlaylist($playlist->getName(), $track);
                $_SESSION['user'] = serialize($user);
                unset($_SESSION['current_playlist']);
                return;
            }
        }

        throw new DataInsertException('Playlist not found');
    }
}