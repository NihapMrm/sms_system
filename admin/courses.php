<?php
session_start();


if ((!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) &&
    (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true)) {
    header("Location: login.php");
    exit; 
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>

var courseFees = {}; 

function fetchCourseFees() {
    <?php
    include("../db.php");
    $courseFeesResult = $conn->query("SELECT CourseID, CourseFee FROM Courses");
    $courseFeesArray = [];
    while($row = $courseFeesResult->fetch_assoc()) {
        $courseFeesArray[$row['CourseID']] = $row['CourseFee'];
    }
    echo "courseFees = " . json_encode($courseFeesArray) . ";";
    ?>
}

function updatePaymentDisplay(courseId) {
    var paymentTypeSelect = document.getElementById('paymentType');
    var fullPaymentOption = paymentTypeSelect.querySelector('option[value="full"]');
    if (courseId && courseFees[courseId]) {
        fullPaymentOption.text = "Full Payment (" + courseFees[courseId] + ")";
    } else {
        fullPaymentOption.text = "Full Payment";
    }
}

function toggleAmountInput(paymentType) {
    var amountInput = document.getElementById('partialAmount');
    if (paymentType.value === 'partial') {
        amountInput.style.display = 'block';
    } else {
        amountInput.style.display = 'none';
     
        var selectedCourse = document.getElementById('course').value;
        updatePaymentDisplay(selectedCourse);
    }
}

$(document).ready(function(){
            $('#courseSelect').change(function(){
                var courseId = $(this).val();
                $.ajax({
                    url: 'courses.php',  
                    type: 'post',
                    data: {courseId: courseId},
                    success: function(data){
                        $('#studentsTable').html(data);
                    }
                });
            });
        });

function filterTable() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("crsTable"); 
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[1]; 
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }       
    }
}

        function updateDurationLabel(value) {
            var durationLabel = document.getElementById('durationLabel');
          
            switch(value) {
                case '1': durationLabel.innerText = '1 Month'; break;
                case '2': durationLabel.innerText = '3 Months'; break;
                case '3': durationLabel.innerText = '6 Months'; break;
                case '4': durationLabel.innerText = '1 Year'; break;
                case '5': durationLabel.innerText = '2 Years'; break;
                case '6': durationLabel.innerText = '4 Years'; break;
                default: durationLabel.innerText = 'Select Duration'; 
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            updateDurationLabel(document.getElementById('courseDuration').value);
            fetchCourseFees();
        });

    </script>
</head>
<body>
   
</body>
</html>

<?php
include('header.php');

include("../db.php");

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    fetch_students($conn);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if (isset($_POST['assignStudent'])) {
    $studentID = $_POST['student'];
    $courseID = $_POST['course'];
    $paymentType = $_POST['paymentType'];
    $amount = isset($_POST['amount']) ? $_POST['amount'] : 0;

    $stmt = $conn->prepare("SELECT CourseFee, DurationInMonths FROM Courses WHERE CourseID = ?");
    $stmt->bind_param("i", $courseID);
    $stmt->execute();
    $courseDetailsResult = $stmt->get_result();
    $courseDetails = $courseDetailsResult->fetch_assoc();
    $courseFee = $courseDetails['CourseFee'];
    $courseDuration = $courseDetails['DurationInMonths'];
    $stmt->close();

    $enrollmentDate = date("Y-m-d"); 
    $durationString = "+" . $courseDuration . " months";
    $completionDate = date("Y-m-d", strtotime($durationString, strtotime($enrollmentDate)));

    if ($paymentType === 'full') {
        $finalAmount = $courseFee;
        $finalStatus = 'Completed';
    } else {
        $finalAmount = $amount;
        $finalStatus = 'Pending';
    }

    $stmt = $conn->prepare("INSERT INTO StudentCourses (StudentID, CourseID, EnrollmentDate, CompletionDate) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $studentID, $courseID, $enrollmentDate, $completionDate);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO Payments (StudentID, Amount, PaymentStatus, CourseID) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $studentID, $finalAmount, $finalStatus, $courseID);
    if ($stmt->execute()) {
        //echo "Student added successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
     }
elseif (isset($_POST['addCourse'])){
  

        function convertToMonths($value) {
            switch ($value) {
                case '1': return 1;
                case '2': return 3;
                case '3': return 6;
                case '4': return 12;
                case '5': return 24;
                case '6': return 48;
                default: return 0; 
            }
        }
    
        $courseName = $conn->real_escape_string($_POST['courseName']);
        $courseDuration = $conn->real_escape_string($_POST['courseDuration']);
        $courseFee = $conn->real_escape_string($_POST['courseFee']);
    
        $courseDurationInMonths = convertToMonths($courseDuration);
    
        $sql = "INSERT INTO Courses (CourseName, DurationInMonths, CourseFee) VALUES ('$courseName', '$courseDurationInMonths', '$courseFee')";
    
        if ($conn->query($sql) === FALSE) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    
    
    
      
    }
    
    
    }


$sql = "SELECT * FROM Courses";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<div class="container mt-5">';
    echo'<input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search Course Name" class="form-control mb-3">';

    echo "<table border='1' class='table table-bordered ' id='crsTable'>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Duration</th>
        <th>Fee</th>
        <th>No of Students</th>
    </tr>";

    while($row = $result->fetch_assoc()) {
        $duration = $row["DurationInMonths"];
        if ($duration == 12){
            $durationText = "1 Year";
        } else if ($duration > 12){
            $years = floor($duration / 12);
            $durationText = $years . " Years";
          
        } else {
            if($duration > 1){
                $durationText = $duration. " Months";
            }
            else{
                $durationText = $duration. " Month";
            }
            
        }

        $CourseId=  $row['CourseID'];

        $sql="SELECT COUNT(StudentID)FROM StudentCourses WHERE CourseID = $CourseId";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $num_rows = $stmt->get_result()->fetch_row()[0];
    


        echo "<tr class='course-row'>
            <td>" . $row["CourseID"] . "</td>
            <td>" . $row["CourseName"] . "</td>
            <td>"  . $durationText . "</td>
            <td>" . $row["CourseFee"] . "</td>
            <td>" . $num_rows . "</td>

        </tr>";
    }
    echo "</table>";
    echo "</div>";
} else {
    echo "0 results";
}





