<?php

namespace iutnc\deefy\models;

use Exception;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\database\DeefyRepository;
use iutnc\deefy\exception\InvalidPropertyValueException;

class User
{
    private int $user_id;
    private string $user_name;
    private string $user_email;
    private Permission $permission;
    private array $playlists;

    /**
     * @throws InvalidPropertyValueException
     */
    public function __construct(string $user_email)
    {
        $user = DeefyRepository::getInstance()->getUserByEmail($user_email);

        $this->user_id = $user['user_id'];
        $this->user_name = $user['user_name'];
        $this->user_email = $user_email;
        $this->permission = new Permission($user['permission_id'], $user['role_name'], $user['role_level']);
        $this->playlists = $this->initializePlaylists();
    }

    /**
     * @throws InvalidPropertyValueException
     */
    private function initializePlaylists(): array
    {
        $playlists = [];
        $repository = DeefyRepository::getInstance();
        $userPlaylists = $repository->getUserPlaylists($this->user_id);

        foreach ($userPlaylists as $playlistData) {
            $playlist = new Playlist($playlistData['playlist_name'], $playlistData['playlist_id']);
            $tracks = $repository->getPlaylistTracks($playlistData['playlist_id']);
            $audioTracks = [];

            foreach ($tracks as $trackData) {
                $track = new AudioTrack($trackData['track_title'], $trackData['track_duration']);
                $track->setArtist($trackData['track_artist']);
                $track->setYear($trackData['track_year']);
                $track->setGenre($trackData['track_genre']);
                $track->setUrl($trackData['track_filename']);
                $audioTracks[] = $track;
            }

            $playlist->addTrackArray($audioTracks);
            $playlists[] = $playlist;
        }

        return $playlists;
    }

    public function addPlaylist(Playlist $playlist, bool $insertIntoDatabase = true): void
    {
        $this->playlists[] = $playlist;
        if ($insertIntoDatabase) {
            $repository = DeefyRepository::getInstance();
            $repository->addPlaylistToUser($this->user_id, $playlist);
        }
    }

    /**
     * @throws Exception
     */
    public function addTrackToPlaylist(string $playlistName, AudioTrack $track, bool $insertIntoDatabase = true): void
    {
        foreach ($this->playlists as $playlist) {
            if ($playlist->getName() === $playlistName) {
                $playlist->addTrack($track);
                if ($insertIntoDatabase) {
                    $repository = DeefyRepository::getInstance();
                    $repository->addTrackToPlaylist($playlist, $track);
                }
                return;
            }
        }
        throw new Exception("Playlist not found");
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
