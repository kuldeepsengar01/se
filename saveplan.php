<?php
session_start();
include('navbar.php');

$conn = mysqli_connect("localhost","root","","seating");
if(!$conn){ die("Database connection failed"); }

unset($_SESSION['students']);

/* FORM DATA */
$session = $_POST['session'] ?? '';
$semester_type = $_POST['semester_type'] ?? '';
$exam_time = $_POST['exam_time'] ?? '';
$shift = $_POST['shift'] ?? '';
$date  = $_POST['exam_date'] ?? '';
$rooms = $_POST['rooms'] ?? [];

/* DATE FORMAT */
$formatted_date = !empty($date) ? date("d-m-Y", strtotime($date)) : '';

/* HEADING */
$heading = "AKTU EXAMINATION";
$sem = strtolower(trim($semester_type));

if($sem == "odd semester"){
    $heading = "AKTU ODD SEMESTER EXAMINATION";
}
else if($sem == "even semester"){
    $heading = "AKTU EVEN SEMESTER EXAMINATION";
}

/* PAPERS */
$papers = [];
if(isset($_POST['paper_codes'])){
    foreach($_POST['paper_codes'] as $code){
        $field="paper_students_".$code;
        if(isset($_POST[$field])){
            $count=intval($_POST[$field]);
            if($count>0){
                $papers[$code]=$count;
            }
        }
    }
}

/* SHIFT BASED TABLE */
$table = ($shift == "Shift 1") ? "shift1" : "student";

/* ================= COLUMN AUTO CREATE ================= */

function ensureColumnExists($conn, $table, $column){
    $checkQuery = "SHOW COLUMNS FROM `$table` LIKE '$column'";
    $result = mysqli_query($conn, $checkQuery);

    if(mysqli_num_rows($result) == 0){
        $alterQuery = "ALTER TABLE `$table` ADD `$column` VARCHAR(50) NULL";
        mysqli_query($conn, $alterQuery);
    }
}

/* FETCH STUDENTS */
$all_students=[];
foreach($papers as $code=>$count){

    //  ensure column exists
    ensureColumnExists($conn, $table, $code);

    $q="SELECT `$code` FROM `$table`
        WHERE `$code` IS NOT NULL AND `$code`!=''
        LIMIT $count";

    $res=mysqli_query($conn,$q);

    while($row=mysqli_fetch_assoc($res)){
        if(!empty($row[$code])){
            $all_students[$code][]=$row[$code]." ($code)";
        }
    }
}

/* ================= FINAL LOGIC (MAX CODE LEFT START) ================= */

$paper_keys = array_keys($all_students);

/* FIND MAX STUDENT CODE */
$main_paper = null;
$max_count = 0;

foreach($all_students as $code => $students){
    if(count($students) > $max_count){
        $max_count = count($students);
        $main_paper = $code;
    }
}

/* POINTERS */
$pointers=[];
foreach($paper_keys as $p){
    $pointers[$p]=0;
}

/* ACTIVE PAPERS */
function activePapers($ptr,$all){
    $c=0;
    foreach($all as $k=>$arr){
        if($ptr[$k] < count($arr)) $c++;
    }
    return $c;
}

/* GET NEXT PAPER */
function getNextPaper($paper_keys,$pointers,$all_students,$exclude=[]){
    foreach($paper_keys as $p){
        if(in_array($p,$exclude)) continue;
        if($pointers[$p] < count($all_students[$p])){
            return $p;
        }
    }
    return null;
}


/* GLOBAL CONTROL */
$current_left_paper = $main_paper; // start from max paper
$right_paper = null;
$seat_counter = 0;

$room_grids=[];

