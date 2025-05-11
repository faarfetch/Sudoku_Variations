<?php
//utilizzata per interfacciarsi con le API di rawg.io
class GestoreAPI
{

    private $__APIKEY = "GxNQuM+pmylHCgMAEgRHKQ==w3abC6JuVl2ZwCKw"; //DA NASCONDERE!!!!!!! // Chiave API per accedere ai servizi di rawg.io

    private $__BASEURLGEN = "https://api.api-ninjas.com/v1/sudokugenerate";
    private $__BASEURLSOL = "https://api.api-ninjas.com/v1/sudokusolve"; // URL base per la generazione e risoluzione del sudoku

    public function __construct() {} // Costruttore vuoto della classe

    public function generateSudoku($difficulty,$Size) // Funzione per ottenere i giochi
    {
        $gridSize = sqrt($Size); // Dimensione della griglia
        $api_url = $this->createURL($difficulty,$gridSize); // Crea l'URL per la richiesta API in base alla difficoltà e alla dimensione della griglia

        return $this->callGenAPI($api_url); // Chiama la funzione callAPI per ottenere il sudoku
        // Restituisce la risposta dell'API
    }

    private function createURL($difficulty,$gridSize) // Funzione per ottenere un sudoku specifico
    {
        $diff_array = array("easy", "medium", "hard");
        $size_array = array("2", "3", "4");

        if (!in_array($difficulty, $diff_array)) { // Se la difficoltà non è valida, impostala su "medium"
            $difficulty = "medium";
        }
        if (!in_array($gridSize, $size_array)) { // Se la dimensione non è valida, impostala su "3"
            $gridSize = "3";
        }
        
        return $this->__BASEURLGEN.'?difficulty=' . $difficulty."&width=".$gridSize."&height=".$gridSize;
    }

    private function callGenAPI($api_url) // Funzione per ottenere un sudoku specifico
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Api-Key: ' . $this->__APIKEY
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_code == 200) {
           
        } else {
            echo "Error: " . $http_code . " " . $response;
        }

        curl_close($ch);
        return json_decode($response);
    }

    private function callSolAPI($api_url) // Funzione per ottenere un sudoku specifico
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Api-Key: ' . $this->__APIKEY,
            'Content-Type: application/json'
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_code == 200) {
            
        } else {
            echo "Error: " . $http_code . " " . $response;
        }

        curl_close($ch);
        return $response; // Restituisce la risposta dell'API
    }


    public function solveSudoku($sudoku) // Funzione per risolvere il sudoku
    {
        $api_url = $this->__BASEURLSOL."?puzzle=".$sudoku; // URL dell'API per la risoluzione del sudoku

        return $this->callSolAPI($api_url);
    }


}