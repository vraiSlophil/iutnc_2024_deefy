<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\Authn;

class LogoutAction extends Action
{
    public function execute(): string
    {
        $authn = new Authn();

        $authn->logoutUser();

        // Redirection vers l'action par défaut
        header('Location: ?action=default');
        exit();
    }
}