/* SEATING */
foreach($rooms as $roomData){

    $room=$roomData['room_number'];
    $rows=intval($roomData['rows']);
    $cols=intval($roomData['cols']);

    for($c=0;$c<$cols;$c++){
        for($r=0;$r<$rows;$r++){

            $left="";
            $right="";

            /* LEFT CONTINUE */
            if($current_left_paper === null || 
               $pointers[$current_left_paper] >= count($all_students[$current_left_paper])){
                
                /* जब current खत्म → नया LEFT */
                $current_left_paper = getNextPaper($paper_keys,$pointers,$all_students);
                $right_paper = null;
            }

            /* ASSIGN LEFT */
            if($current_left_paper !== null){
                $left = $all_students[$current_left_paper][$pointers[$current_left_paper]];
                $pointers[$current_left_paper]++;
            }

            $active = activePapers($pointers,$all_students);

            /* RIGHT WITH GAP */
            if($seat_counter % 2 == 0 && $active > 1){

                if($right_paper === null || 
                   $pointers[$right_paper] >= count($all_students[$right_paper])){

                    $right_paper = getNextPaper(
                        $paper_keys,
                        $pointers,
                        $all_students,
                        [$current_left_paper]
                    );
                }

                if($right_paper !== null){
                    $right = $all_students[$right_paper][$pointers[$right_paper]];
                    $pointers[$right_paper]++;
                }

            } else {
                $right = "";
            }

            $seat_counter++;

            $room_grids[$room][$r][$c]=[$left,$right];
        }
    }
}

/* ================= END ================= */

?>

