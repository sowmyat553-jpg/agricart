<?php

$conn = new mysqli("localhost", "root", "", "agricart");

if ($conn->connect_error) {
    die("Database Connection Failed");
}

$conn->set_charset("utf8mb4");

?>