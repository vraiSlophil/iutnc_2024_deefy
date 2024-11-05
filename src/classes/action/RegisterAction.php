<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\Authn;
use iutnc\deefy\exception\AuthException;

class RegisterAction extends Action
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
        <form method="post" action="?action=register" class="form form-action">
        <div class="inputs">
            <div class="input-parent">
                <label for="user_name">Nom d\'utilisateur</label>
                <input type="text" id="user_name" name="user_name" placeholder="Nom d\'utilisateur">
            </div>
            <div class="input-parent">
                <label for="user_email">Email</label>
                <input type="email" id="user_email" name="user_email" placeholder="Email">
            </div>
            <div class="input-parent">
                <label for="user_password">Mot de passe</label>
                <input type="password" id="user_password" name="user_password" placeholder="Mot de passe">
            </div>
            <div class="input-parent">
                <label for="user_password_confirm">Confirmer le mot de passe</label>
                <input type="password" id="user_password_confirm" name="user_password_confirm" placeholder="Confirmer le mot de passe">
            </div>
        </div>
        <button type="submit">Créer mon compte</button>
        </form>
    ';

    }

    /**
     * @throws AuthException
     */
    private function handleFormSubmission(): string
    {
//        vérifier si les champs sont remplis et corrects

        if (empty($_POST['user_name']) || empty($_POST['user_email']) || empty($_POST['user_password']) || empty($_POST['user_password_confirm'])) {
            throw new AuthException('Tous les champs sont obligatoires');
        }

//        vérifier si l'email est valide
        if (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
            throw new AuthException('Email invalide');
        }

        $user_name = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $user_email = filter_input(INPUT_POST, 'user_email', FILTER_SANITIZE_SPECIAL_CHARS);

//        mots de passe mini 8 caractères dont : 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial sinon AuthException

        $user_password = $_POST['user_password'];
        $user_password_confirm = $_POST['user_password_confirm'];

        if ($user_password !== $user_password_confirm) {
            throw new AuthException('Les mots de passe ne correspondent pas');
        }

        $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()\-_+.\/'\",]).{8,}$/";

        if (!preg_match($regex, $user_password)) {
            throw new AuthException('Le mot de passe doit contenir au moins 8 caractères dont : 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial');
        }

        $user_password = password_hash($user_password, PASSWORD_DEFAULT);

        $authn = new Authn();
        $authn->registerUser($user_name, $user_email, $user_password);


        // Redirection vers l'action par défaut
        header('Location: ?action=default');
        exit();
    }
}