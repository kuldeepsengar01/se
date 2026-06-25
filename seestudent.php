<?php
session_start();
include('navbar.php');

/* ❗ CHECK SESSION */
$session_ok = isset($_SESSION['students']) && !empty($_SESSION['students']);
?>

<!DOCTYPE html>
<html>
<head>
<title>Search Student Seat</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{
    background:#f5f5f5;
    font-family:Arial;
}
.search-box{
    max-width:500px;
    margin:80px auto;
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 6px 12px rgba(0,0,0,0.1);
}
.result-box{
    margin-top:20px;
}
</style>

</head>

<body>

<div class="search-box">

<h4 class="text-center mb-3">
<i class="fa-solid fa-magnifying-glass"></i> Find Your Seat
</h4>

<!-- ❗ SESSION WARNING -->
<?php if(!$session_ok){ ?>
<div class="alert alert-danger text-center">
    ❌ Seating not generated or session expired.<br>
    Please generate seating first.
</div>
<?php } ?>

<form method="post">
    <input type="text" name="roll" class="form-control" placeholder="Enter Roll Number" required>
    <button class="btn btn-primary w-100 mt-3">
        <i class="fa-solid fa-search"></i> Search
    </button>
</form>

<div class="result-box">

<?php
if(isset($_POST['roll']) && $session_ok){

    $roll = trim($_POST['roll']);

    if(isset($_SESSION['students'][$roll])){

        $data = $_SESSION['students'][$roll];
?>

<!-- ✅ SUCCESS -->
<div class="card shadow border-0">
    <div class="card-header bg-success text-white text-center">
        <h5 class="mb-0">
            <i class="fa-solid fa-user-graduate"></i> Student Seat Details
        </h5>
    </div>

    <div class="card-body">
        <p><b>Roll No:</b> <?= $roll ?></p>
        <p><b>Room No:</b> <?= $data['room'] ?></p>
        <p><b>Seat No:</b> <?= $data['seat'] ?></p>
        <p><b>Paper Code:</b> <?= $data['paper'] ?></p>
    </div>
</div>

<?php
    } else {
?>

<!-- ❌ NOT FOUND -->
<div class="card shadow border-0">
    <div class="card-header bg-danger text-white text-center">
        <h5 class="mb-0">
            <i class="fa-solid fa-circle-exclamation"></i> Result
        </h5>
    </div>

    <div class="card-body text-center">
        <p class="text-danger mb-0">
            Student not found.
        </p>
    </div>
</div>

<?php
    }
}
?>

</div>

</div>

</body>
</html>