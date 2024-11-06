<?php

namespace iutnc\deefy\auth;

use iutnc\deefy\database\DeefyRepository;

class Authz
{
//    public static function validateToken(): bool
//    {
//        // Vérifier si le token de session et le cookie existent
//        if (!isset($_SESSION['user']) || !isset($_COOKIE['auth_token'])) {
//            return false;
//        }
//
//        // Récupérer l'ID utilisateur depuis la session
//        $user = unserialize($_SESSION['user']);
//        $user_id = $user->getUserId();
//
//        // Récupérer le token du cookie
//        $token = $_COOKIE['auth_token'];
//
//        // Valider le token via DeefyRepository
//        return DeefyRepository::getInstance()->validateToken($user_id, $token);
//    }

    /**
     * Vérifie si l'utilisateur est propriétaire de la playlist ou s'il est administrateur.
     *
     * @param int $userId
     * @param int $playlistId
     * @return bool
     */
    public static function isOwnerOfPlaylist(int $userId, int $playlistId): bool
    {
        $repository = DeefyRepository::getInstance();
        $user = $repository->getUserById($userId);

        // Check if the user is an admin
        if ($user['role_level'] >= 100) {
            return true;
        }

        // Check if the user is the owner of the playlist
        return $repository->isUserOwnerOfPlaylist($userId, $playlistId);
    }
}
