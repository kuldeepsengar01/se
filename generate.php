<?php
include("db.php");

$roomNo = trim($_POST['roomNo']);
$seatCount = (int)$_POST['seatCount'];
$courseA = $_POST['courseA'];
$courseB = $_POST['courseB'];
$yearA = $_POST['yearA'];
$yearB = $_POST['yearB'];
$semesterA = $_POST['semesterA'];
$semesterB = $_POST['semesterB'];

if ($courseA === $courseB) {
    die("Course A and Course B must be different.");
}

// Fetch students for Course A with Year & Semester
$stmtA = $conn->prepare("SELECT * FROM student WHERE Course=? AND Year=? AND Semester=?");
$stmtA->bind_param("sss", $courseA, $yearA, $semesterA);
$stmtA->execute();
$resultA = $stmtA->get_result();
$studentsA = $resultA->fetch_all(MYSQLI_ASSOC);

// Fetch students for Course B with Year & Semester
$stmtB = $conn->prepare("SELECT * FROM student WHERE Course=? AND Year=? AND Semester=?");
$stmtB->bind_param("sss", $courseB, $yearB, $semesterB);
$stmtB->execute();
$resultB = $stmtB->get_result();
$studentsB = $resultB->fetch_all(MYSQLI_ASSOC);

$conn->close();

// Shuffle for randomness
shuffle($studentsA);
shuffle($studentsB);

// Prepare benches
$benches = [];
for ($i = 0; $i < $seatCount; $i++) {
    $leftStudent = isset($studentsA[$i]) ? $studentsA[$i] : null;
    $rightStudent = isset($studentsB[$i]) ? $studentsB[$i] : null;

    $benches[] = [
        'left' => $leftStudent,
        'right' => $rightStudent
    ];
}

// Count assigned students
$courseCount = [
    $courseA => min(count($studentsA), $seatCount),
    $courseB => min(count($studentsB), $seatCount)
];
$totalAssigned = array_sum($courseCount);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Room Seating - <?php echo htmlspecialchars($roomNo); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .college-header {
            background: #0d6efd;
            color: #fff;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .table th {
            background: #0d6efd;
            color: #fff;
            text-align: center;
        }
        .badge-course {
            font-size: 0.95rem;
            padding: 0.45em 0.8em;
        }
        @media print {
            button { display: none !important; }
        }
    </style>
</head>
<body>
<div class="container py-4">

    <!-- College Header -->
    <div class="college-header">
        <h2 class="mb-0">Aligarh College of Engineering and Technology</h2>
        <small>Room: <?php echo htmlspecialchars($roomNo); ?> | Total Seats: <?php echo $seatCount; ?></small>
    </div>

    <!-- Course Count Badges -->
    <div class="text-center mb-4">
        <?php foreach ($courseCount as $course => $count): ?>
            <span class="badge bg-info text-dark badge-course mx-1">
                <?php echo htmlspecialchars($course); ?> : <?php echo $count; ?> student(s)
            </span>
        <?php endforeach; ?>
        <span class="badge bg-success badge-course mx-1">Total Seats: <?php echo $totalAssigned; ?></span>
    </div>

    <!-- Seating Table -->
    <div class="table-responsive shadow rounded bg-white p-3">
    <table class="table table-bordered table-hover text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>Bench No</th>
                <th>Left Roll No</th>
                <th>Left Name</th>
                <th>Left Course</th>
                <th>Left Year</th>
                <th>Left Semester</th>
                <th>Left Papercode</th>
                <th>Right Roll No</th>
                <th>Right Name</th>
                <th>Right Course</th>
                <th>Right Year</th>
                <th>Right Semester</th>
                <th>Right Papercode</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($benches as $index => $bench): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>

                <!-- Left student -->
                <td><?php echo $bench['left'] ? $bench['left']['Rollno'] : '(...)'; ?></td>
                <td><?php echo $bench['left'] ? htmlspecialchars($bench['left']['Name']) : '(...)'; ?></td>
                <td><?php echo $bench['left'] ? htmlspecialchars($bench['left']['Course']) : '(...)'; ?></td>
                <td><?php echo $bench['left'] ? htmlspecialchars($bench['left']['Year']) : '(...)'; ?></td>
                <td><?php echo $bench['left'] ? htmlspecialchars($bench['left']['Semester']) : '(...)'; ?></td>
                <td><?php echo $bench['left'] ? htmlspecialchars($bench['left']['Papercode']) : '(...)'; ?></td>

                <!-- Right student -->
                <td><?php echo $bench['right'] ? $bench['right']['Rollno'] : '(...)'; ?></td>
                <td><?php echo $bench['right'] ? htmlspecialchars($bench['right']['Name']) : '(...)'; ?></td>
                <td><?php echo $bench['right'] ? htmlspecialchars($bench['right']['Course']) : '(...)'; ?></td>
                <td><?php echo $bench['right'] ? htmlspecialchars($bench['right']['Year']) : '(....)'; ?></td>
                <td><?php echo $bench['right'] ? htmlspecialchars($bench['right']['Semester']) : '(...)'; ?></td>
                <td><?php echo $bench['right'] ? htmlspecialchars($bench['right']['Papercode']) : '(...)'; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <div class="text-center mt-4">
        <button onclick="window.print()" class="btn btn-primary btn-lg">Print Seating</button>
    </div>

</div>
</body>
</html>