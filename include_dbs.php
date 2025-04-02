<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "apothecare";

// Maak verbinding met de database
$conn = new mysqli($servername, $username, $password, $database);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
