<?php

namespace iutnc\deefy\database;

//use AllowDynamicProperties;
use DateTime;
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
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
        $dotenv->load();

        try {
            self::$database = new PDO($_ENV['DB_CONNECTION'] . ":host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
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
        $stmt->execute();
        return self::$database->lastInsertId();
    }

    public function loginUser(string $email, string $password)
    {
        $query = "SELECT user_password FROM users WHERE user_email = :email";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            return password_verify($password, $user['password']);
        }
        return false;

    }

    public function generateToken(int $user_id): string
    {
        $token = bin2hex(random_bytes(16));
        $expires_at = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

        $query = "INSERT INTO tokens (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires_at', $expires_at);
        $stmt->execute();

        return $token;
    }

    public function validateToken(string $token): ?int
    {
        $query = "SELECT user_id FROM tokens WHERE token = :token AND expires_at > NOW()";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['user_id'] : null;
    }

    public function deleteToken(string $token): void
    {
        $query = "DELETE FROM tokens WHERE token = :token";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
    }

    public function getUserById(int $user_id): array
    {
        $query = "SELECT * FROM users WHERE user_id = :user_id";
        $stmt = self::$database->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}