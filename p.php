<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>ciao</h1>
    <?php
    require_once("gestori/Sudoku.php");
    require_once("gestori/gestoreDatabase.php");
    if (!isset($_SESSION)) {
        session_start();
    }
    
    $sudoku = new Sudoku("normal",9, "easy");
    /*
    print_r($sudoku->grid);
    echo "<br>";
    echo "<br>";
    echo    "<br>row:";
    print_r($sudoku->getAllCityPovRow());
    echo    "<br>column:";
    print_r($sudoku->getAllCityPovColumn());
    */
    /*
    $db = GestoreDatabase::getInstance();
    print_r($db->getStatistichePerTipo($_SESSION['username']));
    */
    ?>
</body>

</html>