<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "hw1";

$conn = mysqli_connect($host, $username, $password, $database) or die("Errore: " . mysqli_connect_error());

mysqli_set_charset($conn, "utf8mb4") or die("Errore charset: " . mysqli_error($conn));
