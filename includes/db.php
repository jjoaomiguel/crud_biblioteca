<?php

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "biblioteca_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexao falhou: " . $conn->connect_error);
}

?>