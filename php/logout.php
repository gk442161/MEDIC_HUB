<?php
    include 'mysql_login.php';

    $conn=new mysqli($hostname,$username,$password,$database);
    if($conn->connect_error) die($conn->connect_error);

session_start();
$conn->close();
session_destroy();
echo 'You have been logged out. ';

// ?>