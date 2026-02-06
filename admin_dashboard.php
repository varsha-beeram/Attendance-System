
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Admin Dashboard</h2>
    <p class="subtitle">Attendance Management System</p>

    <div class="branch-badge">
        <?php echo $_SESSION['branch']; ?>
    </div>

    <div class="menu">
        <a href="add_student.php"><span>➕</span> Add Student</a>
        <a href="take_attendance_periodwise.php"><span>📝</span> Take Attendance</a>
        <a href="logout.php"><span>🚪</span> Logout</a>
    </div>
</div>

</body>
</html>
