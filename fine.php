<?php
require_once("gestori/Sudoku.php");

if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['sudoku']) && $_SESSION["sudoku"]->isComplete() == true) {
    $sudoku = $_SESSION['sudoku'];
    $sudoku->saveData();
} else {
    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complimenti, hai vinto!</title>
    <link rel="stylesheet" href="style/fineStyle.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h1>Complimenti, hai vinto!</h1>
        <p>Ottimo lavoro! Hai completato il Sudoku con successo.</p>

        <div class="stats">
            <?php
            $seconds = isset($_SESSION['elapsed']) ? $_SESSION['elapsed'] : 0;
            $minutes = floor(($seconds % 3600) / 60);
            $remainingSeconds = $seconds % 60;
            ?>
            <p><strong>Tempo impiegato:</strong> <?php echo $minutes . "m " . $remainingSeconds . "s"; ?></p>
            <p><strong>Numero di errori:</strong> <?php echo $_SESSION['sudoku']->errors; ?></p>
            <p><strong>Punteggio:</strong> <?php echo $_SESSION['sudoku']->getPunteggio($_SESSION['elapsed']); ?></p>
        </div>

        <!-- Pulsante per tornare alla home -->
        <a href="home.php" class="btn-home">Torna alla home</a>
    </div>

    <?php
    // Unset the session variables to clear the game data
    unset($_SESSION['sudoku']);
    unset($_SESSION['elapsed']);
    unset($_SESSION['start_time']);
    unset($_SESSION['last_ping']);
    ?>
</body>

</html>
