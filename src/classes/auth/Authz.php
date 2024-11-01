<?php

namespace iutnc\deefy\auth;

use iutnc\deefy\database\DeefyRepository;

class Authz
{
    public static function validateToken(): bool
    {
        // Vérifier si le token de session et le cookie existent
        if (!isset($_SESSION['user']) || !isset($_COOKIE['auth_token'])) {
            return false;
        }

        // Récupérer l'ID utilisateur depuis la session
        $user = unserialize($_SESSION['user']);
        $user_id = $user->getUserId();

        // Récupérer le token du cookie
        $token = $_COOKIE['auth_token'];

        // Valider le token via DeefyRepository
        return DeefyRepository::getInstance()->validateToken($user_id, $token);
    }
}
