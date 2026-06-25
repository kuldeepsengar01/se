<?php
$host="localhost";
$user="root";
$pass="";
$db="seating";
$conn = new mysqli($host,$user,$pass,$db);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $courseA = $_POST['courseA'];
    $yearA = $_POST['yearA'];
    $semesterA = $_POST['semesterA'];

    $courseB = $_POST['courseB'];
    $yearB = $_POST['yearB'];
    $semesterB = $_POST['semesterB'];

    $roomNo = $_POST['roomNo'];
    $seatCount = $_POST['seatCount'];

    // Fetch Group A
    $stmtA = $conn->prepare("SELECT * FROM student WHERE Course=? AND Year=? AND Semester=?");
    $stmtA->bind_param("sss", $courseA, $yearA, $semesterA);
    $stmtA->execute();
    $resultA = $stmtA->get_result();
    $studentsA = $resultA->fetch_all(MYSQLI_ASSOC);

    // Fetch Group B
    $stmtB = $conn->prepare("SELECT * FROM student WHERE Course=? AND Year=? AND Semester=?");
    $stmtB->bind_param("sss", $courseB, $yearB, $semesterB);
    $stmtB->execute();
    $resultB = $stmtB->get_result();
    $studentsB = $resultB->fetch_all(MYSQLI_ASSOC);

    $conn->close();

    shuffle($studentsA);
    shuffle($studentsB);

    $seating = [];
    $i = 0;
    $j = 0;

    while (count($seating) < $seatCount && ($i < count($studentsA) || $j < count($studentsB))) {

        if ($i < count($studentsA)) {
            $seating[] = $studentsA[$i++];
        }

        if ($j < count($studentsB) && count($seating) < $seatCount) {
            $seating[] = $studentsB[$j++];
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seating Arrangement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h3 class="text-center">Room No: <?php echo $roomNo; ?></h3>

    <table class="table table-bordered text-center mt-4">
        <thead class="table-dark">
            <tr>
                <th>Seat No</th>
                <th>Roll No</th>
                <th>Name</th>
                <th>Course</th>
                <th>Year</th>
                <th>Semester</th>
                <th>Paper Code</th>
            </tr>
        </thead>
        <tbody>

        <?php
        $seatNo = 1;
        foreach ($seating as $student) {
            echo "<tr>
                    <td>".$seatNo++."</td>
                    <td>".$student['Rollno']."</td>
                    <td>".$student['Name']."</td>
                    <td>".$student['Course']."</td>
                    <td>".$student['Year']."</td>
                    <td>".$student['Semester']."</td>
                    <td>".$student['Papercode']."</td>
                  </tr>";
        }
        ?>

        </tbody>
    </table>
</div>

</body>
</html>