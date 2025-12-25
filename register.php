<?php
    session_start();
    require_once "database.php";

    header("Content-Type: application/json");

    $name     = trim($_POST["name"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm  = $_POST["confirm"] ?? "";

    $errors = [];

    if (!$name || !$email || !$password || !$confirm) {
        $errors[] = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email is not valid";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match";
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = "Email already exists";
    }

    if (!empty($errors)) {
        echo json_encode([
            "success" => false,
            "errors"  => $errors
        ]);
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("sss", $name, $email, $passwordHash);
    $stmt->execute();

    require "sendMail.php";
    sendEmail(
        $email,
        "Welcome to Hashpik",
        "Now you can get tons of images from different websites on a single platform"
    );

    echo json_encode([
        "success" => true,
        "message" => "Registration successful! Check your email."
]);
