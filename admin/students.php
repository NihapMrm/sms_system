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

    function filterTable() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("stTable");
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

    function showNameModal(name) {
        document.getElementById('studentName').innerText = name;
        var modal = new bootstrap.Modal(document.getElementById('nameDisplayModal'));
        modal.show();
    }

    function filterStudentsByCourse() {
        var selectedCourseId = document.getElementById('courseSelect').value;
        var studentRows = document.querySelectorAll('.student-row');

        studentRows.forEach(function(row) {
            var courseIds = row.getAttribute('data-course-ids').split(',');
            if (selectedCourseId === "" || courseIds.includes(selectedCourseId)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        fetchCourseFees();
        filterStudentsByCourse();
    });




</script>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname = $conn->real_escape_string($_POST['fname']);
    $lname = $conn->real_escape_string($_POST['lname']);
    $email = $conn->real_escape_string($_POST['email']);
    $courseID = $conn->real_escape_string($_POST['course']);
    $paymentType = $conn->real_escape_string($_POST['paymentType']);
    $amount = $conn->real_escape_string($_POST['amount']);

    $emailCheckQuery = "SELECT Email FROM Students WHERE Email = '$email'";
    $emailresult = $conn->query($emailCheckQuery);

    if ($emailresult->num_rows > 0) {
        echo "This email is already used. Please use a different email.";
    } else {
        $sql = "INSERT INTO Students (FirstName, LastName, Email, Status) VALUES ('$fname', '$lname', '$email', 'Registered')";

        if ($conn->query($sql) === TRUE) {
            $studentID = $conn->insert_id;
            $courseDetailsResult = $conn->query("SELECT CourseFee, DurationInMonths FROM Courses WHERE CourseID = '$courseID'");
            $courseDetails = $courseDetailsResult->fetch_assoc();
            $courseFee = $courseDetails['CourseFee'];
            $courseDuration = $courseDetails['DurationInMonths'];

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

            $insertStudentCourseSQL = "INSERT INTO StudentCourses (StudentID, CourseID, EnrollmentDate, CompletionDate) VALUES ('$studentID', '$courseID', '$enrollmentDate', '$completionDate')";
            $conn->query($insertStudentCourseSQL);

            $insertPaymentSQL = "INSERT INTO Payments (StudentID, Amount, PaymentStatus, CourseID) VALUES ('$studentID', '$finalAmount', '$finalStatus', '$courseID')";
            if ($conn->query($insertPaymentSQL) === TRUE) {
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$sql = "SELECT * FROM Students";
$result = $conn->query($sql);

$coursesSql = "SELECT CourseID, CourseName FROM Courses";
$coursesResult = $conn->query($coursesSql);

if ($result->num_rows > 0) {
    echo '<div class="container">';
    echo '<div class="row g-2 mb-3 mt-5">';
    echo '  <div class="col-sm-10">';
    echo'<input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search Student Name" class="form-control mb-3">';
    echo '  </div>';
    echo '  <div class="col-sm-2">';
    echo '<select id="courseSelect" class="form-control mb-3" onchange="filterStudentsByCourse()" >
        <option value="">Select a course</option>';
        while($course = $coursesResult->fetch_assoc()) {
            echo '<option value="' . $course["CourseID"] . '">' . $course["CourseName"] . '</option>';
        }

        echo '</select>';
        echo '  </div>';
    echo '</div>';

    echo "<table class='table table-bordered ' id='stTable'>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>";

    while($row = $result->fetch_assoc()) {
        echo "<tr class='student-row' data-course-ids='" . getStudentCourseIds($conn, $row["StudentID"]) . "' style='display: none;' > 
            <td>" . $row["StudentID"] . "</td>
            <td>" . $row["FirstName"] . " " . $row["LastName"] . "</td>
            <td>" . $row["Email"] . "</td>
        </tr>";
    }

    echo "</tbody></table>";
    echo "</div>";
} else {
    echo "0 results";
}

$conn->close();

function getStudentCourseIds($conn, $studentId) {
    $ids = [];
    $query = "SELECT CourseID FROM StudentCourses WHERE StudentID = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $ids[] = $row["CourseID"];
        }
        $stmt->close();
    }
    return implode(',', $ids);
}

?>

<div class="container d-flex justify-content-end">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
        Add Student
    </button>
</div>




<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
                <button type="button" class="btn rounded" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <label for="fname">First Name:</label>
                    <input type="text" name="fname" id="fname" class="form-control" required><br>
                    <label for="lname">Last Name:</label>
                    <input type="text" name="lname" id="lname" class="form-control" required><br>
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" class="form-control" required><br>
                    <label for="course">Course:</label>
                    <select name="course" id="course" class="form-control" required onchange="updatePaymentDisplay(this.value)">
                        <option value="0">Select Course</option>
                        <?php
                        $conn = new mysqli('localhost', 'root', '', 'sms');
                        $result = $conn->query("SELECT CourseID, CourseName FROM Courses");
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['CourseID'] . "'>" . $row['CourseName'] . "</option>";
                        }
                        ?>
                    </select><br>

                    <label for="paymentType">Payment Type:</label>
                    <select name="paymentType" id="paymentType" class="form-control" onchange="toggleAmountInput(this)" required>
                        <option value="0">Select Payment Type</option>
                        <option value="full">Full Payment</option>
                        <option value="partial">Partial Payment</option>
                    </select><br>

                    <div id="partialAmount" style="display:none;">
                        <label for="amount">Amount:</label>
                        <input type="number" name="amount" id="amount" class="form-control"><br>
                    </div>
                    <input type="submit" value="Submit" class="form-control btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>
