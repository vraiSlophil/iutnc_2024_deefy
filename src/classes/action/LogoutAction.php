<?php

namespace iutnc\deefy\action;

class LogoutAction extends Action
{
    public function execute(): string
    {
        // Détruire la session
        session_start();
        session_unset();
        session_destroy();

        // Supprimer le cookie d'authentification
        if (isset($_COOKIE['auth_token'])) {
            setcookie('auth_token', '', time() - 3600, '/');
        }

        // Redirection vers l'action par défaut
        header('Location: ?action=default');
        exit();
    }
}