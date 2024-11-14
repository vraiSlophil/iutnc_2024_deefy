<?php

namespace iutnc\deefy\auth;

use Exception;
use iutnc\deefy\database\DeefyRepository;
use iutnc\deefy\exception\AuthException;
use iutnc\deefy\models\Permission;
use iutnc\deefy\models\User;

class Authn
{
    private DeefyRepository $deefyRepository;

    public function __construct()
    {
        $this->deefyRepository = DeefyRepository::getInstance();
    }

    public static function isUserLoggedIn(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function getAuthenticatedUser(): User|null
    {
        if (Authn::isUserLoggedIn()) {
            return unserialize($_SESSION['user']);
        }
        return null;
    }

    /**
     * @throws AuthException
     */
    public function registerUser(string $name, string $email, string $hashed_password): bool
    {
        try {
            // étape 1: Enregistrer l'utilisateur
            $query = $this->deefyRepository->registerUser($name, $email, $hashed_password);

            if (!$query) {
                throw new AuthException('Erreur lors de l\'enregistrement de l\'utilisateur.');
            }

            $user = new User($email);

            // étape 3: Enregistrer l'utilisateur dans la session
            $_SESSION['user'] = serialize($user);

//            // étape 4: Générer un jeton d'authentification
//            $token = $this->deefyRepository->generateToken($user->getUserId());
//            $_SESSION['token'] = $token;
//
//            // étape 5: Créer un cookie d'authentification
//            setcookie('auth_token', $token, time() + (1000*60*60), "/"); // Cookie valide pour 1 heure

            return true;
        } catch (Exception $e) {
            throw new AuthException('Erreur lors de l\'enregistrement de l\'utilisateur : ' . $e->getMessage());
        }
    }

    /**
     * @throws AuthException
     */
    public function loginUser(string $email, string $password): bool
    {
        try {
            if ($this->deefyRepository->loginUser($email, $password)) {
                $user = new User($email);

                // étape 2: Enregistrer l'utilisateur dans la session
                $_SESSION['user'] = serialize($user);

//                // étape 3: Générer un nouveau jeton d'authentification
//                $token = $this->deefyRepository->generateToken($user->getUserId());
//                $_SESSION['token'] = $token;
//                setcookie('auth_token', $token, time() + (1000*60*60), "/"); // Cookie valide pour 1 heure

                return true;
            }
                throw new AuthException('Identifiants incorrects.');

        } catch (Exception $e) {
            throw new AuthException('Erreur lors de la connexion de l\'utilisateur: ' . $e->getMessage());
        }
    }

    public function logoutUser(): void
    {
//        // Supprimer le cookie d'authentification
//        if (isset($_COOKIE['auth_token'])) {
//            setcookie('auth_token', '', time() - 3600, '/');
//        }
//
//
//        $user = unserialize($_SESSION['user']);

//        // Supprimer le jeton d'authentification de la base de données
//        if (isset($_SESSION['token'])) {
//            $this->deefyRepository->deleteTokens($user->getUserId(), false);
//        }

        // Détruire la session
        session_start();
        session_unset();
        session_destroy();
    }
}