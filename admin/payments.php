<?php

session_start();


if ((!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) &&
    (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true)) {
    header("Location: login.php");
    exit; 
}



$server = 'localhost';
$username = 'root';
$password = '';
$dbname = 'sms';

$conn = new mysqli($server, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getStudents($conn) {
    $sql = "SELECT StudentID, FirstName, LastName FROM Students";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getStudentCourses($studentID, $conn) {
    $sql = "SELECT c.CourseName, c.CourseFee, sc.CourseID,
                   (c.CourseFee - IFNULL(SUM(p.Amount), 0)) as Balance,
                   IFNULL(SUM(p.Amount), 0) as AmountPaid
            FROM StudentCourses sc
            LEFT JOIN Courses c ON sc.CourseID = c.CourseID
            LEFT JOIN Payments p ON sc.StudentID = p.StudentID AND sc.CourseID = p.CourseID
            WHERE sc.StudentID = ?
            GROUP BY sc.CourseID, c.CourseName, c.CourseFee";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $studentID);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

$students = getStudents($conn);
$selectedStudentCourses = [];
$selectedStudentID = 0;

if (isset($_GET['student_id'])) {
    $selectedStudentID = $_GET['student_id'];
    $selectedStudentCourses = getStudentCourses($selectedStudentID, $conn);
}

function addPayment($studentID, $courseID, $amount, $conn) {
    $sql = "INSERT INTO Payments (StudentID, CourseID, Amount, DatePaid, PaymentStatus) VALUES (?, ?, ?, CURDATE(), 'completed')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iid", $studentID, $courseID, $amount);
    return $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['amount'], $_POST['student_id'], $_POST['course_id'])) {
        $amount = $_POST['amount'];
        $studentID = $_POST['student_id'];
        $courseID = $_POST['course_id'];
        
        if (addPayment($studentID, $courseID, $amount, $conn)) {
            header("Location: payments.php?student_id=$studentID");
            exit();
        } else {
            $error = "Error adding payment.";
        }
    }
}

$conn->close();
?>

<?php

include('header.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Payments</title>
    <script>
        function showPaymentInput(studentID, courseID) {
            var inputDiv = document.getElementById('paymentInput' + courseID);
            inputDiv.style.display = 'block';
        }

        function submitPayment(studentID, courseID) {
            var amountInput = document.getElementById('paymentAmount' + courseID).value;
            var form = document.getElementById('paymentForm' + courseID);
            form.amount.value = amountInput;
            form.student_id.value = studentID;
            form.course_id.value = courseID;
            form.submit();
        }
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
    </script>
</head>
<body>
<style>
     .active4{
        background-color: #8b3dff!important;
        color: #fff!important;
        }

    #addCourseModal{
        display: flex;
    opacity: 1;
    height: 100vh;
    align-items: center;
    background-color: #00000082;
    }

    i{
        color: #8b3dff;
    }


</style>

    <div class="container mt-5">
    <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search Course Name" class="form-control mb-3">
  <table border='1' class='table table-bordered ' id='crsTable'>
    <thead>
      <tr >
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($students as $student):?>
      <tr onclick="window.location='?student_id=<?php echo $student['StudentID']; ?>'">
        <td><?php echo $student['StudentID'];?></td>
        <td><?php echo $student['FirstName'];?></td>
        <td><?php echo $student['LastName'];?></td>
      </tr>
      <?php endforeach;?>
    </tbody>
  </table>
  </div>

    <?php if ($selectedStudentID > 0): ?>
  
        <div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel">Student Courses and Payments</h5>
                <button type="button" class="btn rounded" data-bs-dismiss="modal" aria-label="Close" onclick="window.location.href='payments.php'">
                <i class="bi bi-x"></i>

                    </button>
            </div>
            <div class="modal-body">
    
    <table border="1" class='table table-bordered'>
        <tr>
            <th>Course Name</th>
            <th>Course Fee</th>
            <th>Amount Paid</th>
            <th>Balance</th>
            <th>Status</th>
        </tr>
        <?php foreach ($selectedStudentCourses as $course): ?>
            <tr>
                <td><?php echo htmlspecialchars($course['CourseName']); ?></td>
                <td><?php echo htmlspecialchars($course['CourseFee']); ?></td>
                <td><?php echo htmlspecialchars($course['AmountPaid']); ?></td>
                <td><?php echo htmlspecialchars($course['Balance']); ?></td>
                <td>
                    <?php if ($course['Balance'] == 0): ?>
                        <i class="bi bi-check2-all"></i>
                    <?php else: ?>
                        <a href="javascript:void(0);" onclick="showPaymentInput(<?php echo $course['CourseID']; ?>)"><i class="bi bi-hourglass-split"></i></a> 
                        <div id="paymentInput<?php echo $course['CourseID']; ?>" style="display:none;">
                            <input type="number" id="paymentAmount<?php echo $course['CourseID']; ?>" placeholder="Enter amount" class="form-control">
                            <button class="button form-control" onclick="submitPayment(<?php echo $selectedStudentID; ?>, <?php echo $course['CourseID']; ?>)">Submit</button>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>        </div>
        </div>
    </div>
</div>

    <?php foreach ($selectedStudentCourses as $course): ?>
        <form id="paymentForm<?php echo $course['CourseID']; ?>" method="post" style="display:none;">
            <input type="hidden" name="amount" value="">
            <input type="hidden" name="student_id" value="<?php echo $selectedStudentID; ?>">
            <input type="hidden" name="course_id" value="<?php echo $course['CourseID']; ?>">
        </form>
    <?php endforeach; ?>
<?php endif; ?>

<script>
    function showPaymentInput(courseID) {
        var inputDiv = document.getElementById('paymentInput' + courseID);
        inputDiv.style.display = 'block';
    }

    function submitPayment(studentID, courseID) {
        var amountInput = document.getElementById('paymentAmount' + courseID).value;
        var form = document.getElementById('paymentForm' + courseID);
        form.amount.value = amountInput;
        form.student_id.value = studentID;
        form.course_id.value = courseID;
        form.submit();
    }
</script>

</body>
</html>
