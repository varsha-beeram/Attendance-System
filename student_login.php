<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {
    $roll = $_POST['roll'];
    $password = $_POST['password'];

    $query = "SELECT * FROM students WHERE roll_no='$roll'";
    $result = mysqli_query($conn, $query);
    $student = mysqli_fetch_assoc($result);

    if ($student && password_verify($password, $student['password'])) {
        $_SESSION['student_id'] = $student['student_id'];
        $_SESSION['roll_no'] = $student['roll_no'];
        $_SESSION['name'] = $student['name'];
        header("Location: student_dashboard.php");
        exit();
    } else {
        $error = "❌ Invalid roll number or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Student Login</h2>

    <?php if (!empty($error)) { ?>
        <p style="color:red; text-align:center;"><?php echo $error; ?></p>
    <?php } ?>

    <form method="post">
        <input type="text" name="roll" placeholder="Roll Number" required>
        <input type="password" name="password" placeholder="Password" required>
        <button name="login">Login</button>
    </form>

    <a href="index.html">⬅ Back</a>
</div>

</body>
</html>
