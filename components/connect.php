<?php
$serverName = "40.82.147.6";
$database = "flavorfussion_db";
$username = "sa";
$password = "Diba@123";

try {
    // Attempt to connect to the database
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
