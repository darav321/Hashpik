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
        if(isset($_POST["submit"])) {
            $name = $_POST["name"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $confirm = $_POST["confirm"];
            $errors = array();
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            if(empty($name) OR empty($email) or empty($password) or empty($confirm)) {
                array_push($errors, "All fields are required");
            }   

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($errors, "Email not valid");
            }

            if(strlen($password) < 8) {
                array_push($errors, "Password must be at least of 8 characters long");
            }

            if($password !== $confirm) {
                array_push($errors, "Password does not match");
            }

            require_once "database.php";
            $sql = "Select * from users where email = '$email'";
            $result = mysqli_query($conn, $sql);
            $rowCount = mysqli_num_rows($result);
            if($rowCount > 0) {
                array_push($errors, "Email already exists");
            }

            if(count($errors) > 0) {
                foreach($errors as $error) {
                    echo "<div>$error</div>";
                }
            }
            else {
                $sql = "Insert into users (name, email, password) valus (?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                if($prepareStmt) {
                    mysqli_stmt_bind_param($stmt,"sss",$name, $email, $passwordHash);
                    mysqli_stmt_execute($stmt);
                    echo "<div>You are registered successfully.</div>";
                } else {
                    die("Something went wrong");
                }
            }
        }
    ?>
    <div class="flex flex-col justify-center items-center gap-6 w-full">
        <h1 class="text-5xl text-slate-800 font-bold">Welcome again!</h1>
        <form action="Registration.php" method="post" class="flex bg-[#f5f0ed] z-10 flex-col items-center px-10 py-10 w-[90%] sm:w-1/2 lg:w-1/3 shadow-lg gap-4 rounded-lg">
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
            </div>
            <button name="submit" class="bg-[#CC774A] hover:shadow-md px-5 py-2 text-white rounded-md mt-2 cursor-pointer">Submit</button>
            <p class="w-full text-left">Not registered yet? <a href="Registration.php" class="text-[#CC774A]">Register</a></p>
        </form>
    </div>

</body>
</html>