?>

<style>
     .active2{
        background-color: #8b3dff!important;
        color: #fff!important;
        }
        
</style>


<div class="container d-flex justify-content-end" style="gap: 20px;">
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignCourse" style="gap: 20px;">
Assign Student to Course
</button>
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
    Add Course
</button>
</div>

<div class="modal fade" id="assignCourse" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel">Assign Student to Course</h5>
                <button type="button" class="btn rounded" data-bs-dismiss="modal" aria-label="Close">
                <i class="bi bi-x"></i>

                    </button>
            </div>
            <div class="modal-body">
             
            <form method="post">
        <select name="student" id="student" required class="form-control">
            <?php
            include("../db.php");
            $result = $conn->query("SELECT StudentID, FirstName, LastName FROM Students");
            while($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['StudentID'] . "'>" . $row['FirstName'] . " " . $row['LastName'] ."</option>";
            }
            ?>
        </select><br>

        <select name="course" id="course" required class="form-control" onchange="updatePaymentDisplay(this.value)">
        <option value="0">Select Course</option>
            <?php
            $result = $conn->query("SELECT CourseID, CourseName FROM Courses");
            while($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['CourseID'] . "'>" . $row['CourseName'] . "</option>";
            }
            ?>
        </select><br>

        <select name="paymentType" id="paymentType" onchange="toggleAmountInput(this)" required class="form-control">
        <option value="0">Select Payment Type</option>
            <option value="full">Full Payment</option>
            <option value="partial">Partial Payment</option>
        </select><br>

        <div id="partialAmount" style="display:none;">
            <label for="amount">Amount:</label>
            <input type="number" name="amount" id="amount" class="form-control"><br>
        </div>

        <input type="submit" name="assignStudent" value="Add Student to Course" class="form-control btn btn-primary">
    </form>
            </div>
        </div>
    </div>
</div>

   


    <div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel">Add New Course</h5>
                <button type="button" class="btn rounded" data-bs-dismiss="modal" aria-label="Close">
                <i class="bi bi-x"></i>

                    </button>
            </div>
            <div class="modal-body">
             
            <form method="POST">
        <label for="courseName">Course Name:</label>
        <input type="text" name="courseName" id="courseName" class="form-control" required><br>
        
        <label for="courseDuration">Course Duration:</label> <span id="durationLabel">Select Duration</span><br>
        <input type="range" name="courseDuration" id="courseDuration" class="form-control" min="1" max="6" oninput="updateDurationLabel(this.value)" required>
       

        <label for="courseFee">Course Fee:</label>
        <input type="number" name="courseFee" id="courseFee" class="form-control" required><br>
        
        <input type="submit" value="Add Course" name="addCourse" class="form-control btn btn-primary">
    </form>
            </div>
        </div>
    </div>
</div>
