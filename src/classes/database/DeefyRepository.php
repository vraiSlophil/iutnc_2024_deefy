<?php

namespace iutnc\deefy\database;

//use AllowDynamicProperties;
use Cassandra\Date;
use DateTime;
use DateTimeZone;
use Dotenv\Dotenv;
use Exception;
use PDO;
use PDOException;

class DeefyRepository
{

    private static ?PDO $database = null;

    private static ?DeefyRepository $instance = null;

    private function __construct()
    {
        try {
            self::$database = new PDO($_ENV['DB_CONNECTION'] . ":host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): ?DeefyRepository
    {
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository();
        }
        return self::$instance;
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

    public function registerUser(string $name, string $email, string $hashed_password): int
    {
        $query = "INSERT INTO users (user_name, user_email, user_password) VALUES (:name, :email, :password)";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();
        return self::$database->lastInsertId();
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

    public function generateToken(int $user_id): string
    {
        // Supprimer les tokens expirés pour cet utilisateur
        $this->deleteExpiredTokens($user_id);

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

    public function deleteExpiredTokens(int $user_id): void
    {
        $query = "DELETE FROM tokens WHERE user_id = :user_id AND expires_at <= NOW()";
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

    public function deleteToken(string $token): bool
    {
        $query = "DELETE FROM tokens WHERE token = :token";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':token', $token);
        return $stmt->execute();
    }

    public function getUserById(int $user_id): array
    {
        $query = "SELECT * FROM users JOIN deefy.permissions p on p.permission_id = users.permission_id WHERE user_id = :user_id";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $this->returnWithoutPassword($stmt->fetch(PDO::FETCH_ASSOC));

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

    private function returnWithoutPassword(array $user): array
    {
        if ($user) {
            unset($user['user_password']);
        }
        return $user;
    }
}