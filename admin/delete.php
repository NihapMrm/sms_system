<?php
include("../db.php"); // Include your database connection

if(isset($_GET['id'])) {
    $staffId = $conn->real_escape_string($_GET['id']);

    // SQL to delete a record
    $sql = "DELETE FROM Staffs WHERE StaffID = '$staffId'";

    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

$conn->close();
header('Location: staffs.php'); // Redirect back to your staff page
?>
