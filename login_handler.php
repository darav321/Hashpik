<?php
session_start();
require "database.php";

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {

    $_SESSION['user'] = $user;

    if (isset($_SESSION['pending_search'])) {
        $term = $_SESSION['pending_search'];
        unset($_SESSION['pending_search']);
        header("Location: index.php?search=$term&login_success=1");
        exit();
    }

    header("Location: index.php?login_success=1");
    exit();

} else {
    header("Location: index.php?login_failed=1");
    exit();
}
?>

<?php if (isset($_GET['login_success'])): ?>
<script>
Toastify({
    text: "Login successful!",
    duration: 3000,
    gravity: "top",
    position: "center",
    close: true,
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

