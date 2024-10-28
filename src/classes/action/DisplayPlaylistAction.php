<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\AudioListRenderer;

class DisplayPlaylistAction extends Action
{
    public function execute(): string
    {
        return $this->renderPlaylists();
    }

    public function renderPlaylists(): string
    {
        if (!isset($_SESSION['playlists'])) {
            return '<div>No playlists available.</div>';
        }
        $output = '<section class="playlists">';
        foreach ($_SESSION['playlists'] as $playlist) {
            $renderer = new AudioListRenderer(unserialize($playlist));
            $output .= $renderer->render();
        }
        $output .= '</section>';
        return $output;
    }
}