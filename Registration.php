<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hashpik Signup</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

</head>

<body class="relative min-h-screen
    before:content-[''] before:absolute before:inset-0 
    before:bg-[radial-gradient(circle_at_center,#FF7112,transparent)]
    before:opacity-30 before:mix-blend-multiply
    bg-white flex justify-center items-center">
    <div class="flex flex-col justify-center items-center gap-6 w-full">
        <?php 
        if(isset($_POST["submit"])) {
            $name = $_POST["name"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $confirm = $_POST["confirm"];
            $errors = array();
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            if(empty($name) OR empty($email) OR empty($password) OR empty($confirm)) {
                array_push($errors, "All fields are required");
            }
            elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($errors, "Email is not valid");
            }
            elseif(strlen($password) < 8)
            {
                array_push($errors, "Password must be at least of 8 characters long");
            }
            elseif($password !== $confirm) 
            {
                array_push($errors, "Password does not match");
            }

            require_once "database.php";
            $sql = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);
            $rowCount = mysqli_num_rows($result);
            if ($rowCount>0) {
                array_push($errors,"Email already exists!");
            }

            if(count($errors) > 0) {
                foreach($errors as $error) {
                    echo "
                    <script>
                        Toastify({
                            text: '$error',
                            duration: 3000,
                            gravity: 'top',
                            position: 'center',
                            close: true,
                            style: {
                                background: '#dc2626',         // red-600
                                border: '2px solid #dc2626',   // red border
                                borderRadius: '8px',           // rounded corners
                                fontWeight: '500',
                                padding: '12px 16px'
                            }
                        }).showToast();
                    </script>
                    ";
                }
            }


            else {
                $sql = "INSERT INTO users (name, email, password) VALUES ( ?, ?, ? )";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt,$sql);
                if ($prepareStmt) { 
                    mysqli_stmt_bind_param($stmt,"sss",$name, $email, $passwordHash);
                    mysqli_stmt_execute($stmt);
                    require 'sendMail.php';
                    $subject = "Welcome to Hashpik";
                    $message = "Now you can get tons of images from different websites on single platform";
                    sendEmail($email, $subject, $message);
                    
                    header("Location: Registration.php?registered=1");
                    exit();
                }else{
                    die("Something went wrong");
                }
            }
        }

    ?>
        <h1 class="text-5xl text-slate-800 font-bold">Welcome to Hashpik</h1>
        <form action="Registration.php" method="POST" autocomplete="off" class="flex bg-[#f5f0ed] z-10 flex-col items-center px-10 py-10 w-[90%] sm:w-1/2 lg:w-1/3 shadow-lg gap-4 rounded-lg">
            <div class="w-full flex flex-col gap-1">
                <h1 class="text-3xl text-slate-800 font-bold">Sign up</h1>
                <p class="font-medium text-sm text-slate-500">All fields are required</p>
            </div>
            <div class="flex flex-col gap-2 w-full">
                <label for="name">Name:</label>
                <input type="text" name="name" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none" autocomplete="off">
            </div>
            <div class="flex flex-col gap-2 w-full">
                <label for="email">Email:</label>
                <input id="email" type="email" name="email" autocomplete="off" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none">
            </div>
            <div class="flex flex-col gap-2 w-full">
                <label for="password">Password:</label>
                <input type="password" name="password" autocomplete="off" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none">
            </div>
            <div class="flex flex-col gap-2 w-full">
                <label for="password">Confirm Password:</label>
                <input type="password" name="confirm" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none">
            </div>
            <button name="submit" class="bg-[#CC774A] hover:shadow-md px-5 py-2 text-white rounded-md mt-2 cursor-pointer">Submit</button>
            <p class="w-full text-left">Already have an account <a href="Login.php" class="text-[#CC774A]">Login</a></p>
        </form>
    </div>
    <?php if (isset($_GET['registered']) && $_GET['registered'] == 1): ?>
    <script>
    Toastify({
    text: "Registration successful! Check your Email...",
    duration: 3000,
    gravity: "top",
    position: "center",
    style: {
        background: "#16a34a",        
        border: "2px solid #16a34a",  
        borderRadius: "8px",          
        fontWeight: "500",            
        padding: "12px 16px"          
    }
}).showToast();
    </script>
    <?php endif; ?>
</body>
</html>