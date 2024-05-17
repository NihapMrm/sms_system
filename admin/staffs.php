<?php
session_start();


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit; 
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staffs</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    

<script>
 

  
        <?php
       
        include("../db.php");

        ?>
    


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



    function showDeleteModal(staffId) {
    // Show the modal
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
    deleteModal.show();

    // When the delete button in the modal is clicked
    document.getElementById('confirmDeleteButton').onclick = function() {
        window.location.href = 'delete.php?id=' + staffId; // Your PHP script for deletion
    };
}



</script>

<?php
 include('header.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname = $conn->real_escape_string($_POST['fname']);
    $lname = $conn->real_escape_string($_POST['lname']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $confirmPassword = $conn->real_escape_string($_POST['confirm_password']);


    $emailCheckQuery = "SELECT Email FROM Staffs WHERE Email = '$email'";
    $emailresult = $conn->query($emailCheckQuery);
    if ($password !== $confirmPassword) {
        echo "Passwords do not match. Please try again.";
    } elseif ($emailresult->num_rows > 0) {
        echo "This email is already used. Please use a different email.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insertSql = "INSERT INTO Staffs (FirstName, LastName, Email, PasswordHash) VALUES (?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($insertSql)) {
            $stmt->bind_param("ssss", $fname, $lname, $email, $hashedPassword);
            if ($stmt->execute()) {
                //echo "New record created successfully";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error: " . $conn->error;
        }
    }
   
}

$sql = "SELECT * FROM Staffs";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    echo '<div class="container">';
    echo '<div class="row g-2 mb-3 mt-5">';
  
    echo'<input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search Student Name" class="form-control mb-3">';

    

    echo '</div>';

    echo "<table class='table table-bordered ' id='stTable'>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>";

    while($row = $result->fetch_assoc()) {
        echo "<tr class='student-row' data-course-ids='" . getStudentCourseIds($conn, $row["StudentID"]) . "' > 
            <td>" . $row["StaffID"] . "</td>
            <td>" . $row["FirstName"] . " " . $row["LastName"] . "</td>
            <td>" . $row["Email"] . "</td>
            <td>
            <button onclick='showDeleteModal(" . $row["StaffID"] . ")' class='btn btn-primary active5'><i class='bi bi-x-octagon'></i></button>
            </td>
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
<style>
     .active5{
        background-color: #8b3dff!important;
        color: #fff!important;
        }
        
</style>
<div class="container d-flex justify-content-end">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
        Add Staff
    </button>
</div>




<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel">Add Staff</h5>
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

                    <!-- Inside your form tag -->
<label for="password">Password:</label>
<input type="password" name="password" id="password" class="form-control" required><br>
<label for="confirm_password">Confirm Password:</label>
<input type="password" name="confirm_password" id="confirm_password" class="form-control" required><br>

                    <input type="submit" value="Submit" class="form-control btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this staff member?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary active5" id="confirmDeleteButton">Delete</button>
            </div>
        </div>
    </div>
</div>
