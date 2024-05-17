<?php
session_start();

include("../db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the Admins table first
    $stmt = $conn->prepare("SELECT AdminID, Email, PasswordHash FROM Admins WHERE Email = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Admin found, verify password
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['PasswordHash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $row['AdminID'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Password is incorrect";
        }
    } else {
        // Admin not found, check in Staffs table
        $stmt = $conn->prepare("SELECT StaffID, Email, PasswordHash FROM Staffs WHERE Email = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Staff found, verify password
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['PasswordHash'])) {
                session_regenerate_id(true);
                $_SESSION['staff_logged_in'] = true;
                $_SESSION['staff_id'] = $row['StaffID'];
                header("Location: index.php"); // Redirect to staff dashboard
                exit;
            } else {
                $error = "Password is incorrect";
            }
        } else {
            // User not found in both tables
            $error = "User not found";
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <script src="../assets/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
</head>
<body>

<style>
     body, html {
            height: 100%;
        }
        .btn-primary{
    background-color: #8b3dff !important;
    }
</style>




<div class="container h-100 d-flex justify-content-center align-items-center">
    <div class="card p-4" style="width: 22rem;">
        <h2 class="card-title text-center">Admin Login</h2>
        <?php if(isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary form-control" value="Login" style="margin-top:20px;" >
            </div>
        </form>
    </div>
</div>
</body>
</html>