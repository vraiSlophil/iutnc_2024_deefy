<?php

namespace iutnc\deefy\action;

class DefaultAction extends Action
{
    public function execute(): string
    {
        $output = '<div>Welcome to Deefy!</div>';
        $output .= '<div>Use the menu to navigate.</div>';
        $output .= isset($_SESSION['playlists']) ? var_dump($_SESSION['playlists']) : '<div>No playlists available.</div>';

        return $output;
    }
}