<!DOCTYPE html>
<html>
<head>
<title>AKTU Seating Plan</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<style>
body{background:#f5f5f5;font-family:Arial;}
.seating-container{background:white;padding:20px;border-radius:10px;box-shadow:0 6px 12px rgba(0,0,0,0.1);margin-top:30px;position:relative;}
.top-buttons{position:absolute;right:10px;top:10px;}
.my-btn{padding:4px 8px;font-size:11px;margin-left:5px;}
.seat-table{border-collapse:separate;border-spacing:40px 10px;}
.seat-table td{width:110px;height:90px;border:1px solid #999;text-align:center;}
.seat-number{font-weight:bold;color:#0d6efd;font-size:12px;}
.seat-box{
    display:flex;
    width:100%;
}

/* BASE */
.seat-part{
    width:85%;
    min-height:40px;
    font-size:11px;
    display:flex;
    align-items:center;
    justify-content:center;
}

/* OUTER BORDER */
.seat-box{
    border:1px solid #aaa;
}

/* MIDDLE LINE DEFAULT */
.seat-part.left{
    border-right:1px solid #aaa;
}

/* FIX: restore outer border look */
.seat-box .seat-part:first-child{
    border-left:none;
}
.seat-box .seat-part:last-child{
    border-right:none;
}
.header-info td{font-weight:bold;text-align:center;background:#f1f1f1;}
@media print{.top-buttons{display:none;}body{background:white;}}
</style>

<script>
function printSeating(){ window.print(); }

async function downloadPDF(){
    const { jsPDF } = window.jspdf;
    let pdf = new jsPDF("l","mm","a4");

    let roomHeadings = document.querySelectorAll("h5.mt-4");

    for(let i=0; i<roomHeadings.length; i++){
        let content = document.createElement("div");
        content.appendChild(roomHeadings[i].cloneNode(true));

        let next = roomHeadings[i].nextElementSibling;
        while(next && next.tagName !== "H5"){
            content.appendChild(next.cloneNode(true));
            next = next.nextElementSibling;
        }

        content.style.padding = "10px";
        content.style.background = "white";
        content.style.width = "1300px";

        document.body.appendChild(content);
        let canvas = await html2canvas(content, {scale:2});
        document.body.removeChild(content);

        let imgData = canvas.toDataURL("image/png");

        let imgWidth = 297;
        let imgHeight = canvas.height * imgWidth / canvas.width;

        if(i > 0){ pdf.addPage(); }

        pdf.addImage(imgData, "PNG", 0, 0, imgWidth, imgHeight);
    }

    pdf.save("SeatingPlanLandscape.pdf");
}

function downloadWord(){
    var content = document.querySelector(".seating-container").innerHTML;
    var html = `<html><body>${content}</body></html>`;
    var blob = new Blob(['\ufeff', html], {type:'application/msword'});
    var url = URL.createObjectURL(blob);
    var a = document.createElement("a");
    a.href = url;
    a.download = "Seating_Plan.doc";
    a.click();
}

/* DRAG + AUTO UPDATE */
let dragged = null;

document.addEventListener("dragstart", e=>{
    if(e.target.classList.contains("draggable")){
        dragged = e.target;
        e.target.style.opacity="0.5";
    }
});

document.addEventListener("dragover", e=>e.preventDefault());

document.addEventListener("drop", e=>{
    if(e.target.classList.contains("draggable")){
        let temp = e.target.innerHTML;
        e.target.innerHTML = dragged.innerHTML;
        dragged.innerHTML = temp;

        updateRoom(e.target.dataset.room);
        updateRoom(dragged.dataset.room);
    }
});

document.addEventListener("dragend", e=>{
    if(e.target.classList.contains("draggable")){
        e.target.style.opacity="1";
    }
});

function updateRoom(room){
    let seats = document.querySelectorAll(`[data-room='${room}']`);
    let total = 0;
    let paperCount = {};

    seats.forEach(seat=>{
        let text = seat.innerText.trim();
        if(text !== ""){
            total++;
            let match = text.match(/\((.*?)\)/);
            if(match){
                let code = match[1];
                paperCount[code] = (paperCount[code] || 0) + 1;
            }
        }
    });

    document.getElementById("total_"+room).innerText = total;

    let summary = "";
    let first = true;
    for(let code in paperCount){
        if(!first) summary += " | ";
        summary += code + " : " + paperCount[code];
        first = false;
    }

    document.getElementById("summary_"+room).innerText = summary;
}
</script>

</head>

<body>

<div class="container seating-container">

<div class="text-center mb-4 position-relative">
<h3><?= $heading ?></h3>
<h5>SESSION : <?= $session ?></h5>

<div class="top-buttons">
<button class="btn btn-primary btn-sm my-btn" onclick="printSeating()">Print</button>
<button class="btn btn-danger btn-sm my-btn" onclick="downloadPDF()">Pdf</button>
<button class="btn btn-success btn-sm my-btn" onclick="downloadWord()">word</button>
</div>
</div>

<?php foreach($room_grids as $room => $grid): ?>

<?php
$room_summary=[];
foreach($grid as $row){
    foreach($row as $seat){
        foreach($seat as $s){
            if($s!=""){
                preg_match('/\((.*?)\)/',$s,$m);
                $room_summary[$m[1]] = ($room_summary[$m[1]]??0)+1;
            }
        }
    }
}
$total_students = array_sum($room_summary);
?>

<h5 class="mt-4">Room <?= $room ?></h5>

<table class="table table-bordered header-info">
<tr>
<td>Shift</td><td><?= $shift ?></td>
<td>Time</td><td><?= $exam_time ?></td>
<td>Date</td><td><?= $formatted_date ?></td>
<td>Total Students</td><td id="total_<?= $room ?>"><?= $total_students ?></td>
</tr>
</table>

<table class="table seat-table">
<tbody>
<?php foreach($grid as $r=>$row): ?>
<tr>
<?php foreach($row as $c=>$seat): ?>
<td>
<div class="seat-number">Seat <?= ($c*count($grid)+$r+1) ?></div>

<div class="seat-box">
    <div class="seat-part left draggable <?= empty($seat[0]) ? 'empty' : '' ?>" 
         draggable="true" data-room="<?= $room ?>">
         <?= $seat[0] ?: '&nbsp;' ?>
    </div>

    <div class="seat-part right draggable <?= empty($seat[1]) ? 'empty' : '' ?>" 
         draggable="true" data-room="<?= $room ?>">
         <?= $seat[1] ?: '&nbsp;' ?>
    </div>
</div>

</td>
<?php endforeach; ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<div class="text-center fw-bold mt-2" id="summary_<?= $room ?>">
<?php
$first=true;
foreach($room_summary as $code=>$count){
    if(!$first) echo " | ";
    echo "$code : $count";
    $first=false;
}
?>
</div>

<hr>

<?php endforeach; ?>

</div>

</body>
</html>