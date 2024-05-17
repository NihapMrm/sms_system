<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Managemen System</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <script src="../assets/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
</head>
<style>
    .header{
        height: 35vh;
    }

    .bg-primary{
        background-color: #8b3dff!important;
    }
    .text-primary{
        color: #8b3dff!important;
    }
    .main-items{
        border-radius: 10px;
        border: 1px solid #8b3dff;
        color: #8b3dff!important;

    }
   .btn-primary{
    background-color: unset!important;
    border-color: #8b3dff!important;
    color: #8b3dff!important;
 
   }

   a{
    text-decoration:none !important;
   }
input.btn-primary{
    background-color: #8b3dff !important;
    color: #ffffff!important;
 
   }

</style>

<body>
   
    <div class="container">
        <header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
          <a href="/" class="d-flex align-items-center col-md-4 mb-2 mb-md-0 text-dark text-decoration-none">
            <h5 class="text-primary">Student Mangement System</h5>         
         </a>
    
         
    
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0 ">
                <a href="#" class="d-block link-dark text-decoration-none ">
                    <img src="../assets/img/slyc-logo.png" alt="mdo" width="32" height="32" class="rounded-circle"> 
                </a>
             </div>
            </div>
        </header>

        <div class="container">
            <div class="row ">
               
                <div class="col">
                    <a href="index.php">
                    <div class="p-3 border  text-white main-items text-center active1">Students</div>
                    </a>
                </div>
             
                <div class="col">
                    <a href="courses.php">
                    <div class="p-3 border text-white main-items text-center active2">Courses</div>
                    </a>
                </div>
           
                <div class="col">
                <a href="attendance.php">
                    <div class="p-3 border text-white main-items text-center active3">Attendance</div>
                    </a>
                </div>
                <div class="col">
                <a href="payments.php">
                    <div class="p-3 border text-white main-items text-center active4">Payments</div>
                    </a>
                </div>
                <?php
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    ?>
    <div class="col">
        <a href="staffs.php">
            <div class="p-3 border text-white main-items text-center active5">Staffs</div>
        </a>
    </div>
    <?php
}
?>

              
            </div>
        
        </div>

      </div>


    </div>
</body>
</html>