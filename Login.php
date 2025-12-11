<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hashpik Signup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="relative min-h-screen
    before:content-[''] before:absolute before:inset-0 
    before:bg-[radial-gradient(circle_at_center,#FF7112,transparent)]
    before:opacity-30 before:mix-blend-multiply
    bg-white flex justify-center items-center">

<?php 
require_once "database.php";

if(isset($_POST["forgot_email_submit"])) {

    $email = $_POST["forgot_email"];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if($user){
        require 'sendMail.php';
        $subject = "Reset Pssword Link - Hashpik";
        $message = "Click on the link below to reset your password";
        sendEmail($email, $subject, $message);
        exit;
    } else {
        echo "<div class='text-red-600 font-bold'>Email not found</div>";
    }
}

if(isset($_POST["submit"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $errors = array();

    if(empty($email) or empty($password)) {
        array_push($errors, "All fields are required");
    }   
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Email not valid");
    }

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

    if($user) {
        if(password_verify($password, $user["password"])) {
            session_start();
            $_SESSION["user"] = "yes";
            header("Location: index.php");
            exit;
        } else {
            echo "<div class='text-red-600 font-bold'>Password does not match</div>";
        }
    } else {
        echo "<div class='text-red-600 font-bold'>Email does not match</div>";
    }
}
?>

<div class="flex flex-col justify-center items-center gap-6 w-full">

    <h1 class="text-5xl text-slate-800 font-bold">Welcome again!</h1>

    <form method="post" class="flex bg-[#f5f0ed] z-10 flex-col items-center px-10 py-10 w-[90%] sm:w-1/2 lg:w-1/3 shadow-lg gap-4 rounded-lg">
        
        <div class="w-full flex flex-col gap-1">
            <h1 class="text-3xl text-slate-800 font-bold">Sign in</h1>
            <p class="font-medium text-sm text-slate-500">All fields are required</p>
        </div>

        <div class="flex flex-col gap-2 w-full">
            <label for="email">Email:</label>
            <input type="email" name="email" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none">
        </div>

        <div class="flex flex-col gap-2 w-full">
            <label for="password">Password:</label>
            <input type="password" name="password" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none">

            <p id="forgotBtn" class="text-blue-500 hover:text-blue-600 underline cursor-pointer">Forgot Password?</p>
        </div>

        <button name="submit" class="bg-[#CC774A] hover:shadow-md px-5 py-2 text-white rounded-md mt-2 cursor-pointer">Submit</button>
        
        <p class="w-full text-left">Not registered yet? 
            <a href="Registration.php" class="text-[#CC774A]">Register</a>
        </p>
    </form>

    <div id="forgotForm" class="hidden fixed top-0 left-0 w-full h-full z-10 bg-black bg-opacity-40 flex justify-center items-center">
        <form method="post" class="bg-white px-10 py-8 rounded-lg shadow-lg w-lg flex flex-col gap-4">
            <div>
                <h2 class="text-xl font-bold">Reset Password</h2>
                <p class="font-medium text-slate-500 text-sm">A verification mail will be sent to this Email</p>
            </div>
            <input type="email" name="forgot_email" placeholder="Enter your email" class="border-2 px-3 py-2 rounded">
            <button name="forgot_email_submit" class="bg-[#CC774A] text-white py-2 rounded">Send Reset Link</button>
            <p id="closeBtn" class="cursor-pointer text-red-500 underline text-center">Cancel</p>
        </form>
    </div>

</div>

<script>
document.getElementById("forgotBtn").onclick = () => {
    document.getElementById("forgotForm").classList.remove("hidden");
}
document.getElementById("closeBtn").onclick = () => {
    document.getElementById("forgotForm").classList.add("hidden");
}
</script>

</body>
</html>
