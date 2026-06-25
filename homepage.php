<?php
include('navbar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Homepage</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body{
background: linear-gradient(135deg,#4facfe,#00f2fe);
min-height:100vh;
}

.card-box{
border-radius:15px;
transition:0.3s;
box-shadow:0 8px 20px rgba(0,0,0,0.2);
}

.card-box:hover{
transform:scale(1.05);
}

.hidden-buttons{
display:none;
}

.dashboard-title{
color:white;
font-weight:bold;
margin-bottom:40px;
}

</style>

</head>

<body>

<div class="container text-center mt-5">

<h1 class="dashboard-title">Exam Duty Management</h1>

<div class="row justify-content-center g-4">

<!-- Create Plan -->
<div class="col-md-3">
<div class="card card-box p-4">
<i class="fa-solid fa-pen-to-square fa-3x text-primary mb-3"></i>

<button onclick="togglePlan()" class="btn btn-primary btn-lg">
Create Plan
</button>

<div id="planButtons" class="hidden-buttons mt-3">

<a href="createplan.php" class="btn btn-success w-100 mb-2">
College Plan
</a>

<a href="aktuplan.php" class="btn btn-warning w-100 mb-2">
AKTU Plan
</a>

<a href="" class="btn btn-dark w-100">
School Plan
</a>

</div>

</div>
</div>

<div class="col-md-3">
<div class="card card-box p-4">

<i class="fa-solid fa-user-graduate fa-3x text-danger mb-3"></i>

<a href="seestudent.php" class="btn btn-danger btn-lg w-100">
See Student
</a>

</div>
</div>

<div class="col-md-3">
<div class="card card-box p-4">

<i class="fa-solid fa-chalkboard-user fa-3x text-warning mb-3"></i>

<a href="duty_teacher.php" class="btn btn-warning btn-lg w-100">
Duty Teacher
</a>

</div>
</div>


<div class="col-md-3">
<div class="card card-box p-4">

<i class="fa-solid fa-list-check fa-3x text-success mb-3"></i>

<a href="see_teacher_duty.php" class="btn btn-success btn-lg w-100">
See Teacher Duty
</a>

</div>
</div>

</div>

</div>

<script>

function togglePlan(){

var x = document.getElementById("planButtons");

if(x.style.display==="none" || x.style.display===""){
x.style.display="block";
}else{
x.style.display="none";
}

}

</script>

</body>
</html>