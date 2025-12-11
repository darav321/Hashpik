<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

</head>

<body>
    <?php
    if (isset($_POST["register_submit"])) {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $confirm = $_POST["confirm"];
        $errors = array();
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        if (empty($name) or empty($email) or empty($password) or empty($confirm)) {
            array_push($errors, "All fields are required");
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Email is not valid");
        } elseif (strlen($password) < 8) {
            array_push($errors, "Password must be at least of 8 characters long");
        } elseif ($password !== $confirm) {
            array_push($errors, "Password does not match");
        }

        require_once "database.php";
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        $rowCount = mysqli_num_rows($result);
        if ($rowCount > 0) {
            array_push($errors, "Email already exists!");
        }

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                echo "
                    <script>
                        Toastify({
                            text: '$error',
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
            }
        } else {
            $sql = "INSERT INTO users (name, email, password) VALUES ( ?, ?, ? )";
            $stmt = mysqli_stmt_init($conn);
            $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
            if ($prepareStmt) {
                mysqli_stmt_bind_param($stmt, "sss", $name, $email, $passwordHash);
                mysqli_stmt_execute($stmt);
                require 'sendMail.php';
                $subject = "Welcome to Hashpik";
                $message = "Now you can get tons of images from different websites on single platform";
                sendEmail($email, $subject, $message);

                header("Location: index.php?registered=1");
                exit();
            } else {
                die("Something went wrong");
            }
        }
    }

    if (isset($_POST["forgot_email_submit"])) {

        $email = $_POST["forgot_email"];

        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            require 'sendMail.php';
            $subject = "Reset Pssword Link - Hashpik";
            $message = "Click on the link below to reset your password";
            sendEmail($email, $subject, $message);
            exit;
        } else {
            echo "<div class='text-red-600 font-bold'>Email not found</div>";
        }
    }

    if (isset($_POST["loginsubmit"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];
        $errors = array();

        if (empty($email) or empty($password)) {
            array_push($errors, "All fields are required");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Email not valid");
        }

        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

        if ($user) {
            if (password_verify($password, $user["password"])) {
                session_start();
                $_SESSION["user"] = "yes";
                header("Location: index.php?login_success=1");
                exit;
            } else {
                echo "<div class='text-red-600 font-bold'>Password does not match</div>";
            }
        } else {
            echo "<div class='text-red-600 font-bold'>Email does not match</div>";
        }
    }

    ?>
    <header class="w-screen flex justify-around items-center py-5 shadow-md">
        <h1 class="text-3xl text-slate-800 font-bold">hash<span class="text-orange-500">pik</span></h1>
        <ul class="flex gap-8 items-center">
            <?php
            if (isset($_SESSION['user'])) {
                echo '<li><a href="logout.php" class="text-xl font-medium text-slate-600 cursor-pointer hover:text-slate-900">Logout</a></li>';
            } else {
                echo '<li><p id="login-btn" class="text-xl font-medium text-slate-600 cursor-pointer hover:text-slate-900">Login</p></li>';
            }
            ?>
        </ul>
    </header>
    <section class="relative min-h-[calc(100vh-400px)] flex flex-col justify-center items-center text-center px-6">

        <div class="absolute inset-0">
            <div class="relative h-full w-full 
            [&>div]:absolute 
            [&>div]:bottom-0 
            [&>div]:right-0 
            [&>div]:z-[-2] 
            [&>div]:h-full 
            [&>div]:w-full 
            [&>div]:bg-gradient-to-b 
            [&>div]:from-orange-200 
            [&>div]:to-white">
                <div></div>
            </div>
        </div>

        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 leading-tight mb-4 z-10">
            Just one click and you are in<br> the world of Images
        </h1>

        <p class="text-lg md:text-xl text-slate-600 max-w-2xl mb-10 z-10">
            Enter any image name with a <span class="text-orange-500 font-semibold">#</span>
            to discover the best results from the Internet.
        </p>


        <form id="searchForm" action="index.php" method="GET" onsubmit="return checkLoginBeforeSearch()" class="flex w-full max-w-xl z-10">
            <input
                type="text"
                name="search"
                id="searchInput"
                placeholder="#mountains"
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : (isset($_SESSION['pending_search']) ? htmlspecialchars($_SESSION['pending_search']) : ''); ?>"
                class="flex-1 outline-none border border-slate-300 rounded-l-full py-3 px-5 text-lg hover:border-slate-500">
            <input type="hidden" name="force_submit" id="forceSubmit" value="0">
            <button
                type="submit"
                class="bg-orange-500 hover:bg-orange-600 text-white px-8 text-lg font-semibold rounded-r-full transition">
                Search
            </button>
        </form>


    </section>

    <div id="forms" class="hidden fixed top-0 left-0 w-full h-full z-10 bg-black bg-opacity-40 flex justify-center items-center">

        <form action="index.php" method="POST" autocomplete="off" id="registration-form" class=" hidden flex bg-[#f5f0ed] z-10 flex-col items-center px-10 py-10 w-[90%] sm:w-1/2 lg:w-1/3 shadow-lg gap-4 rounded-lg">
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
            <button type="submit" name="register_submit" class="bg-[#CC774A] hover:shadow-md px-5 py-2 text-white rounded-md mt-2 cursor-pointer">Submit</button>
            <p class="w-full text-left">Already have an account <a id="goto-login" class="text-[#CC774A]">Login</a></p>
        </form>

        <form id="login-form" method="post" action="login_handler.php" class="hidden flex bg-[#f5f0ed] z-10 flex-col items-center px-10 py-10 w-[90%] sm:w-1/2 lg:w-1/3 shadow-lg gap-4 rounded-lg">

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

            <button name="loginsubmit" type="submit" class="bg-[#CC774A] hover:shadow-md px-5 py-2 text-white rounded-md mt-2 cursor-pointer">Submit</button>

            <p class="w-full text-left">Not registered yet?
                <a id="goto-register" class="text-[#CC774A]">Register</a>
            </p>
        </form>

        <div id="forgotForm" class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-40 flex justify-center items-center">
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

    <form method="GET" class="flex gap-4 px-10 mt-6">
        <input type="text" name="search" value="<?php echo $_GET['search'] ?? '' ?>"
            placeholder="Search..." class="border px-4 py-2 rounded">

        <select name="filter" class="border px-4 py-2 rounded">
            <option value="">Sort By</option>
            <option value="az">Title A → Z</option>
            <option value="za">Title Z → A</option>
            <option value="likes_high">Most Liked</option>
            <option value="likes_low">Least Liked</option>
            <option value="width_high">Width High → Low</option>
            <option value="width_low">Width Low → High</option>
        </select>

        <button class="bg-orange-500 text-white px-6 py-2 rounded">Apply</button>
    </form>


    <?php

    $allImages = [];

    if (isset($_GET['search'])) {

        $query = urlencode($_GET['search']);
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $filter = $_GET['filter'] ?? ""; // IMPORTANT FIX


        $access_key_unsplash = "CVIUtNuMDIbvCHnYTobrqSAcsEq2muf9-LvNpFH9wjE";
        $url_unsplash = "https://api.unsplash.com/search/photos?query=$query&client_id=$access_key_unsplash&page=$page&per_page=20";

        $response = file_get_contents($url_unsplash);
        $data = json_decode($response, true);

        if (!empty($data['results'])) {
            foreach ($data['results'] as $img) {
                $allImages[] = [
                    "title"  => $img["alt_description"] ?: "",
                    "small"  => $img["urls"]["small"],
                    "full"   => $img["urls"]["full"],
                    "likes"  => $img["likes"],
                    "width"  => $img["width"],
                    "height" => $img["height"],
                    "source" => "unsplash"
                ];
            }
        }



        $access_key_pixabay = "53563226-9f711b527fabe9b621944ae54";
        $url_pixabay = "https://pixabay.com/api/?key=$access_key_pixabay&q=$query&image_type=photo&page=$page&per_page=20";

        $response2 = file_get_contents($url_pixabay);
        $data2 = json_decode($response2, true);

        if (!empty($data2['hits'])) {
            foreach ($data2['hits'] as $img) {

                $allImages[] = [
                    "title"  => $img["tags"],
                    "small"  => $img["previewURL"],
                    "full"   => $img["largeImageURL"],
                    "likes"  => $img["likes"],
                    "width"  => $img["imageWidth"],
                    "height" => $img["imageHeight"],
                    "source" => "pixabay"
                ];
            }
        }

        if ($filter === "az") {
            usort($allImages, fn($a, $b) => strcmp($a["title"], $b["title"]));
        } elseif ($filter === "za") {
            usort($allImages, fn($a, $b) => strcmp($b["title"], $a["title"]));
        } elseif ($filter === "likes_high") {
            usort($allImages, fn($a, $b) => $b["likes"] - $a["likes"]);
        } elseif ($filter === "likes_low") {
            usort($allImages, fn($a, $b) => $a["likes"] - $b["likes"]);
        } elseif ($filter === "width_high") {
            usort($allImages, fn($a, $b) => $b["width"] - $a["width"]);
        } elseif ($filter === "width_low") {
            usort($allImages, fn($a, $b) => $a["width"] - $b["width"]);
        }

        echo '<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-10 px-10">';

        foreach ($allImages as $img) {

            $small = $img["small"];
            $full  = $img["full"];
            $title = htmlspecialchars($img["title"]);

            echo "
        <div class='rounded-xl overflow-hidden shadow-lg'>
            <img src='$small'
                 alt='$title'
                 data-full='$full'
                 data-source='{$img['source']}'
                 class='w-full h-64 object-cover previewImg cursor-pointer'
                 onclick='openImgModal(\"$full\")'>
        </div>";
        }

        echo '</div>';

        $total_pages = 10;
        $start = max(1, $page - 2);
        $end = min($total_pages, $page + 2);

        echo '<div class="flex justify-center gap-3 my-10">';

        if ($page > 1) {
            echo '<a href="?search=' . $_GET['search'] . '&page=' . ($page - 1) . '" 
                class="px-3 py-2 bg-gray-200 rounded">Prev</a>';
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($i == $page) {
                echo '<span class="px-3 py-2 bg-orange-500 text-white rounded">' . $i . '</span>';
            } else {
                echo '<a href="?search=' . $_GET['search'] . '&page=' . $i . '" 
                    class="px-3 py-2 bg-gray-200 rounded">' . $i . '</a>';
            }
        }

        echo '<a href="?search=' . $_GET['search'] . '&page=' . ($page + 1) . '" 
            class="px-3 py-2 bg-gray-200 rounded">Next</a>';

        echo '</div>';
    }

    ?>


    <div id="imgModal" class="hidden fixed inset-0 bg-black bg-opacity-70 flex justify-center items-center z-[999]">
        <div class="relative bg-white p-4 rounded-lg max-w-7xl w-full h-[90vh] flex gap-4">
            <div id="imgWrapper" class=" w-full h-full overflow-auto flex justify-center items-center">
                <img id="modalImg" src="" class="max-w-full max-h-full">
            </div>


            <div class="flex flex-col gap-4 items-end top-0">
                <a id="downloadBtn" download class="px-4 py-2 bg-[#CC774A] text-white rounded">Download</a>
                <button id="closeModal" class="px-4 py-2 bg-red-500 text-white rounded">Close</button>
            </div>
        </div>
    </div>


    <script>
        const loginBtn = document.getElementById("login-btn");
        if (loginBtn) {
            loginBtn.onclick = () => {
                document.getElementById("login-form").classList.remove("hidden");
                document.getElementById("forms").classList.remove("hidden");
            };
        }

        const forgotBtn = document.getElementById("forgotBtn");
        if (forgotBtn) {
            forgotBtn.onclick = () => {
                document.getElementById("forgotForm").classList.remove("hidden");
            };
        }

        const closeBtn = document.getElementById("closeBtn");
        if (closeBtn) {
            closeBtn.onclick = () => {
                document.getElementById("forgotForm").classList.add("hidden");
            };
        }

        const gotoRegister = document.getElementById("goto-register");
        if (gotoRegister) {
            gotoRegister.onclick = () => {
                document.getElementById("login-form").classList.add("hidden");
                document.getElementById("registration-form").classList.remove("hidden");
            };
        }

        const gotoLogin = document.getElementById("goto-login");
        if (gotoLogin) {
            gotoLogin.onclick = () => {
                document.getElementById("registration-form").classList.add("hidden");
                document.getElementById("login-form").classList.remove("hidden");
            };
        }

        const modal = document.getElementById("imgModal");
        const modalImg = document.getElementById("modalImg");
        const downloadBtn = document.getElementById("downloadBtn");
        let scale = 1;

        document.querySelectorAll(".previewImg").forEach(img => {
            img.addEventListener("click", () => {
                modal.classList.remove("hidden");
                const hdImage = img.getAttribute("data-full");
                modalImg.src = hdImage;
                downloadBtn.href = hdImage;
                scale = 1;
                modalImg.style.transform = "scale(1)";
                modalImg.style.maxWidth = "100%";
                modalImg.style.maxHeight = "100%";
            });
        });

        document.getElementById("closeModal").onclick = () => {
            modal.classList.add("hidden");
        };


        imgWrapper.addEventListener("wheel", (e) => {
            e.preventDefault();

            if (e.deltaY < 0) scale += 0.1;
            else scale = Math.max(0.5, scale - 0.1);

            modalImg.style.transform = `scale(${scale})`;
        });


        function checkLoginBeforeSearch() {
            const isLoggedIn = "<?php echo isset($_SESSION['user']) ? '1' : '0'; ?>";
            const forceSubmit = document.getElementById("forceSubmit").value;

            if (isLoggedIn === "1" || forceSubmit === "1") {
                document.getElementById("forceSubmit").value = "0";
                return true;
            }

            const forms = document.getElementById("forms");
            const login = document.getElementById("login-form");
            if (forms) forms.classList.remove("hidden");
            if (login) login.classList.remove("hidden");

            const term = document.getElementById("searchInput").value;
            if (term && term.trim() !== "") {
                fetch("save_search.php?term=" + encodeURIComponent(term)).catch(() => {});
            }

            return false;
        }

        function autoSubmitSearch() {
            document.getElementById("forceSubmit").value = "1";
            document.getElementById("searchForm").submit();
        }
    </script>';

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

    <?php if (isset($_GET['login_success'])): ?>
        <script>
            Toastify({
                text: "Login successful!",
                duration: 3000,
                gravity: "top",
                position: "center",
                style: {
                    background: "#16a34a",
                    borderRadius: "8px",
                    padding: "12px 16px"
                }
            }).showToast();
        </script>
    <?php endif; ?>


</body>

</html>