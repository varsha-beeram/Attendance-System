<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$branch = $_SESSION['branch'];
$date = date("Y-m-d");

/* Check if attendance already taken */
$check = mysqli_query($conn, "
    SELECT a.attendance_id 
    FROM attendance a
    JOIN students s ON a.student_id = s.student_id
    WHERE s.branch='$branch' AND a.date='$date'
");

$already_taken = mysqli_num_rows($check) > 0;

if (isset($_POST['submit']) && !$already_taken) {
    foreach ($_POST['attendance'] as $student_id => $periods) {
        foreach ($periods as $period => $status) {
            mysqli_query($conn, "
                INSERT INTO attendance (student_id, date, period, status)
                VALUES ('$student_id', '$date', '$period', '$status')
            ");
        }
    }
    $already_taken = true;
}

$students = mysqli_query($conn,
    "SELECT * FROM students WHERE branch='$branch' ORDER BY roll_no"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take Attendance</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="attendance-container">

    <div class="attendance-header">
        <h2>📝 Take Attendance</h2>
        <span>Branch: <?php echo $branch; ?> | Date: <?php echo $date; ?></span>
    </div>

    <?php if ($already_taken) { ?>
        <p style="color:red; font-weight:bold; text-align:center;">
            ⚠ Attendance already taken for today
        </p>
    <?php } else { ?>

    <form method="post">

        <table class="attendance-table">
            <tr>
                <th>Roll No</th>
                <th>Name</th>
                <?php for ($p = 1; $p <= 7; $p++) { ?>
                    <th>P<?php echo $p; ?></th>
                <?php } ?>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($students)) { ?>
            <tr>
                <td><?php echo $row['roll_no']; ?></td>
                <td><?php echo $row['name']; ?></td>

                <?php for ($p = 1; $p <= 7; $p++) { ?>
                <td>
                    <div class="radio-group">
                        <label class="present">
                            <input type="radio"
                                   name="attendance[<?php echo $row['student_id']; ?>][<?php echo $p; ?>]"
                                   value="Present" required> P
                        </label>
                        <label class="absent">
                            <input type="radio"
                                   name="attendance[<?php echo $row['student_id']; ?>][<?php echo $p; ?>]"
                                   value="Absent"> A
                        </label>
                    </div>
                </td>
                <?php } ?>
            </tr>
            <?php } ?>
        </table>

        <div class="attendance-actions">
            <a href="admin_dashboard.php" class="back-link">⬅ Back to Dashboard</a>
            <button type="submit" name="submit" class="submit-btn">
                ✅ Submit Attendance
            </button>
        </div>

    </form>
    <?php } ?>

</div>

</body>
</html>
