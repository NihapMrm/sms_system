<?php

$server = 'localhost';
$username = 'root';
$password = '';
$dbname = 'sms';

$conn = new mysqli($server, $username, $password, $dbname);

if ($conn->connect_error){
    echo "Connection Faild: ";
} 