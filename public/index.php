<?php

use alwk\php_sudoku\controller\ApiController;
use alwk\php_sudoku\Controllers\AuthController;
use alwk\php_sudoku\Controllers\SudokuController;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/SudokuController.php';
require_once __DIR__ . '/../app/controllers/ApiController.php';

require_once __DIR__ . '/../app/db/DB_Creator.php';
require_once __DIR__ . '/../app/db/Database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($path === '/register' && $method === 'POST') {
    $controller = new AuthController();
    $controller->register();
} elseif ($path === '/login' && $method === 'POST') {
    $controller = new AuthController();
    $controller->login();
} elseif ($path === '/solve' && $method === 'POST') {
    $controller = new SudokuController();
    $controller->solve();
} elseif ($path === '/users') {
    $controller = new ApiController();
    $controller -> getUsers();
} elseif ($path === '/activity') {
    $controller = new ApiController();
    $controller -> getActivities();
} elseif ($path === '/' || $path === '/login.html') {
    include 'login.html';
} elseif ($path === '/register.html') {
    include 'register.html';
} elseif ($path === '/sudoku.html') {
    include 'sudoku.html';
} elseif ($path === '/admin.html') {
    include 'admin.html';


} else {
    http_response_code(404);
    echo "Страница не найдена";
}
exit;
