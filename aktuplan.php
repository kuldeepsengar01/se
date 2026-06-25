<?php
// include('navbar.php');
session_start();
$conn = mysqli_connect("localhost","root","","seating");
if(!$conn){ die("Database Error"); }

/* PAPER LIST */
$shift1_papers = ["BCE502","BCS055","BEE058","BPS05T","KME055","KCE502","BAG059","BEC057"];
$shift2_papers = ["BCE072","BCS071","BEE071","BMBEMO1","BP702T","MTPE013","MTME013","MTCS102","KME076","KMBNFM02","KCS713","KCE074","KCE070","BEC071","BAU071","BAG073","BME072"];

/* ================= GET STUDENT COUNT ================= */
if(isset($_GET['action']) && $_GET['action']=="getCount"){
    $code = trim($_GET['code']);
    $shift = $_GET['shift'];

    $table = ($shift == "Shift 1") ? "shift1" : "student";

    // Check column exists
    $check = mysqli_query($conn,"SHOW COLUMNS FROM `$table` LIKE '$code'");
    if(mysqli_num_rows($check) == 0){
        echo 0;
        exit;
    }

    $query = "SELECT COUNT(`$code`) as total FROM `$table` 
              WHERE `$code` IS NOT NULL AND `$code`!=''";
    $result = mysqli_query($conn,$query);
    $row = mysqli_fetch_assoc($result);

    echo $row['total'] ?? 0;
    exit;
}

