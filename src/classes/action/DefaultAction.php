<?php

namespace iutnc\deefy\action;

class DefaultAction extends Action
{
    public function execute(): string
    {
        $output = '<div>Welcome to Deefy!</div>';
        $output .= '<div>Use the menu to navigate.</div>';
        if (isset($_ENV) && $_ENV['APP_DEBUG'] === 'true') {
            $output .= isset($_SESSION['playlists']) ? var_dump($_SESSION['playlists']) : '<div>No playlists available.</div>';
        }

        return $output;
    }
}