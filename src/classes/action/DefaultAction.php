<?php

namespace iutnc\deefy\action;

class DefaultAction extends Action
{
    public function execute(): string
    {
        $output = '<div>Welcome to Deefy!</div>';
        $output .= '<div>Use the menu to navigate.</div>';
        if ($_SESSION['debug']) {
            $output .= "<br>";
            $output .= isset($_SESSION['playlists']) ? "<pre>" . print_r($_SESSION['playlists'], true) . "</pre>" : '<div>No playlists available.</div>';
            $output .= "<br>";
            $output .= isset($_SESSION['user']) ? "<pre>" . print_r(unserialize($_SESSION['user']), true) . "</pre>" : '<div>No user available.</div>';
        }

        return $output;
    }
}