/* ================= GET PAPERS BY DATE (DD-MM-YYYY FIX) ================= */
if(isset($_GET['action']) && $_GET['action']=="getPapersByDate"){

    $inputDate = trim($_GET['date']); // from <input type="date"> (YYYY-MM-DD)
    $shift = $_GET['shift'];

    // Convert input YYYY-MM-DD → DD-MM-YYYY
    $date = date('d-m-Y', strtotime($inputDate));

    $allowed = ($shift == "Shift 1") ? $shift1_papers : $shift2_papers;
    $papers = [];

    $query = "SELECT TRIM(UPPER(PAPERCODE)) as code 
              FROM timetable 
              WHERE TRIM(DATE)='$date'";

    $result = mysqli_query($conn, $query);

    if($result){
        while($row = mysqli_fetch_assoc($result)){
            $code = $row['code'];
            if(in_array($code, $allowed)){
                $papers[] = $code;
            }
        }
    }

    echo json_encode(array_values(array_unique($papers)));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Exam Seating Form</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{ background:linear-gradient(135deg,#4facfe,#00f2fe); min-height:100vh; }
.form-box{ background:white; padding:30px; border-radius:15px; margin-top:50px; box-shadow:0 10px 25px rgba(0,0,0,0.2); }
#examTime{ background:#e9ecef; font-weight:bold; }
</style>
</head>

<body>

<div class="container">
<div class="row justify-content-center">
<div class="col-md-7 col-lg-6">

<div class="form-box">
    import file<input type="file" class="form-control form-control-sm"><br>

<h3 class="text-center mb-4">Exam Seating Form</h3>

<form action="saveplan.php" method="post">

<div class="mb-3">
<label class="form-label">Session</label>
<select class="form-select" name="session" required>
<option value="2023-2024">2023-2024</option>
<option value="2024-2025">2024-2025</option>
<option value="2025-2026">2025-2026</option>
</select>
</div>

<div class="mb-3">
<label class="form-label">Shift</label>
<select class="form-select" name="shift" id="shiftSelect" required>
<option value="">Select Shift</option>
<option value="Shift 1">Shift 1</option>
<option value="Shift 2">Shift 2</option>
</select>
</div>

<div class="mb-3">
<label class="form-label">Exam Time</label>
<input type="text" class="form-control" id="examTime" name="exam_time" readonly required>
</div>

<div class="mb-3">
<label class="form-label">Semester</label>
<select class="form-select" name="semester_type" required>
<option value="Odd Semester">Odd Semester</option>
<option value="Even Semester">Even Semester</option>
</select>
</div>

<div class="mb-3">
<label class="form-label">Exam Date</label>
<input type="date" id="examDate" class="form-control" name="exam_date" required>
</div>

<div class="mb-3">
<label class="form-label">Select Paper Codes</label>
<select class="form-select" id="paperCodes" name="paper_codes[]" multiple required></select>
</div>

<div id="dynamicPaperFields">
<h5 class="text-primary mt-3">Student Count</h5>
</div>

<div class="mb-3">
<h5 class="text-success">Total Students: <span id="totalStudents">0</span></h5>
</div>

<div class="mb-3">
<label class="form-label">Number of Rooms</label>
<input type="number" class="form-control" id="roomCount" name="room_count" min="1" required>
</div>

<div id="roomFields"></div>

<button type="submit" class="btn btn-primary w-100 mt-3">
Generate Seating Plan
</button>

</form>
</div>
</div>
</div>
</div>

<script>
const shiftSelect = document.getElementById("shiftSelect");
const paperSelect = document.getElementById("paperCodes");
const examDate = document.getElementById("examDate");

/* LOAD PAPERS */
function loadPapers(){
    let shift = shiftSelect.value;
    let date = examDate.value;

    if(!shift || !date) return;

    document.getElementById("examTime").value =
        (shift === "Shift 1") 
        ? "09:30 AM - 12:30 PM"
        : "02:00 PM - 05:00 PM";

    fetch(`?action=getPapersByDate&date=${date}&shift=${shift}`)
    .then(res => res.json())
    .then(papers => {
        paperSelect.innerHTML = "";

        if(!papers.length){
            paperSelect.innerHTML = "<option disabled>No papers found</option>";
            return;
        }

        papers.forEach(p => {
            let opt = document.createElement("option");
            opt.value = p;
            opt.textContent = p;
            paperSelect.appendChild(opt);
        });
    });
}

shiftSelect.addEventListener("change", loadPapers);
examDate.addEventListener("change", loadPapers);

/* STUDENT COUNT */
paperSelect.addEventListener("change", function(){

    const selectedOptions = Array.from(this.selectedOptions);
    const dynamicFields = document.getElementById("dynamicPaperFields");

    dynamicFields.innerHTML = '<h5 class="text-primary mt-3">Student Count</h5>';

    let promises = [];

    selectedOptions.forEach(opt => {

        let paper = opt.value;

        let div = document.createElement("div");
        div.className = "mb-2";

        div.innerHTML = `
        <label>Students for ${paper}</label>
        <input type="number" class="form-control" 
        name="paper_students_${paper}" id="count_${paper}">
        `;

        dynamicFields.appendChild(div);

        let p = fetch(`?action=getCount&code=${paper}&shift=${shiftSelect.value}`)
        .then(res => res.text())
        .then(data => {
            let count = parseInt(data) || 0;
            document.getElementById("count_"+paper).value = count;
            return count;
        });

        promises.push(p);
    });

    Promise.all(promises).then(values => {
        let total = values.reduce((a,b)=>a+b,0);
        document.getElementById("totalStudents").textContent = total;
    });
});

/* ROOM FIELDS */
document.getElementById("roomCount").addEventListener("input", function(){

    let count = parseInt(this.value) || 0;
    let roomFields = document.getElementById("roomFields");

    roomFields.innerHTML = "";

    for(let i=1;i<=count;i++){
        let div = document.createElement("div");
        div.className = "border p-2 mb-2 bg-light";

        div.innerHTML = `
        <h5>Room ${i}</h5>
        <input type="text" class="form-control mb-1" placeholder="Room No" name="rooms[${i}][room_number]" required>
        <input type="number" class="form-control mb-1" placeholder="Rows" name="rooms[${i}][rows]" required>
        <input type="number" class="form-control" placeholder="Cols" name="rooms[${i}][cols]" required>
        `;

        roomFields.appendChild(div);
    }
});
</script>

</body>
</html>