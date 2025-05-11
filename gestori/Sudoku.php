<?php
require_once 'GestoreAPI.php';
require_once 'GestoreDatabase.php';
class Sudoku
{

    public $API;
    public $size;
    public $sectorSize;
    public $numbers;
    public $difficulty;

    public $errors;

    public $type;

    public $grid = [];
    public $solution = [];


    public function __construct($type, $size, $difficulty)
    {

        if ($size == "cazzo" || $difficulty == "cazzo") {
            $this->difficulty = "easy";
            $this->size = 9;
            $this->type = $type;
            if ($type != "normal" && $type != "city" && $type != "pois") {
                throw new InvalidArgumentException("Il tipo di sudoku è errato");
            }
            $this->sectorSize = 3;
            $this->errors = 0;
            $this->numbers = range(1, 9);
            $this->API = new GestoreAPI(); // Inizializza l'oggetto GestoreAPI
            $this->newGriglia(); // Genera una nuova griglia di sudoku
            $this->grid = $this->solution;
            $this->grid[0][0] = null;
            return;
        }

        if ($size !== 9 && $size !== 16) {
            throw new InvalidArgumentException("La dimensione della griglia deve essere correta");
        }
        $this->type = $type;
        if ($type != "normal" && $type != "city" && $type != "pois") {
            throw new InvalidArgumentException("Il tipo di sudoku è errato");
        }
        $this->difficulty = $difficulty;
        $this->size = $size;
        $this->errors = 0;
        $this->sectorSize = sqrt($size);
        $this->numbers = range(1, $size);
        $this->API = new GestoreAPI(); // Inizializza l'oggetto GestoreAPI
        $this->newGriglia(); // Genera una nuova griglia di sudoku
    }


    public function newGriglia()
    {
        $response = $this->API->generateSudoku($this->difficulty, $this->size);
        $this->grid = array_chunk($response->puzzle, $this->size)[0];
        $this->solution = array_chunk($response->solution, $this->size)[0];
        //se la dimensione è 16 controllo che i numeri sopra il nove siano trasformati a lettere (10->a 11->b ecc)
        if ($this->size == 16) {
            $this->numToLetter();
        }
        //print_r($this->solution);
    }

    public function numToLetter()
    {
        foreach ($this->grid as $key => $value) {
            foreach ($value as $key2 => $value2) {
                if ($value2 > 9) {
                    $this->grid[$key][$key2] = chr($value2 + 55); // Trasforma i numeri in lettere
                }
            }
        }
        foreach ($this->solution as $key => $value) {
            foreach ($value as $key2 => $value2) {
                if ($value2 > 9) {
                    $this->solution[$key][$key2] = chr($value2 + 55); // Trasforma i numeri in lettere maiuscole
                }
            }
        }
    }

    public function getPoisMask()
    {
        // Funzione per generare una maschera di pois
        // La maschera è una matrice di dimensione size x size
        // Ogni numero distinto nella griglia deve avere un pois
        $mask = array_fill(0, $this->size, array_fill(0, $this->size, 0));
        $usedNumbers = [];
        $count = 0;

        while ($count < $this->size) {
            $row = rand(0, $this->size - 1);
            $col = rand(0, $this->size - 1);
            $value = $this->solution[$row][$col];

            if (!in_array($value, $usedNumbers)) {
            $mask[$row][$col] = 1;
            $usedNumbers[] = $value;
            $count++;
            }
        }
        return $mask;
        
    }


    public function checkNumber($row, $col, $value)
    {
        $row = intval($row);
        $col = intval($col);


        if ($this->isCorrect($row, $col, $value)) {
            if ($this->grid[$row][$col] == null) {
                $this->grid[$row][$col] = $value;
            }
            return true; // The value is correct
        } else {
            $this->errors++;
            return false; // The value is not correct
        }
    }
    public function isCorrect($row, $col, $value)
    {
        //check: se è una lettera la tasformo in maiuscolo
        /*if (is_string($value)) {
        $value = strtoupper($value);
    }*/

        if ($this->solution[$row][$col] == $value) {
            return true;
        }
        return false;
    }

