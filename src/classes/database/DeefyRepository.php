<?php

namespace iutnc\deefy\database;

use DateMalformedStringException;
use DateTime;
use DateTimeZone;
use Exception;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AudioTrack;
use PDO;
use PDOException;
use Random\RandomException;

class DeefyRepository
{

    private static ?PDO $database = null;

    private static ?DeefyRepository $instance = null;

    /**
     * @throws Exception
     */
    private function __construct()
    {
        try {
            self::$database = new PDO($_ENV['DB_CONNECTION'] . ":host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"]);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public static function testConnection(): bool
    {
        try {
            self::getInstance();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getInstance(): ?DeefyRepository
    {
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository();
        }
        return self::$instance;
    }

    public function registerUser(string $name, string $email, string $hashed_password): bool
    {
        $query = "INSERT INTO users (user_name, user_email, user_password) VALUES (:name, :email, :password)";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        return $stmt->execute();
    }

    public function loginUser(string $email, string $password): bool
    {
        $query = "SELECT user_password FROM users WHERE user_email = :email";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && isset($user['user_password'])) {
            return password_verify($password, $user['user_password']);
        }
        return false;
    }

    /**
     * @throws DateMalformedStringException
     * @throws RandomException
     */
    public function generateToken(int $user_id): string
    {
        // Supprimer les tokens expirés pour cet utilisateur
        $this->deleteTokens($user_id, false);

        $token = bin2hex(random_bytes(32));
        $date = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $expires_at = $date->modify('+1 hour')->format('Y-m-d H:i:s');

        $query = "INSERT INTO tokens (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires_at', $expires_at);
        $stmt->execute();

        return $token;
    }

    public function deleteTokens(int $user_id, bool $onlyExpiredTokens = true): void
    {
        $query = "DELETE FROM tokens WHERE user_id = :user_id";
        $query .= $onlyExpiredTokens ? " AND expires_at < NOW()" : "";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    }

    public function validateToken(int $user_id, string $token): bool
    {
        $query = "SELECT COUNT(*) FROM tokens WHERE user_id = :user_id AND token = :token AND expires_at > NOW()";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count > 0;
    }

    public function getUserById(int $user_id): array
    {
        $query = "SELECT * FROM users JOIN deefy.permissions p on p.permission_id = users.permission_id WHERE user_id = :user_id";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $this->returnWithoutPassword($stmt->fetch(PDO::FETCH_ASSOC));

    }

    private function returnWithoutPassword(array $user): array
    {
        if ($user) {
            unset($user['user_password']);
        }
        return $user;
    }

    public function getUserByEmail(string $email): array
    {
        $query = "SELECT * FROM users JOIN deefy.permissions p on p.permission_id = users.permission_id WHERE user_email = :email";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $this->returnWithoutPassword($stmt->fetch(PDO::FETCH_ASSOC));

    }

    public function getUserList(): array
    {
        $query = "SELECT * FROM users JOIN deefy.permissions p on p.permission_id = users.permission_id";
        $stmt = self::$database->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $key => $value) {
            $result[$key] = $this->returnWithoutPassword($value);
        }

        return $result;
    }

    public function getUserPlaylists(int $user_id): array
    {
        $query = "SELECT p.playlist_id, p.playlist_name
                  FROM playlists p
                  JOIN user_playlists up ON p.playlist_id = up.playlist_id
                  WHERE up.user_id = :user_id";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPlaylistTracks(int $playlist_id): array
    {
        $query = "SELECT pt.track_id, pt.track_title, pt.track_genre, pt.track_duration, pt.track_year, pt.track_filename, pt.track_artist
                  FROM podcast_tracks pt
                  JOIN playlist_tracks plt ON pt.track_id = plt.track_id
                  WHERE plt.playlist_id = :playlist_id";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':playlist_id', $playlist_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addPlaylistToUser(int $user_id, Playlist $playlist): void
    {
        $query = "INSERT INTO playlists (playlist_name) VALUES (:name)";
        $stmt = self::$database->prepare($query);
        $playlist_name = $playlist->getName();
        $stmt->bindParam(':name', $playlist_name, PDO::PARAM_STR);
        $stmt->execute();
        $playlist_id = self::$database->lastInsertId();

        $query = "INSERT INTO user_playlists (user_id, playlist_id) VALUES (:user_id, :playlist_id)";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':playlist_id', $playlist_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function addTrackToPlaylist(Playlist $playlist, AudioTrack $track): void
    {

        // Vérifiez si la playlist existe
        $query = "SELECT COUNT(*) FROM playlists WHERE playlist_id = :playlist_id";
        $stmt = self::$database->prepare($query);
        $playlist_id = $playlist->getId();
        $stmt->bindParam(':playlist_id', $playlist_id);
        $stmt->execute();
        $playlistExists = $stmt->fetchColumn();

        if (!$playlistExists) {
            throw new Exception("Playlist with ID $playlist_id does not exist.");
        }

        // Insert the track into the podcast_tracks table
        $query = "INSERT INTO podcast_tracks (track_title, track_genre, track_duration, track_year, track_filename, track_artist)
              VALUES (:title, :genre, :duration, :year, :filename, :artist)";
        $stmt = self::$database->prepare($query);
        $track_title = $track->getTitle();
        $track_genre = $track->getGenre();
        $track_duration = $track->getDuration();
        $track_year = $track->getYear();
        $track_filename = $track->getUrl();
        $track_artist = $track->getArtist();
        $stmt->bindParam(':title', $track_title);
        $stmt->bindParam(':genre', $track_genre);
        $stmt->bindParam(':duration', $track_duration);
        $stmt->bindParam(':year', $track_year);
        $stmt->bindParam(':filename', $track_filename);
        $stmt->bindParam(':artist', $track_artist);
        $stmt->execute();
        $track_id = self::$database->lastInsertId();

        // Insert the relationship into the playlist_tracks table
        $query = "INSERT INTO playlist_tracks (playlist_id, track_id) VALUES (:playlist_id, :track_id)";
        $stmt = self::$database->prepare($query);
        $playlist_id = $playlist->getId();
        $stmt->bindParam(':playlist_id', $playlist_id);
        $stmt->bindParam(':track_id', $track_id);
        $stmt->execute();
    }
}