<?php

namespace alwk\php_sudoku\Controllers;

use alwk\php_sudoku\Services\AuthService;
use Exception;

include_once("C:\Users\User\Desktop\php sudoku\app\services\AuthService.php");
class AuthController {

    private ?AuthService $_authService;

    public function __construct() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['auth_service'])) {
            $_SESSION['auth_service'] = new AuthService();
        }
        $this->_authService = $_SESSION['auth_service'];
    }

    public function register(): void
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['username']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing username or password']);
            return;
        }

        $username = $data['username'];
        $password = $data['password'];

        try {
            $result = $this->_authService->register($username, $password);
            echo json_encode($result);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function login(): void
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['username']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing username or password']);
            return;
        }

        $username = $data['username'];
        $password = $data['password'];

        try {
            $result = $this->_authService->login($username, $password);
            echo json_encode($result);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}