    public function isComplete()
    {
        foreach ($this->grid as $row) {
            foreach ($row as $cell) {
                if ($cell == null) {
                    return false; // Se c'è almeno una cella vuota, il sudoku non è completo
                }
            }
        }
        $_SESSION['elapsed'] += time() - $_SESSION['last_ping'];
        $_SESSION['last_ping'] = time();
        return true; // Se tutte le celle sono piene, il sudoku è completo
    }

    public function saveData()
    {
        $db = GestoreDatabase::getInstance();
        $db->saveSudoku($this, $_SESSION['username'], $_SESSION['elapsed']);
    }


    public function getRow($row, $order)
    {
        if ($order == "asc") {
            return $this->solution[$row]; // Restituisce la riga specificata
        } elseif ($order == "desc") {
            return array_reverse($this->solution[$row]); // Restituisce la riga specificata in ordine inverso
        }
        throw new InvalidArgumentException("L'ordine deve essere 'asc' o 'desc'");
    }


    public function getColumn($col, $order)
    {
        $column = array_column($this->solution, $col); // Extract the column from the solution grid
        if ($order == "asc") {
            return $column; // Return the column in ascending order
        } elseif ($order == "desc") {
            return array_reverse($column); // Return the column in descending order
        }
        throw new InvalidArgumentException("L'ordine deve essere 'asc' o 'desc'");
    }


    public function getCityPOV($array)
    {
        $maxSeen = 0;
        $visibleCount = 0;


        if ($this->size == 16) {
            $array = array_map(function ($value) {
                return is_numeric($value) ? $value : ord(strtoupper($value)) - 55;
            }, $array);
        }
        foreach ($array as $height) {
            if ($height > $maxSeen) {
                $visibleCount++;
                $maxSeen = $height;
            }
        }
        return $visibleCount;
    }

    public function getAllCityPov($position)
    {

        //1 = top, 2 = left, 3 = right, 4 = bottom

        $pov = [];
        if ($position == 1) {
            for ($i = 0; $i < $this->size; $i++) {
                $pov[] = $this->getCityPOV($this->getColumn($i, "asc"));
            }
        } elseif ($position == 2) {
            for ($i = 0; $i < $this->size; $i++) {
                $pov[] = $this->getCityPOV($this->getRow($i, "asc"));
            }
        } elseif ($position == 3) {
            for ($i = 0; $i < $this->size; $i++) {
                $pov[] = $this->getCityPOV($this->getRow($i, "desc"));
            }
        } elseif ($position == 4) {
            for ($i = 0; $i < $this->size; $i++) {
                $pov[] = $this->getCityPOV($this->getColumn($i, "desc"));
            }
        } else {
            throw new InvalidArgumentException("La posizione deve essere 1, 2 o 3");
        }
        return $pov;
    }

    public function getPunteggio($time)
    {
        //funzione che calcola il punteggio in base al tempo e agli errori e alla difficoltà e alla dimensione e al tipo
        //formula: punteggio = (dimensione * 100) / (tempo * errori * difficoltà)

        //difficoltà: easy = 1, medium = 2, hard = 3
        if ($this->difficulty == "easy") {
            $this->difficulty = 2;
        } elseif ($this->difficulty == "medium") {
            $this->difficulty = 1;
        } elseif ($this->difficulty == "hard") {
            $this->difficulty = 0.5;
        }
        //tipo: normal = 1, city = 2, pois = 3
        if ($this->type == "normal") {
            $this->type = 1;
        } elseif ($this->type == "city") {
            $this->type = 2;
        } elseif ($this->type == "pois") {
            $this->type = 2;
        }

        return intval(($this->size * 100) / (($time / 2) * ($this->errors + 1) * $this->difficulty * $this->type));
    }
}
