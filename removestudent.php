<?php
include('navbar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Remove Student</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card shadow-lg p-4 w-100" style="max-width: 500px;">
    <h3 class="text-center text-primary mb-4">Remove Student</h3>
    <form>
      <!-- Roll Number -->
      <div class="mb-3">
        <label for="rollNo" class="form-label">Roll No</label>
        <input type="text" class="form-control" id="rollNo" placeholder="Enter Roll Number">
      </div>

      <!-- Course Dropdown -->
      <div class="mb-3">
        <label for="course" class="form-label">Course</label>
        <select id="course" class="form-select">
          <option selected disabled>Choose Course</option>
          <option value="BCA">BCA</option>
          <option value="MCA">MCA</option>
          <option value="MBA">MBA</option>
          <option value="BTech">BTech</option>
          <option value="BBA">BBA</option>
          <option value="BPharma">B. Pharma</option>
          <option value="DPharma">D. Pharma</option>
        </select>
      </div>

      <!-- Year & Semester Dropdowns -->
      <div class="row mb-3">
        <div class="col">
          <label for="year" class="form-label">Year</label>
          <select id="year" class="form-select">
            <option selected disabled>Choose Year</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
          </select>
        </div>
        <div class="col">
          <label for="semester" class="form-label">Semester</label>
          <select id="semester" class="form-select">
            <option selected disabled>Choose Semester</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
          </select>
        </div>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn btn-primary w-100">Submit</button>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>