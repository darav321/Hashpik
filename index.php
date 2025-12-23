<?php
session_start();
if (isset($_POST["forgot_email_submit"])) {
    require_once "database.php";
    $email = $_POST["forgot_email"];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        require 'sendMail.php';
        $subject = "Reset Pssword Link - Hashpik";
        $link = "https://localhost/Hashpik/reset_password.php?email=" . urlencode($email);
        $message = "Click on the link below to reset your password" . "\n" . $link;
        sendEmail($email, $subject, $message);
    } else {
        echo "<div class='text-red-600 font-bold'>Email not found</div>";
    }
}
?>
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

            header("Location: reset_password.php?email=" . urlencode($email));
            exit();
        } else {
            echo "<div class='text-red-600 font-bold'>Email not found</div>";
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


        <form id="searchForm" class="flex w-full max-w-xl z-10">
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

        <form autocomplete="off" id="registration-form" class=" hidden flex bg-[#f5f0ed] z-10 flex-col items-center px-10 py-10 w-[90%] sm:w-1/2 lg:w-1/3 shadow-lg gap-4 rounded-lg">
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
                <input type="password" name="password" autocomplete="off" autocomplete="new-password" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none">
            </div>
            <div class="flex flex-col gap-2 w-full">
                <label for="password">Confirm Password:</label>
                <input type="password" name="confirm" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none">
            </div>
            <button type="submit" name="register_submit" class="bg-[#CC774A] hover:shadow-md px-5 py-2 text-white rounded-md mt-2 cursor-pointer">Submit</button>
            <p class="w-full text-left">Already have an account <a id="goto-login" class="text-[#CC774A]">Login</a></p>
        </form>

        <form autocomplete="off" id="login-form" class="hidden flex bg-[#f5f0ed] z-10 flex-col items-center px-10 py-10 w-[90%] sm:w-1/2 lg:w-1/3 shadow-lg gap-4 rounded-lg">

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
                <input type="password" name="password" autocomplete="new-password" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none">

                <p id="forgotBtn" class="text-blue-500 hover:text-blue-600 underline cursor-pointer">Forgot Password?</p>
            </div>

            <button name="loginsubmit" type="submit" class="bg-[#CC774A] hover:shadow-md px-5 py-2 text-white rounded-md mt-2 cursor-pointer">Submit</button>

            <p class="w-full text-left">Not registered yet?
                <a id="goto-register" class="text-[#CC774A]">Register</a>
            </p>
        </form>

    </div>
    <div id="forgotForm" class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-40 flex justify-center items-center z-10">
        <form method="post" action="index.php" class="bg-white px-10 py-8 rounded-lg shadow-lg w-lg flex flex-col gap-4">
            <div>
                <h2 class="text-xl font-bold">Reset Password</h2>
                <p class="font-medium text-slate-500 text-sm">A verification mail will be sent to this Email</p>
            </div>
            <input type="email" name="forgot_email" placeholder="Enter your email" class="border-2 px-3 py-2 rounded">
            <button type="submit" name="forgot_email_submit" class="bg-[#CC774A] text-white py-2 rounded">Send Reset Link</button>
            <p id="closeBtn" class="cursor-pointer text-red-500 underline text-center">Cancel</p>
        </form>
    </div>

    <form method="GET" class="w-full max-w-none flex flex-wrap gap-4 px-6 mt-6 justify-start">
        <input type="text" name="search" value="<?php echo $_GET['search'] ?? '' ?>"
            placeholder="Search..." class="border px-4 py-2 rounded">

        <select name="filter" class="border px-4 py-2 rounded">
            <option value="">Sort By</option>
            <option value="az" <?= $filter == "az" ? "selected" : "" ?>>Title A → Z</option>
            <option value="za" <?= $filter == "za" ? "selected" : "" ?>>Title Z → A</option>
            <option value="likes_high" <?= $filter == "likes_high" ? "selected" : "" ?>>Most Liked</option>
            <option value="likes_low" <?= $filter == "likes_low" ? "selected" : "" ?>>Least Liked</option>
            <option value="width_high" <?= $filter == "width_high" ? "selected" : "" ?>>Width High → Low</option>
            <option value="width_low" <?= $filter == "width_low" ? "selected" : "" ?>>Width Low → High</option>
        </select>


        <button class="bg-orange-500 text-white px-6 py-2 rounded">Apply</button>
    </form>

    <section class="w-full px-4">

        <div id="loader"
            class="hidden w-full grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-4 gap-6 px-6 mt-10">

            <?php for ($i = 0; $i < 12; $i++): ?>
                <div class="h-64 bg-gray-200 animate-pulse rounded-xl"></div>
            <?php endfor; ?>

        </div>

        <div id="imageGrid"
            class="w-full grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-6 px-6 mt-10">
        </div>

    </section>



    <div id="pagination" class="w-full flex justify-center gap-6 my-12 hidden">
        <button onclick="prevPage()" class="px-4 py-2 bg-gray-200 rounded">Prev</button>
        <button onclick="nextPage()" class="px-4 py-2 bg-gray-200 rounded">Next</button>
    </div>

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

        const registerForm = document.getElementById("registration-form");

        registerForm.addEventListener("submit", function(e) {
            e.preventDefault();

            const formData = new FormData(registerForm);

            fetch("register.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        data.errors.forEach(err => {
                            Toastify({
                                text: err,
                                duration: 3000,
                                gravity: "top",
                                position: "center",
                                style: {
                                    background: "#dc2626",
                                    borderRadius: "8px",
                                    padding: "12px 16px"
                                }
                            }).showToast();
                        });
                        return;
                    }

                    Toastify({
                        text: data.message,
                        duration: 3000,
                        gravity: "top",
                        position: "center",
                        style: {
                            background: "#16a34a",
                            borderRadius: "8px",
                            padding: "12px 16px"
                        }
                    }).showToast();

                    document.getElementById("forms").classList.add("hidden");
                    registerForm.reset();

                })
                .catch(() => {
                    Toastify({
                        text: "Something went wrong",
                        duration: 3000,
                        gravity: "top",
                        position: "center",
                        style: {
                            background: "#dc2626"
                        }
                    }).showToast();
                })
        })

        const loginForm = document.getElementById("login-form");

        loginForm.addEventListener("submit", function(e) {
            e.preventDefault();

            fetch("login.php", {
                    method: "POST",
                    body: new FormData(loginForm)
                })
                .then(res => res.json())
                .then(data => {

                    if (!data.success) {
                        data.errors.forEach(err => {
                            Toastify({
                                text: err,
                                duration: 3000,
                                gravity: "top",
                                position: "center",
                                style: {
                                    background: "#dc2626"
                                }
                            }).showToast();
                        });
                        return;
                    }

                    Toastify({
                        text: data.message,
                        duration: 3000,
                        gravity: "top",
                        position: "center",
                        style: {
                            background: "#16a34a"
                        }
                    }).showToast();

                    document.getElementById("forms").classList.add("hidden");
                    loginForm.reset();

                    window.isLoggedIn = true;
                })
                .catch(() => {
                    Toastify({
                        text: "Something went wrong",
                        duration: 3000,
                        gravity: "top",
                        position: "center",
                        style: {
                            background: "#dc2626"
                        }
                    }).showToast();
                });
        });


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

        document.addEventListener("click", function(e) {
            const img = e.target.closest(".previewImg");
            if (!img) return;

            modal.classList.remove("hidden");

            const hdImage = img.getAttribute("data-full");
            modalImg.src = hdImage;
            downloadBtn.href = hdImage;

            scale = 1;
            modalImg.style.transform = "scale(1)";
            modalImg.style.maxWidth = "100%";
            modalImg.style.maxHeight = "100%";
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

        window.isLoggedIn = "<?php echo isset($_SESSION['user']) ? '1' : '0'; ?>";

        function checkLoginBeforeSearch() {
            if (window.isLoggedIn === true || window.isLoggedIn === "1") {
                return true;
            }

            document.getElementById("forms").classList.remove("hidden");
            document.getElementById("login-form").classList.remove("hidden");
            return false;
        }

        function autoSubmitSearch() {
            document.getElementById("forceSubmit").value = "1";
            document.getElementById("searchForm").submit();
        }

        document.getElementById("forgotBtn").onclick = () => {
            document.getElementById("forgotForm").classList.remove("hidden");
            document.getElementById("login-form").classList.add("hidden");
        }

        document.getElementById("closeBtn").onclick = () => {
            document.getElementById("forgotForm").classList.add("hidden");
            document.getElementById("login-form").classList.remove("hidden");
        }

        let currentPage = 1;

        function loadImages(page = 1) {
            currentPage = page;

            const searchInput = document.querySelector('input[name="search"]');
            const filterSelect = document.querySelector('select[name="filter"]');
            const p_btn = document.getElementById("pagination");

            const search = searchInput ? searchInput.value.trim() : "";
            const filter = filterSelect ? filterSelect.value : "";

            if (!search) return;

            const loader = document.getElementById("loader");
            const grid = document.getElementById("imageGrid");

            loader.classList.remove("hidden");
            grid.classList.add("hidden");
            p_btn.classList.add("hidden");

            fetch(`fetch_images.php?search=${encodeURIComponent(search)}&page=${page}&filter=${encodeURIComponent(filter)}`)
                .then(res => res.text())
                .then(html => {
                    grid.innerHTML = html;
                })
                .catch(err => {
                    console.error(err);
                    grid.innerHTML =
                        "<p class='text-red-500 text-center col-span-full'>Failed to load images</p>";
                })
                .finally(() => {
                    loader.classList.add("hidden");
                    grid.classList.remove("hidden");
                    p_btn.classList.remove("hidden");
                });
        }


        document.querySelector('select[name="filter"]').addEventListener("change", () => {
            loadImages(1);
        });

        function nextPage() {
            loadImages(currentPage + 1);
        }

        function prevPage() {
            if (currentPage > 1) loadImages(currentPage - 1);
        }

        if (new URLSearchParams(window.location.search).get("search")) {
            loadImages(1);
        }

        document.getElementById("searchForm").addEventListener("submit", (e) => {
            e.preventDefault();
            if (!checkLoginBeforeSearch()) return;

            loadImages(1);
        });
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