<?php
namespace alwk\php_sudoku\controller;

use alwk\php_sudoku\db\Database\Database;

class ApiController
{
    private ?Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }


    public function getUsers()
    {
        // Получаем всех пользователей из базы
        $users = $this->db->getAllUsers();
        $formattedUsers = array_map(function($user) {
            return [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
        }, $users);

        header('Content-Type: application/json');
        echo json_encode($formattedUsers);
    }

    public function getActivities()
    {
        // Получаем активность за последний месяц
        $activities = $this->db->getRecentActivities();
        header('Content-Type: application/json');
        echo json_encode($activities);
    }
}