<?php

namespace iutnc\deefy\models;

use iutnc\deefy\audio\lists\Playlist;

class User
{
    private int $user_id;
    private string $user_name;
    private string $user_email;
    private int $permission_id;
    private array $playlists;

    public function __construct(int $user_id, string $user_name, string $user_email, int $permission_id)
    {
        $this->user_id = $user_id;
        $this->user_name = $user_name;
        $this->user_email = $user_email;
        $this->permission_id = $permission_id;
        $this->playlists = [];
    }

    public function addPlaylist(Playlist $playlist): void
    {
        $this->playlists[] = $playlist;
    }










    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getUserName(): string
    {
        return $this->user_name;
    }

    public function getUserEmail(): string
    {
        return $this->user_email;
    }

    public function getPermissionId(): int
    {
        return $this->permission_id;
    }
}