<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\Authn;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\PlaylistRenderer;

class DisplayPlaylistAction extends Action
{
    public function execute(): string
    {
        return $this->renderPlaylists();
    }

    public function renderPlaylists(): string
    {
        $user = Authn::getAuthenticatedUser();
        $playlists = $user->getPlaylists();

        if (empty($playlists)) {
            return '<div>No playlists available.</div>';
        }

        $output = '<section class="playlists">';
        foreach ($playlists as $playlist) {
            $renderer = new PlaylistRenderer($playlist);
            $output .= $renderer->render();
        }
        $output .= '</section>';
        return $output;
    }
}