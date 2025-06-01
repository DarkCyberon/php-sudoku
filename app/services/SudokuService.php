<?php

namespace alwk\php_sudoku\Services;

use Exception;

class SudokuService {

    const N = 9;

    private array $_initialBoard;
    private array $_solutionBoard;

    public function __construct(string $puzzleStr) {
        if (strlen($puzzleStr) !== self::N * self::N) {
            throw new Exception("Invalid puzzle length");
        }
        $this->_initialBoard = [];
        for ($i=0; $i<self::N; ++$i){
            for ($j=0; $j<self::N; ++$j){
                $val= intval(substr($puzzleStr, $i*self::N + $j, 1));
                if ($val < 0 || $val > 9) {
                    throw new Exception("Invalid puzzle character");
                }
                $this->_initialBoard[$i][$j]= $val;
            }
        }
        // Создаем копию начальной доски для решения
        $this->_solutionBoard = $this->deepCopyBoard($this->_initialBoard);
    }

    // Вспомогательная функция для глубокого копирования массива доски
    private function deepCopyBoard(array $board): array {
        return array_map(function($row) { return $row; }, $board);
    }

    // Проверка, безопасно ли поставить число в позицию
    private function isSafe(array &$board, int $row, int $col, int $num): bool {
        for ($x=0; $x<self::N; ++$x){
            if ($board[$row][$x] == $num || $board[$x][$col] == $num) {
                return false;
            }
        }
        // Проверка 3x3 блока
        $startRow = (int) (($row / 3)) * 3;
        $startCol = (int) (($col / 3)) * 3;
        for ($i=0; $i<3; ++$i){
            for ($j=0; $j<3; ++$j){
                if ($board[$startRow + $i][$startCol + $j] == $num) {
                    return false;
                }
            }
        }
        return true;
    }

    private function hiddenSingles(array &$board): bool {
        $foundAny = false;
        // Для каждой ячейки проверяем возможные числа
        for ($row=0; $row<self::N; ++$row) {
            for ($col=0; $col<self::N; ++$col) {
                if ($board[$row][$col] == 0) {
                    $possibleNumbers = [];
                    // Проверяем все числа от 1 до 9
                    for ($num=1; $num<=9; ++$num) {
                        if ($this->isSafe($board, $row, $col, $num)) {
                            $possibleNumbers[] = $num;
                        }
                    }
                    // Если только одно возможное число — ставим его
                    if (count($possibleNumbers) == 1) {
                        $board[$row][$col] = $possibleNumbers[0];
                        $foundAny = true;
                    }
                }
            }
        }
        return $foundAny;
    }

    // Основной метод решения с бэктрекинг
    function backtrackSolve(&$board, &$steps, $isSteps): bool {
        for ($row = 0; $row < self::N; $row++) {
            for ($col = 0; $col < self::N; $col++) {
                if ($board[$row][$col] == 0) {
                    for ($num = 1; $num <= 9; $num++) {
                        if (self::isSafe($board, $row, $col, $num)) {
                            // Пробуем поставить число
                            $board[$row][$col] = $num;

                            if ($isSteps) {
                                self::saveStep($board, $steps);
                            }

                            if (self::backtrackSolve($board, $steps, $isSteps)) {
                                return true;
                            }

                            $board[$row][$col] = 0;
                        }
                    }
                    return false;
                }
            }
        }
        return true;
    }

    // Сохраняет текущий шаг решения в список steps
    function saveStep($currentBoard, &$steps): void {
        $stepCopy = [];
        foreach ($currentBoard as $row) {
            $stepCopy[] = array_slice($row, 0);
        }
        $steps[] = $stepCopy;
    }

    public function solve(bool $isSteps): array {
        $_board = $this->deepCopyBoard($this->_initialBoard);
        $_steps = [];

        $maxIterations = 100;
        $iteration = 0;
        do {
            $progress = $this->hiddenSingles($_board);
            $iteration++;
            if ($iteration >= $maxIterations) {
                break; // Предотвращаем бесконечный цикл
            }
        } while ($progress);

        if (!$this->backtrackSolve($_board, $_steps, $isSteps)){
            return [
                'solution' => null,
                'steps' => null,
                'error' => 'No solution found or puzzle is unsolvable'
            ];
        }

        $solutionStr = '';
        for ($i=0; $i<self::N; ++$i){
            for ($j=0; $j<self::N; ++$j){
                $solutionStr .= ($_board[$i][$j]);
            }
        }

        $stepsStr = '';
        for ($i=0; $i < count($_steps); ++$i) {
            for ($j = 0; $j < self::N; ++$j) {
                for ($k = 0; $k < self::N; ++$k) {
                    $stepsStr .= ($_steps[$i][$j][$k]);
                }
            }
        }

        return [
            'solution' => $solutionStr,
            'steps' => !$isSteps ? null : $stepsStr,
            'solved' => true,
            'error' => null,
            'board' => $_board
        ];
    }
}