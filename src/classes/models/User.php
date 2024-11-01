<?php

namespace iutnc\deefy\models;

use iutnc\deefy\audio\lists\Playlist;

class User
{
    private int $user_id;
    private string $user_name;
    private string $user_email;
    private Permission $permission;
    private array $playlists;

    public function __construct(int $user_id, string $user_name, string $user_email, Permission $permission)
    {
        $this->user_id = $user_id;
        $this->user_name = $user_name;
        $this->user_email = $user_email;
        $this->permission = $permission;
        $this->playlists = [];
    }

    public function addPlaylist(Playlist $playlist): void
    {
        $this->playlists[] = $playlist;
    }

    public function getPlaylists(): array
    {
        return $this->playlists;
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

    public function getPermission(): Permission
    {
        return $this->permission;
    }

    public function hasAccess(int $requiredLevel): bool
    {
        return $this->permission->getRoleLevel() >= $requiredLevel;
    }
}
