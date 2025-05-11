<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../gestori/Sudoku.php';
if (!isset($_SESSION)) {
    session_start();
}


if (!isset($_SESSION['sudoku'])) {
    header("Location: home.php?msg=La dimensione della griglia deve essere 9 o 16");
    exit();
}
$sudoku = $_SESSION['sudoku'];

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['row'], $data['col'], $data['number'])) {
    echo json_encode(['correct' => false, 'error' => 'Missing input']);
    exit;
}

$row = intval($data['row']);
$col = intval($data['col']);
$number = $data['number'];

$result = $sudoku->checkNumber($row, $col, $number);

echo json_encode(['correct' => $result]);
