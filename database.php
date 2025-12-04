<?php 
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "hashpik";

    $conn = mysqli_connect($host, $user, $pass, $dbname);

    if($conn->connect_error) {
        die("Database connection failed". $conn->connect_error);
    }
?>