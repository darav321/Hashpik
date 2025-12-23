<?php
    session_start();
    require_once "database.php";

    header("Content-Type: application/json");

    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $errors = [];

    if (!$email || !$password) {
        $errors[] = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email is not valid";
    }

    if (!empty($errors)) {
        echo json_encode([
            "success" => false,
            "errors"  => $errors
        ]);
        exit;
    }

    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user || !password_verify($password, $user["password"])) {
        echo json_encode([
            "success" => false,
            "errors"  => ["Invalid email or password"]
        ]);
        exit;
    }

    $_SESSION["user"] = true;

    echo json_encode([
        "success" => true,
        "message" => "Login successful"
]);
