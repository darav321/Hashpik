<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hashpik Signup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>
<body class="relative min-h-screen
    before:content-[''] before:absolute before:inset-0 
    before:bg-[radial-gradient(circle_at_center,#FF7112,transparent)]
    before:opacity-30 before:mix-blend-multiply
    bg-white flex justify-center items-center">

    <?php 
        require_once "database.php";

        if(isset($_POST["submit"])) {
            $email = $_GET["email"];
            $password = $_POST["password"];
            $conf_password = $_POST["conf-password"];
            $errors = array();

            if(empty($email) or empty($password) or empty($conf_password)) {
                array_push($errors, "All fields are required");
            }   

            if($password !== $conf_password) {
                array_push($errors, "Passwords do not match");
            }

            if(count($errors) == 0) {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $sql = "UPDATE users SET password='$hashed_password' WHERE email='$email'";
                if(mysqli_query($conn, $sql)) {
                    echo "
                    <script>
                        Toastify({
                            text: 'Password updated successfull+',
                            duration: 3000,
                            gravity: 'top',
                            position: 'center',
                            close: true,
                            style: {
                                background: '#16a34a',
                                border: '2px solid #16a34a',
                                borderRadius: '8px',
                                fontWeight: '500',
                                padding: '12px 16px'
                            }
                        }).showToast();
                    </script>
                    ";

                    header("Location: index.php");
                    exit();
                } else {
                    echo "
                    <script>
                        Toastify({
                            text: 'Error updating password. Please try again.',
                            duration: 3000,
                            gravity: 'top',
                            position: 'center',
                            close: true,
                            style: {
                                background: '#dc2626',
                                border: '2px solid #dc2626',
                                borderRadius: '8px',
                                fontWeight: '500',
                                padding: '12px 16px'
                            }
                        }).showToast();
                    </script>
                    ";
                }
            } else {
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
                                background: '#dc2626',
                                border: '2px solid #dc2626',
                                borderRadius: '8px',
                                fontWeight: '500',
                                padding: '12px 16px'
                            }
                        }).showToast();
                    </script>
                    ";
                }
            }
        }
    ?>

<div class="flex flex-col justify-center items-center gap-6 w-full">

    <h1 class="text-5xl text-slate-800 font-bold">Reset Password</h1>

    <form autocomplete="off" method="post" class="flex bg-[#f5f0ed] z-10 flex-col items-center px-10 py-10 w-[90%] sm:w-1/2 lg:w-1/3 shadow-lg gap-4 rounded-lg">

        <div class="flex flex-col gap-2 w-full">
            <label for="password">New Password:</label>
            <input type="password" name="password" autocomplete="new-password" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none">
        </div>

        <div class="flex flex-col gap-2 w-full">
            <label for="password">Confirm Password:</label>
            <input type="password" name="conf-password" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none">
        </div>

        <button type="submit" name="submit" class="bg-[#CC774A] hover:shadow-md px-5 py-2 text-white rounded-md mt-2 cursor-pointer">Submit</button>
        
        <p class="w-full text-left">Not registered yet? 
            <a href="Registration.php" class="text-[#CC774A]">Register</a>
        </p>
    </form>

</div>

</body>
</html>
