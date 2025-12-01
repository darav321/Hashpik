<?php 
    $token = $_GET['token'];

    $conn = new mysqli("localhost","root","","hashpik");
    $result = $conn->query("SELECT * FROM users WHERE token='$token'");

    if ($result->num_rows > 0) {
        $conn->query("UPDATE users SET is_verified=1, token=NULL WHERE token='$token'");
        echo "Email verified successfully!";
    } else {
        echo "Invalid or expired token!";
    }
?>