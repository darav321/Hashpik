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

if (isset($_POST["submit"])) {

    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm  = $_POST["confirm"];

    $errors = [];

    // Basic validations
    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $errors[] = "All fields are required";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match";
    }

    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows > 0) {
        $errors[] = "Email already exists";
    }

    if (!empty($errors)) {
        foreach ($errors as $e) {
            echo "<div style='color:red; margin-bottom:5px;'>$e</div>";
        }
    } else {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $insert = $conn->prepare(
            "INSERT INTO users (name, email, password) 
             VALUES (?, ?, ?)"
        );
        $insert->bind_param("sss", $name, $email, $passwordHash);

        if ($insert->execute()) {

            $subject = "Verify your email";
            $link = "http://hashpik/verify.php?token=$token";   
            $message = "Click this link to verify your email: $link";

            sendEmail($email, $subject, $message);
            

            echo "<div style='color:green; margin-bottom:10px;'>
                    Registration successful! A verification link has been sent to your email.
                  </div>";
        } else {
            echo "<div style='color:red'>Something went wrong. Try again.</div>";
        }
    }
}
?>
    <div class="flex flex-col justify-center items-center gap-6 w-full">
        <h1 class="text-5xl text-slate-800 font-bold">Welcome to Hashpik</h1>
        <form action="send_verification.php" method="POST" class="flex bg-[#f5f0ed] z-10 flex-col items-center px-10 py-10 w-[90%] sm:w-1/2 lg:w-1/3 shadow-lg gap-4 rounded-lg">
            <div class="w-full flex flex-col gap-1">
                <h1 class="text-3xl text-slate-800 font-bold">Sign up</h1>
                <p class="font-medium text-sm text-slate-500">All fields are required</p>
            </div>
            <div class="flex flex-col gap-2 w-full">
                <label for="name">Name:</label>
                <input type="text" name="name" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none">
            </div>
            <div class="flex flex-col gap-2 w-full">
                <label for="email">Email:</label>
                <input id="email" type="email" name="email" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none">
            </div>
            <div class="flex flex-col gap-2 w-full">
                <label for="password">Password:</label>
                <input type="password" name="password" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none">
            </div>
            <div class="flex flex-col gap-2 w-full">
                <label for="password">Confirm Password:</label>
                <input type="password" name="confirm" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none">
            </div>
            <button name="submit" class="bg-[#CC774A] hover:shadow-md px-5 py-2 text-white rounded-md mt-2 cursor-pointer">Submit</button>
            <p class="w-full text-left">Already have an account <a href="Login.php" class="text-[#CC774A]">Login</a></p>
        </form>
    </div>
</body>
</html>