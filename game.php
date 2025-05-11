<?php
require_once 'gestori/Sudoku.php';
if (!isset($_SESSION)) {
  session_start();
}


if (isset($_POST['difficulty']) && isset($_POST['size']) && isset($_POST['type'])) {
  $type = $_POST['type'];
  $difficulty = $_POST['difficulty'];
  $size = intval($_POST['size']);
  if ($size != 9 && $size != 16) {
    header("Location: home.php?msg=La dimensione della griglia deve essere 9 o 16");
    exit();
  }
  unset($_SESSION['start_time']);
  //$_SESSION['sudoku'] = new Sudoku("normal","cazzo", "cazzo");
  $_SESSION['sudoku'] = new Sudoku($type, $size, $difficulty);
} else if (isset($_SESSION['sudoku'])) {
} else {
  header("Location: home.php?msg=Devi prima generare un sudoku");
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <?php
  $sudoku = $_SESSION['sudoku'];
  $difficulty = $sudoku->difficulty;
  $size = $sudoku->size;
  $title = "Sudoku ".$sudoku->type. " - " . ucfirst($difficulty) . " - " . $size . "x" . $size;
  echo "<title>$title</title>";
  $grid = $sudoku->grid;
  $gridSize = $sudoku->sectorSize;
  ?>
  <link rel="stylesheet" href="style/gameStyle.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
  <?php include 'header.php'; ?>
  <div class="container">
    <main class="game-main">
      <section class="sudoku-section">
        <div class="container">
          <header class="game-header">
            <?php echo "<h1>$title</h1>"; ?>
          </header>

          <main class="game-main">
            <section class="sudoku-section">
              <div class="sudoku-grid-container">

                <?php

                $city1 = $sudoku->getAllCityPov("1");
                $city2 = $sudoku->getAllCityPov("2");
                $city3 = $sudoku->getAllCityPov("3");
                $city4 = $sudoku->getAllCityPov("4");

                $poisMask = $sudoku->getPoisMask();

                ?>

                <table id="sudoku-grid" class="sudoku-grid">
                  <!-- (top) -->
                  <thead>
                    <tr>
                      <th></th>
                      <?php if ($sudoku->type === "city"): ?>
                        <?php for ($i = 0; $i < $size; $i++): ?>
                          <th><?= $city1[$i] ?></th>
                        <?php endfor; ?>
                      <?php endif; ?>
                    </tr>
                  </thead>

                  <tbody>
                    <?php for ($row = 0; $row < $size; $row++): ?>
                      <tr>
                        <!-- (left) -->
                        <?php if ($sudoku->type === "city"): ?>
                          <th><?= $city2[$row] ?></th>
                        <?php endif; ?>
                        <?php for ($col = 0; $col < $size; $col++):
                          $value = $grid[$row][$col];
                          $readonly = $value !== null ? 'readonly' : '';
                          $cellValue = $value !== null ? $value : '';
                          $rowClass = ($row % $gridSize === 0 && $row !== 0) ? 'border-top' : '';
                          $colClass = ($col % $gridSize === 0 && $col !== 0) ? 'border-left' : '';
                          
                          $poisClass = '';
                          if ($sudoku->type === "pois") {
                            if ($poisMask[$row][$col] == 1) {
                              $poisClass = 'pois';
                            } else {
                              $poisClass = 'not-pois';
                            }
                          }
                          $class = trim("$rowClass $colClass $poisClass");
                        ?>
                          <td class="<?= $class ?>">
                            <input
                              type="text"
                              maxlength="1"
                              value="<?= $cellValue ?>"
                              <?= $readonly ?>
                              data-row="<?= $row ?>"
                              data-col="<?= $col ?>"
                              oninput="handleInput(this)"
                              onkeypress="return isNumberKey(event, <?= $size ?>);"
                              onpaste="return false;">
                          </td>
                        <?php endfor; ?>
                        <!-- (right) -->
                        <?php if ($sudoku->type === "city"): ?>
                          <th><?= $city3[$row] ?></th>
                        <?php endif; ?>
                      </tr>
                    <?php endfor; ?>
                  </tbody>

                  <!-- (bottom) -->
                  <tfoot>
                    <tr>
                      <th></th> <!-- Empty bottom-left corner -->
                      <?php if ($sudoku->type === "city"): ?>
                        <?php for ($i = 0; $i < $size; $i++): ?>
                          <th><?= $city4[$i] ?></th>
                        <?php endfor; ?>
                      <?php endif; ?>
                    </tr>
                  </tfoot>
                </table>
            </section>


            <aside class="game-stats">
              <div class="timer-container">
                <label for="timer">Tempo:</label>
                <div id="timer">Loading...</div>
              </div>
              <div class="error-container">
                <label for="errorCounter">Errori:</label>
                <div id="errorCounter"><?= $sudoku->errors ?></div>
              </div>
            </aside>
          </main>
        </div>








        <script>
          function isNumberKey(evt, max) {
            const charCode = evt.which ? evt.which : evt.keyCode;
            const charStr = String.fromCharCode(charCode);

            // Allow control keys: backspace (8), tab(9) delete(46)
            if (
              charCode === 8 || charCode === 9 ||
              (charCode >= 37 && charCode <= 40) || charCode === 46
            ) {
              return true;
            }
            // Allow letters a through g (case insensitive)
            if (/^[a-gA-G]$/.test(charStr)) {
              return true;
            }

            // Only allow digits 1 to max (converted to string)
            if (/\d/.test(charStr)) {
              const digit = parseInt(charStr, 10);
              if (digit >= 1 && digit <= max) {
                return true;
              }
            }
            evt.preventDefault();
            return false;
          }

          function checkCell(row, col, number, inputElement) {
            //print number

            fetch('ajax/check_cell.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                  row: row,
                  col: col,
                  number: number
                })
              })
              .then(res => res.json())
              .then(data => {
                if (data.correct) {
                  inputElement.classList.add("correct");
                  inputElement.classList.remove("wrong");
                } else {
                  inputElement.classList.add("wrong");
                  inputElement.classList.remove("correct");
                  const errorCounter = document.getElementById("errorCounter");
                  errorCounter.textContent = parseInt(errorCounter.textContent) + 1;
                }
              });
          }

          function handleInput(input) {
            const row = parseInt(input.dataset.row);
            const col = parseInt(input.dataset.col);

            // Normalize input to uppercase
            input.value = input.value.toUpperCase();

            // Remove any non-digit and non-a-g characters immediately
            input.value = input.value.replace(/[^1-9A-G]/g, '');

            // Only allow one character max
            if (input.value.length > 1) {
              input.value = input.value.charAt(0);
            }

            // Clear previous state
            input.classList.remove("correct", "wrong");

            const number = input.value;
            const maxNumber = <?= $size ?>;

            const isValidNumber = (number >= '1' && number <= '9') ||
              (maxNumber === 16 && /^[A-G]$/.test(number));

            if (isValidNumber) {
              checkCell(row, col, number, input);

              fetch('ajax/check_Complete.php')
                .then(res => res.json())
                .then(data => {
                  if (data.complete) {
                    window.location.href = "fine.php";
                  }
                });

            } else if (number !== '') {
              input.classList.add("wrong");
            }
          }
        </script>

        <script>
          async function fetchTime() {
            const res = await fetch('ajax/time.php');
            const data = await res.json();
            let seconds = data.seconds;

            setInterval(() => {
              seconds++;
              const mins = Math.floor((seconds % 3600) / 60);
              const secs = seconds % 60;
              document.getElementById("timer").textContent = `${mins}m ${secs}s`;
            }, 1000);
          }

          fetchTime();
        </script>


</body>

</html>