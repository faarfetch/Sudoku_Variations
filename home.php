<?php
require_once("gestori/Sudoku.php");
include("header.php");
if (!isset($_SESSION)) {
    session_start();
}

if (isset($_GET['msg'])) {
    echo "<div class='message'>" . htmlspecialchars($_GET['msg']) . "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sudoku Variants</title>
    <link rel="stylesheet" href="style/homeStyle.css">
</head>

<body>

    <div class="container">
        <header>
            <h1>Sudoku Variants</h1>
        </header>

        <div class="form-container">
            <form action="game.php" method="post">

                <div class="form-group">
                    <label for="type">Scegli il tipo:</label>
                    <select name="type" id="type">
                        <option value="normal">Normale</option>
                        <option value="city">City</option>
                        <option value="pois">Pois</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="size">Scegli la dimensione:</label>
                    <select name="size" id="size">
                        <option value="9">9x9</option>
                        <option value="16">16x16</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="difficulty">Scegli la difficoltà:</label>
                    <select name="difficulty" id="difficulty">
                        <option value="easy">Facile</option>
                        <option value="medium">Medio</option>
                        <option value="hard">Difficile</option>
                    </select>
                </div>

                <div class="form-actions">
                    <input type="submit" value="Inizia il gioco">
                </div>
            </form>
        </div>

        <div class="actions">
            <input type="button" value="Continua gioco" onclick="window.location.href='game.php';">
            <?php
            if (!isset($_SESSION['autenticato']) || $_SESSION['autenticato'] != 1) {
                echo "<a href='login.php' class='login-link'>Fai Login</a>";
            }
            ?>
        </div>
    </div>

</body>

</html>