<?php 
    $host = "db";
    $user = "hashpik_user";
    $pass = "hashpik_pass";
    $dbname = "hashpik";

    $conn = mysqli_connect($host, $user, $pass, $dbname);

    if($conn->connect_error) {
        die("Database connection failed". $conn->connect_error);
    }
?>