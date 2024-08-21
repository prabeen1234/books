<?php

$hostname = "localhost";
$username = "root"; 
$password = "prabin@123"; 
$database = "book";     

$connection = mysqli_connect($hostname, $username, $password, $database);

if (mysqli_connect_errno()) {
    die("Failed to connect to the database: " . mysqli_connect_error());
}


?>
