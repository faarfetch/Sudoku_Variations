<?php
if (!isset($_SESSION)) {
    session_start();
}


$now = time();

// First time visit: initialize session
if (!isset($_SESSION['start_time'])) {
    $_SESSION['start_time'] = $now;
    $_SESSION['elapsed'] = 0;
    $_SESSION['last_ping'] = $now;
} else {
    // Add time difference to elapsed
    $lastPing = $_SESSION['last_ping'];
    $_SESSION['elapsed'] += $now - $lastPing;
    $_SESSION['last_ping'] = $now;
}

// Return the elapsed time as JSON
echo json_encode([
    "seconds" => $_SESSION['elapsed']
]);
?>
