<?php
require_once '../gestori/Sudoku.php';
if (!isset($_SESSION)) {
    session_start();
}


header('Content-Type: application/json');

if (isset($_SESSION['sudoku'])) {
    $sudoku = $_SESSION['sudoku'];
    $complete = $sudoku->isComplete();
    echo json_encode(['complete' => $complete]);
} else {
    echo json_encode(['complete' => false]);
}