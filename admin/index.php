<?php
session_start();


if ((!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) &&
    (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true)) {
    header("Location: login.php");
    exit; 
}


include('header.php');

include('students.php');


?>
<style>
     .active1{
        background-color: #8b3dff!important;
        color: #fff!important;
        }
</style>

