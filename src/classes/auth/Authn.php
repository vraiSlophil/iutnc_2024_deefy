<?php

namespace iutnc\deefy\auth;

use Exception;
use iutnc\deefy\database\DeefyRepository;
use iutnc\deefy\exception\AuthException;
use iutnc\deefy\models\User;

class Authn
{
    private DeefyRepository $deefyRepository;

    public function __construct()
    {
        $this->deefyRepository = DeefyRepository::getInstance();
    }

    /**
     * @throws AuthException
     */
    public function registerUser(string $name, string $email, string $hashed_password): bool
    {
        try {
            // étape 1: Enregistrer l'utilisateur
            $user_id = $this->deefyRepository->registerUser($name, $email, $hashed_password);

            if ($_SESSION['debug']) {
                echo "<pre>" . print_r($user_id, true) . "</pre>";
            }

            if (!$user_id) {
                throw new AuthException('Erreur lors de l\'enregistrement de l\'utilisateur.');
            }

            // étape 2: Récupérer les données de l'utilisateur
            $user_data = $this->deefyRepository->getUserById($user_id);
            $user = new User($user_data['user_id'], $user_data['user_name'], $user_data['user_email'], $user_data['permission_id']);

            // étape 3: Enregistrer l'utilisateur dans la session
            $_SESSION['user'] = serialize($user);

            // étape 4: Générer un jeton d'authentification
            $token = $this->deefyRepository->generateToken($user_id);
            $_SESSION['token'] = $token;

            // étape 5: Créer un cookie d'authentification
            setcookie('auth_token', $token, time() + (1000*60*60), "/"); // Cookie valide pour 1 heure

            return true;
        } catch (Exception $e) {
            throw new AuthException('Erreur lors de l\'enregistrement de l\'utilisateur: ' . $e->getMessage());
        }
    }

    /**
     * @throws AuthException
     */
    public function loginUser(string $email = null, string $password = null): bool
    {
        try {
            // étape 1: Vérifier si un jeton de connexion est présent et valide
            if (isset($_COOKIE['auth_token'])) {
                $token = $_COOKIE['auth_token'];
                $user_id = $this->deefyRepository->validateToken($token);
                if ($user_id) {
                    // étape 2: Récupérer les données de l'utilisateur
                    $user_data = $this->deefyRepository->getUserById($user_id);
                    $user = new User($user_data['user_id'], $user_data['user_name'], $user_data['user_email'], $user_data['permission_id']);

                    // étape 3: Enregistrer l'utilisateur dans la session
                    $_SESSION['user'] = serialize($user);
                    $_SESSION['token'] = $token;
                    return true;
                }
            }

            // étape 4: Vérifier les identifiants de l'utilisateur
            if (isset($email) && isset($password)) {
                echo "email: $email, password: $password";
                if ($this->deefyRepository->loginUser($email, $password)) {
                    // étape 5: Récupérer les données de l'utilisateur
                    $user_data = $this->deefyRepository->getUserByEmail($email);
                    $user = new User($user_data['user_id'], $user_data['user_name'], $user_data['user_email'], $user_data['permission_id']);

                    // étape 6: Enregistrer l'utilisateur dans la session
                    $_SESSION['user'] = serialize($user);

                    // étape 7: Générer un nouveau jeton d'authentification
                    $token = $this->deefyRepository->generateToken($user_data['user_id']);
                    $_SESSION['token'] = $token;
                    setcookie('auth_token', $token, time() + (1000*60*60), "/"); // Cookie valide pour 1 heure

                    return true;
                } else {
                    throw new AuthException('Identifiants incorrects.');
                }
            }

            throw new AuthException('Aucun jeton valide ou identifiants fournis.');
        } catch (Exception $e) {
            throw new AuthException('Erreur lors de la connexion de l\'utilisateur: ' . $e->getMessage());
        }
    }
}