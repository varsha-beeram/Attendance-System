<?php
$host = "localhost";
$user = "root";
$password = "";      // XAMPP default is empty
$database = "attendance_db";  // your local DB name

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
