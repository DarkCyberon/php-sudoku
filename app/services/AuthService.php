<?php

namespace alwk\php_sudoku\Services;

use alwk\php_sudoku\db\Database\Database;
use alwk\php_sudoku\Models\User;
require_once ("C:\Users\User\Desktop\php sudoku\app\models\User.php");
require_once ("C:\Users\User\Desktop\php sudoku\app\db\Database.php");

class AuthService {

    private ?Database $database = null;

    public function __construct() {
        $this->database  = new Database();
    }

    public function register(string $username, string $password): array {
        if (User::findByUsername($username, $this ->database)) {
            return ['success' => false, 'message' => 'User already exists'];
        }
        $user = new User($username, $password, $this ->database);
        $user->save();

        $new_user = User::findByUsername(username: $username,database: $this ->database);
        $this ->database ->logActivity($new_user -> id);
        return ['success' => true, 'message' => 'Registration successful'];
    }

    public function login(string $username, string $password): array {
        $user = User::findByUsername($username, $this ->database);
        if (!$user || $password != $user->password) {
            return ['success' => false, 'token' => '', 'message' => "Wrong password"];
        }
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['user'] = [
            'username' => $user->username,
            'role' => $user->role,
        ];

        // Генерируем токен с учетом роли
        if ($user->role === 'admin') {
            $token = 'admin-' . bin2hex(random_bytes(8));
        } else {
            $token = 'user-' . bin2hex(random_bytes(8));
        }
        $this ->database ->logActivity($user -> id);
        return [
            'success' => true,
            'token' => $token,
            'role' => $user->role
        ];
    }
}