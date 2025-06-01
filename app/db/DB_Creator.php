<?php
// init_db.php
namespace alwk\php_sudoku\db\Database;


use PDO;

class DB_Creator
{
    public static function createDatabase(): void
    {
        $dbFile = __DIR__ . '/database.sqlite';

        if (!file_exists($dbFile)) {
            $db = new PDO('sqlite:' . $dbFile);
            // Создаем таблицу users
            $db->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                role TEXT DEFAULT 'user'
            )
        ");
            // Создаем таблицу activity
            $db->exec("
            CREATE TABLE activity (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                activity_time DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id)
            )
        ");
        }
    }
}