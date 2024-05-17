<?php
session_start();


if ((!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) &&
    (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true)) {
    header("Location: login.php");
    exit; 
}

include("../db.php"); 
function handle_attendance_submission($conn) {
    $courseId = $conn->real_escape_string($_POST['courseId']);
    $attendanceDate = $conn->real_escape_string($_POST['attendanceDate']);

    // Get all students for the course
    $studentsQuery = "SELECT StudentID FROM StudentCourses WHERE CourseID = '{$courseId}'";
    $studentsResult = $conn->query($studentsQuery);

    if ($studentsResult->num_rows > 0) {
        while ($studentRow = $studentsResult->fetch_assoc()) {
            $studentId = $conn->real_escape_string($studentRow['StudentID']);
            $status = isset($_POST['attendance'][$studentId]) ? 'Present' : 'Absent'; // Determine if the student is present or absent

            $checkQuery = "SELECT * FROM Attendance WHERE StudentID = '{$studentId}' AND Date = '{$attendanceDate}' AND CourseID = '{$courseId}'";
            $checkResult = $conn->query($checkQuery);

            if ($checkResult->num_rows > 0) {
                $updateQuery = "UPDATE Attendance SET Status = '{$status}' WHERE StudentID = '{$studentId}' AND Date = '{$attendanceDate}' AND CourseID = '{$courseId}'";
                $conn->query($updateQuery);
            } else {
                $insertQuery = "INSERT INTO Attendance (StudentID, Date, Status, CourseID) VALUES ('{$studentId}', '{$attendanceDate}', '{$status}', '{$courseId}')";
                $conn->query($insertQuery);
            }
        }
        echo "Attendance records updated successfully!";
    } else {
        echo "No students found for this course.";
    }
}

function display_students($conn, $courseId = 1, $selectedDate = null, $mode = 'view') {
    if ($selectedDate === null) {
        $selectedDate = date('Y-m-d'); // Sets the date to today's date if not provided
    }

    // Prepared statement to prevent SQL injection
    $sql = "SELECT Students.StudentID, Students.FirstName, Students.LastName, Attendance.Status 
            FROM Students 
            LEFT JOIN Attendance ON Students.StudentID = Attendance.StudentID 
            AND Attendance.Date = ? AND Attendance.CourseID = ?
            JOIN StudentCourses ON Students.StudentID = StudentCourses.StudentID 
            WHERE StudentCourses.CourseID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $selectedDate, $courseId, $courseId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        if ($mode === 'mark') {
            echo "<form id='attendanceForm' onsubmit='submitAttendance(event)'>";
        } else {
            echo "<table border='1' class='table table-bordered'>";
        }
        
        // Include hidden inputs
        echo "<input type='hidden' name='courseId' value='".htmlspecialchars($courseId)."'>";
        echo "<input type='hidden' name='attendanceDate' value='".htmlspecialchars($selectedDate)."'>";
        
        // Start table
        echo "<table class='table table-bordered'><thead><tr><th>Name</th><th>Attendance</th></tr></thead><tbody>";

        while ($row = $result->fetch_assoc()) {
            $checked = $row['Status'] === 'present' ? 'checked' : '';
            echo "<tr><td>" . htmlspecialchars($row["FirstName"]) . " " . htmlspecialchars($row["LastName"]) . "</td>";
        
            if ($mode === 'mark') {
                // Echo checkbox for marking attendance
                echo "<td><input type='checkbox' name='attendance[{$row['StudentID']}]' value='Present' $checked></td>";
            } else {
                // Just display attendance status
                echo "<td>";
                if (isset($row['Status']) && $row['Status'] !== null) {
                    echo htmlspecialchars($row['Status']);
                } else {
                    echo "Not Marked";
                }
                echo "</td>";
            }
            echo "</tr>";
        }
        

        // Close table body and table
        echo "</tbody></table>";
        
        if ($mode === 'mark') {
            echo "<input type='submit' value='Submit Attendance' class='form-control'>";
            echo "</form>";
        }
    } else {
        echo "<p>No students are enrolled in this course.</p>";
    }

}

// JavaScript should be placed outside the PHP function or in a separate file
if ($mode === 'mark') {
    echo "<script>
            function toggleCheckbox(row) {
                var checkbox = row.getElementsByTagName('input')[0];
                checkbox.checked = !checkbox.checked;
            }
          </script>";
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['course']) && isset($_POST['date'])) {
    $selectedCourse = $conn->real_escape_string($_POST['course']);
    $selectedDate = $conn->real_escape_string($_POST['date']);
    $mode = isset($_POST['mode']) ? $_POST['mode'] : 'mark';
    display_students($conn, $selectedCourse, $selectedDate, $mode);
    $conn->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['attendance'])) {
    handle_attendance_submission($conn);
    $conn->close();
    exit;
}
?>

<?php
include('header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
       function loadStudents() {
            var selectedCourse = $('#courseSelect').val();
            var selectedDate = $('#dateSelect').val();
            var selectedMode = $('input[name="mode"]:checked').val();
            if (selectedCourse && selectedDate) {
                $.ajax({
                    url: 'attendance.php', 
                    type: 'post',
                    data: { course: selectedCourse, date: selectedDate, mode: selectedMode },
                    success: function(data) {
                        $('#studentList').html(data);
                    }
                });
            }
        }

        function submitAttendance(event) {
            event.preventDefault();  
            $.ajax({
                url: 'attendance.php',
                type: 'post',
                data: $('#attendanceForm').serialize(),
                success: function(response) {
                    alert(response);
                    $('#studentList').html('');  
                    location.reload(); 

                }
            });
        }

        $(document).ready(function() {
            $('#courseSelect, #dateSelect, input[name="mode"]').change(loadStudents);
        });
        document.addEventListener('DOMContentLoaded', function() {
    var date = new Date();
    var today = date.toISOString().split('T')[0]; // Format to YYYY-MM-DD
    document.getElementById("dateSelect").setAttribute('max', today);
});

    </script>
</head>
<body>
<style>
     .active3{
        background-color: #8b3dff!important;
        color: #fff!important;
        }

        .btn-check:checked + .btn-primary,
.btn-check:active + .btn-primary {
  color: #fff !important;
  background-color: #8b3dff !important;
  border-color: #8b3dff !important;
}
</style>
<div class="container d-flex justify-content-center" style="padding-top: 40px;">
 <div class="mode-selection">
    <input type="radio" class="btn-check" name="mode" id="viewMode" value="view" checked>
    <label class="btn btn-primary" for="viewMode">View</label>

    <input type="radio" class="btn-check" name="mode" id="markMode" value="mark">
    <label class="btn btn-primary" for="markMode">Mark</label>
</div>

</div>


   <div class="container">
   <div class="row g-2 mb-3 mt-5">
   <div class="col-sm-10">
    <select name="course" id="courseSelect"  class="form-control">
        <option value="">Select a Course</option>
        <?php
        $courses = $conn->query("SELECT CourseID, CourseName FROM Courses");
        while($course = $courses->fetch_assoc()) {
            echo "<option value='" . $course['CourseID'] . "'>" . $course['CourseName'] . "</option>";
        }
        ?>
    </select>
    </div>
    <div class="col-sm-2">
    <input type="date" id="dateSelect"  class="form-control">
    </div>
    </div>

    <div class="container" >
    <div id="studentList">
        <div class="container" style="
    height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 20px;
">
        <i class="bi bi-sliders" style="font-size:60px;"></i>

        <p>Select a course and date to show the details.</p>
        </div>
   

    </div>
    </div>

</div>
</body>
</html>