<?php
$servername = "localhost:3307";
$username = "root";
$password = "";
$database = "salon";

$connection = mysqli_connect($servername, $username, $password, $database);

if (!$connection) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}
?>
