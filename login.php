<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "seating";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['submit'])) {

    $inputrole = $_POST['role'];
    $inputuser = $_POST['username'];
    $inputpass = $_POST['password'];

    /* ================= ADMIN LOGIN ================= */
    if ($inputrole == "admin") {

        $stmt = $conn->prepare("SELECT * FROM admin WHERE Username=? AND Password=?");
        $stmt->bind_param("ss", $inputuser, $inputpass);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $_SESSION['user'] = $inputuser;
            $_SESSION['role'] = "admin";

            header("Location: homepage.php");
            exit();

        } else {
            header("Location: index.php?error=1");
            exit();
        }
    }

    /* ================= STAFF LOGIN ================= */
    if ($inputrole == "staff") {

        $stmt = $conn->prepare("SELECT * FROM staff WHERE username=? AND password=?");
        $stmt->bind_param("ss", $inputuser, $inputpass);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $_SESSION['user'] = $inputuser;
            $_SESSION['role'] = "staff";

            header("Location: staff.php");
            exit();

        } else {
            header("Location: index.php?error=1");
            exit();
        }
    }
}
?>