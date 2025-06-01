<?php

namespace alwk\php_sudoku\Models;

use alwk\php_sudoku\db\Database\Database;
use PDO;

class User {
    public ?int $id;
    public string $username;
    public string $password;
    public string $role;

    private ?Database $database = null;

    public function __construct($username, $password, $database, $role = 'user', ?int $id = null)
    {

        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->id = $id;
        $this->database = $database;

    }


    public static function findByUsername(string $username, ?Database $database): ?User {
        if (!$database) $database = new Database();
        $result = $database -> getUserByUsername($username);
        if ($result) {
            return new User( $result["username"], $result["password"], $database, $result["role"], $result["id"] );
        } else {
            return null;
        }
    }

    public function save(): void {
        if (!$this -> database) $this -> database = new Database();
        $this -> database -> addUser($this -> username, $this -> password, $this -> role);
    }
}