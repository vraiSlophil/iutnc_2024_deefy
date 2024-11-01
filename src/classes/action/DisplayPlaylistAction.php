<?php

namespace iutnc\deefy\action;

use iutnc\deefy\render\AudioListRenderer;

class DisplayPlaylistAction extends Action
{
    public function execute(): string
    {
        return $this->renderPlaylists();
    }

    public function renderPlaylists(): string
    {
        if (!isset($_SESSION['user'])) {
            return '<div>No playlists available.</div>';
        }

        $user = unserialize($_SESSION['user']);
        $playlists = $user->getPlaylists();

        if (empty($playlists)) {
            return '<div>No playlists available.</div>';
        }

        $output = '<section class="playlists">';
        foreach ($playlists as $playlist) {
            $renderer = new AudioListRenderer($playlist);
            $output .= $renderer->render();
        }
        $output .= '</section>';
        return $output;
    }
}