<?php


namespace alwk\php_sudoku\db\Database;
use PDO;
use PDOException;

class Database
{
    private ?PDO $pdo = null;

    public function __construct()
    {
        DB_Creator::createDatabase();
        $this->tryToConnect();
    }

    public function tryToConnect($dbPath = 'C:\Users\User\Desktop\php sudoku\app\db\database.sqlite'): void
    {
        try {
            $this->pdo = new PDO('sqlite:' . $dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die($dbPath);
        }
    }

    public function addUser($username, $password, $role = 'user'): bool
    {
        $this->tryToConnect();
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
        return $stmt->execute([
            ':username' => $username,
            ':password' => $password,
            ':role' => $role
        ]);
    }

    public function getUserByUsername($username)
    {
        $this->tryToConnect();
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function logActivity($userId): bool
    {
        $this->tryToConnect();
        $stmt = $this->pdo->prepare("INSERT INTO activity (user_id) VALUES (:user_id)");
        return $stmt->execute([':user_id' => $userId]);
    }

    public function getAllUsers(): array
    {
        $this->tryToConnect();
        $stmt = $this->pdo->query("SELECT id, username, role FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentActivities(): array
    {
        $this->tryToConnect();
        $stmt = $this->pdo->query("
            SELECT user_id, COUNT(*) as activity_count 
            FROM activity 
            WHERE activity_time >= DATE('now', '-30 days') 
            GROUP BY user_id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}