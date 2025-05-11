<?php
require_once("Utente.php");
if (!isset($_SESSION)) {
    session_start();
}
class GestoreDatabase
{

    private static $instance = null;
    private $conn;

    // Configurazione DB
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'sudoku';

    // Costruttore privato
    private function __construct()
    {
        $this->conn = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->database
        );

        if ($this->conn->connect_error) {
            die("Connessione fallita: " . $this->conn->connect_error);
        }
    }

    // Metodo per ottenere l'istanza
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new GestoreDatabase();
        }
        return self::$instance;
    }

    // Metodo per ottenere la connessione
    private function getConn()
    {
        return $this->conn;
    }

    public function getUtente($username)
    {
        $stmt = $this->conn->prepare("SELECT * FROM utenti WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return Utente::parse($result->fetch_assoc());
    }

    public function registraUtente($username, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM utenti WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return false; // Username already exists
        }

        $hashedPassword = md5($password);
        $stmt = $this->conn->prepare("INSERT INTO utenti (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashedPassword);
        return $stmt->execute();
    }

    public function login($username, $password)
    {
        $hashedPassword = md5($password);
        $stmt = $this->conn->prepare("SELECT * FROM utenti WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $hashedPassword);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // Restituisce il punteggio medio per un utente
    public function getPunteggioMedio($username)
    {
        $idUtente = $this->getUtente($username)->getId();
        $stmt = $this->conn->prepare("SELECT AVG(punteggio) as media FROM sudoku WHERE IdUtente = ?");
        $stmt->bind_param("i", $idUtente);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['media'] ?? 0;
    }

    // Restituisce il punteggio massimo per un utente
    public function getPunteggioMassimo($username)
    {
        $idUtente = $this->getUtente($username)->getId();
        $stmt = $this->conn->prepare("SELECT MAX(punteggio) as massimo FROM sudoku WHERE IdUtente = ?");
        $stmt->bind_param("i", $idUtente);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['massimo'] ?? 0;
    }

    // Restituisce statistiche per ogni tipo di sudoku (tipo, miglior tempo, tempo medio, errori medi, completati per difficoltà)
    public function getStatistichePerTipo($username)
{
    $idUtente = $this->getUtente($username)->getId();

    $query = "
        SELECT 
            type,
            diff,
            size,
            COUNT(*) as completati,
            MIN(velocita) as migliorTempo,
            AVG(velocita) as tempoMedio,
            AVG(errori) as erroriMedi
        FROM sudoku
        WHERE IdUtente = ?
        GROUP BY type, diff, size
        ORDER BY type, diff, size
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $idUtente);
    $stmt->execute();
    $result = $stmt->get_result();

    $statistiche = [];

    while ($row = $result->fetch_assoc()) {
        $type = $row['type'];
        $diff = $row['diff'];
        $size = $row['size'];

        $statistiche[$type][$diff][$size] = [
            'completati' => (int)$row['completati'],
            'migliorTempo' => $row['migliorTempo'],
            'tempoMedio' => $row['tempoMedio'],
            'erroriMedi' => $row['erroriMedi']
        ];
    }

    return $statistiche;
}


    public function saveSudoku($sudoku, $username, $time)
    {
        //database attributes: Id	type	size	diff	velocita	errori	punteggio	IdUtente

        $idUtente = $this->getUtente($username)->getId();
        $punteggio = $sudoku->getPunteggio($time);
        $size = $sudoku->size . "x" . $sudoku->size;


        $stmt = $this->conn->prepare("INSERT INTO sudoku (type, size, diff, velocita, errori, punteggio, IdUtente) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $sudoku->type, $size, $sudoku->difficulty, $time, $sudoku->errors, $punteggio, $idUtente);
        $stmt->execute();
    }
}
