<?php include("navbar.php"); ?>
<?php include("db.php"); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Seating Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-dark text-white text-center">
            <h4 class="mb-0">Create Seating Plan</h4>
        </div>

        <div class="card-body">
            <form method="POST" action="generate.php">

                <!-- SESSION / DATE / TIME -->
                <div class="row g-3 mb-3">

                    <div class="col-md-4">
                        <label class="form-label">Session</label>
                        <input type="text" name="session" class="form-control" placeholder="2025-26" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Exam Date</label>
                        <input type="date" name="exam_date" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Exam Time</label>
                        <input type="time" name="exam_time" class="form-control" required>
                    </div>

                </div>

                <!-- ROOMS -->
                <h5 class="mt-3">Rooms</h5>
                <div id="roomsContainer">

                    <div class="row g-3 mb-2 room-block">
                        <div class="col-md-6">
                            <input type="text" name="rooms[0][roomNo]" class="form-control" placeholder="Room Number" required>
                        </div>

                        <div class="col-md-3">
                            <input type="number" name="rooms[0][rows]" class="form-control" placeholder="Rows" required>
                        </div>

                        <div class="col-md-3">
                            <input type="number" name="rooms[0][cols]" class="form-control" placeholder="Columns" required>
                        </div>
                    </div>

                </div>

                <button type="button" class="btn btn-sm btn-success mb-3" onclick="addRoom()">+ Add Room</button>

                <!-- COURSES -->
                <h5 class="mt-3">Courses</h5>
                <div id="courseContainer">

                    <div class="row g-3 mb-2 course-block">

                        <div class="col-md-3">
                            <select name="courses[0][course]" class="form-select" required>
                                <option value="">Course</option>
                                <option>BCA</option>
                                <option>B-Tech</option>
                                <option>BBA</option>
                                <option>MCA</option>
                                <option>BPharma</option>
                                <option>MBA</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <select name="courses[0][year]" class="form-select" required>
                                <option value="">Year</option>
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                            </select>
                        </div>

                        

                        <div class="col-md-3">
                            <input type="number" name="courses[0][students]" class="form-control" placeholder="No. of Students" required>
                        </div>

                    </div>

                </div>

                <button type="button" class="btn btn-sm btn-primary mb-3" onclick="addCourse()">+ Add Course</button>

                <!-- SUBMIT -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-dark px-5">Generate Seating</button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- JS -->
<script>
let roomIndex = 1;

function addRoom(){
    let html = `
    <div class="row g-3 mb-2 room-block">
        <div class="col-md-6">
            <input type="text" name="rooms[${roomIndex}][roomNo]" class="form-control" placeholder="Room Number" required>
        </div>

        <div class="col-md-3">
            <input type="number" name="rooms[${roomIndex}][rows]" class="form-control" placeholder="Rows" required>
        </div>

        <div class="col-md-3">
            <input type="number" name="rooms[${roomIndex}][cols]" class="form-control" placeholder="Columns" required>
        </div>
    </div>`;

    document.getElementById("roomsContainer").insertAdjacentHTML("beforeend", html);
    roomIndex++;
}

let courseIndex = 1;

function addCourse(){
    let html = `
    <div class="row g-3 mb-2 course-block">

        <div class="col-md-3">
            <select name="courses[${courseIndex}][course]" class="form-select" required>
                <option value="">Course</option>
                <option>BCA</option>
                <option>B-Tech</option>
                <option>BBA</option>
                <option>MCA</option>
                <option>BPharma</option>
                <option>MBA</option>
            </select>
        </div>

        <div class="col-md-3">
            <select name="courses[${courseIndex}][year]" class="form-select" required>
                <option value="">Year</option>
                <option>1</option>
                <option>2</option>
                <option>3</option>
                <option>4</option>
            </select>
        </div>


        <div class="col-md-3">
            <input type="number" name="courses[${courseIndex}][students]" class="form-control" placeholder="No. of Students" required>
        </div>

    </div>`;

    document.getElementById("courseContainer").insertAdjacentHTML("beforeend", html);
    courseIndex++;
}
</script>

</body>
</html>