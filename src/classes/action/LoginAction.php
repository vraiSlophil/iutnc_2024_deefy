<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\Authn;
use iutnc\deefy\exception\AuthException;

class LoginAction extends Action
{
    /**
     * @throws AuthException
     */
    public function execute(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->renderForm();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleFormSubmission();
        }
        return '';
    }

    private function renderForm(): string
    {
        return '
        <form method="post" action="?action=login" class="form form-action">
        <div class="inputs">
            <div class="input-parent">
                <label for="user_email">Email</label>
                <input type="email" id="user_email" name="user_email" placeholder="Email">
            </div>
            <div class="input-parent">
                <label for="user_password">Mot de passe</label>
                <input type="password" id="user_password" name="user_password" placeholder="Mot de passe">
            </div>
        </div>
        <button type="submit">Se connecter</button>
        </form>
    ';
    }

    /**
     * @throws AuthException
     */
    private function handleFormSubmission(): string
    {
        if (empty($_POST['user_email']) || empty($_POST['user_password'])) {
            throw new AuthException('Tous les champs sont obligatoires');
        }

        $user_email = filter_input(INPUT_POST, 'user_email', FILTER_SANITIZE_EMAIL);
        $user_password = $_POST['user_password'];

        $authn = new Authn();
        if ($authn->loginUser($user_email, $user_password)) {
            // Redirection vers l'action par défaut
            header('Location: ?action=default');
            exit();
        } else {
            throw new AuthException('Identifiants incorrects.');
        }
    }
}