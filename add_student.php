<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$message = "";
$message_color = "";

if (isset($_POST['add'])) {
    $roll = $_POST['roll'];
    $name = $_POST['name'];
    $branch = $_SESSION['branch'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = "INSERT INTO students (roll_no, name, branch, password)
              VALUES ('$roll', '$name', '$branch', '$password')";

    if (mysqli_query($conn, $query)) {
        $message = "✅ Student added successfully";
        $message_color = "green";
    } else {
        $message = "❌ Error adding student (Roll number may already exist)";
        $message_color = "red";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Add Student</h2>

    <?php if (!empty($message)) { ?>
        <p style="color:<?php echo $message_color; ?>; text-align:center; font-weight:bold;">
            <?php echo $message; ?>
        </p>
    <?php } ?>

    <p style="text-align:center;">
        Branch: <b><?php echo $_SESSION['branch']; ?></b>
    </p>

    <form method="post">
        <input type="text" name="roll" placeholder="Roll Number" required>
        <input type="text" name="name" placeholder="Student Name" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="add">Add Student</button>
    </form>

    <a href="admin_dashboard.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>
