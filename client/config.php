<?php
$con = mysqli_connect("localhost:3307", "root", "", "salon");

if ($con->connect_error) {
    echo "Failed to connect to MySQL: . $mysqli->connect_error";
    exit();
}
