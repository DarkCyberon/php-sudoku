<?php

namespace alwk\php_sudoku\Controllers;

use alwk\php_sudoku\Services\SudokuService;
use Exception;

require_once("C:\Users\User\Desktop\php sudoku\app\services\SudokuService.php");

class SudokuController {

    public function solve() {
        header('Content-Type: application/json');

        // Чтение входных данных
        $inputData = json_decode(file_get_contents('php://input'), true);

        // Проверка наличия обязательных параметров
        if (!isset($inputData['puzzle']) || !isset($inputData['isSteps'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing parameters']);
            return;
        }

        $puzzleStr = $inputData['puzzle'];
        $isSteps = $inputData['isSteps'];

        try {
            // Создаем сервис и решаем судоку
            /** @var SudokuService */
            $service = new SudokuService($puzzleStr);
            $result = $service->solve((bool)$isSteps);

            // Возвращаем результат
            echo json_encode([
                'success' => true,
                'solution' => $result['solution'],
                'steps' => $result['steps'] ?? null,
                'solved' => $result['solved']
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}