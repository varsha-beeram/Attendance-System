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
$selected_period = $_POST['period'] ?? null;
$message = "";

/* Get completed periods */
$donePeriods = [];
$res = mysqli_query($conn, "
    SELECT DISTINCT period
    FROM attendance a
    JOIN students s ON a.student_id = s.student_id
    WHERE s.branch='$branch' AND a.date='$date'
");
while ($r = mysqli_fetch_assoc($res)) {
    $donePeriods[] = $r['period'];
}

/* Save attendance */
if (isset($_POST['submit']) && $selected_period && !in_array($selected_period, $donePeriods)) {

    $students_list = mysqli_query($conn,
        "SELECT student_id FROM students WHERE branch='$branch'"
    );

    while ($s = mysqli_fetch_assoc($students_list)) {
        $sid = $s['student_id'];
        $status = isset($_POST['attendance'][$sid]) ? 'Present' : 'Absent';

        mysqli_query($conn, "
            INSERT INTO attendance (student_id, date, period, status)
            VALUES ('$sid', '$date', '$selected_period', '$status')
        ");
    }

    header("Location: take_attendance_periodwise.php");
    exit();
}

/* Fetch students */
$students = mysqli_query($conn,
    "SELECT * FROM students WHERE branch='$branch' ORDER BY roll_no"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Period-wise Attendance</title>
<link rel="stylesheet" href="style.css">

<style>
.periods {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin: 20px 0;
}

.periods button {
    padding: 10px 16px;
    border-radius: 10px;
    border: none;
    font-weight: bold;
    background: #eef1ff;
    color: #333;
    cursor: pointer;
}

.periods .active {
    background: linear-gradient(135deg,#667eea,#764ba2);
    color: white;
}

.periods .done {
    background: #ccc;
    cursor: not-allowed;
}

.status-box {
    margin: 15px auto;
    padding: 15px;
    border-radius: 12px;
    background: #e7f3ff;
    color: #084298;
    font-weight: bold;
    text-align: center;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th {
    background: #4a6cf7;
    color: white;
    padding: 10px;
}

td {
    padding: 12px;
    text-align: center;
    background: #f9faff;
    border-bottom: 1px solid #ddd;
}

.checkbox-present {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    font-weight: bold;
    color: #2e7d32;
}

.checkbox-present input {
    transform: scale(1.3);
    cursor: pointer;
}

.submit-btn {
    margin-top: 20px;
    padding: 14px;
    width: 100%;
    border: none;
    border-radius: 14px;
    background: linear-gradient(135deg,#667eea,#764ba2);
    color: white;
    font-weight: bold;
    cursor: pointer;
}
</style>
</head>

<body>

<div class="container" style="width:90%;max-width:900px;">

<h2>Period-wise Attendance</h2>
<p class="subtitle">Branch: <?= $branch ?> | Date: <?= $date ?></p>

<div class="status-box">
    Today completed: <?= count($donePeriods) ?> / 7 periods
</div>

<!-- PERIOD BUTTONS -->
<form method="post" class="periods">
<?php for ($p=1;$p<=7;$p++): ?>
    <button name="period" value="<?= $p ?>"
        class="<?=
            in_array($p,$donePeriods) ? 'done' :
            ($selected_period==$p ? 'active' : '')
        ?>"
        <?= in_array($p,$donePeriods) ? 'disabled' : '' ?>>
        P<?= $p ?> <?= in_array($p,$donePeriods) ? '✔' : '' ?>
    </button>
<?php endfor; ?>
</form>

<?php if (!$selected_period): ?>
    <div class="status-box">
        👉 Please select a period above to take attendance.
    </div>
<?php endif; ?>

<?php if ($selected_period && !in_array($selected_period,$donePeriods)): ?>

<form method="post">
<input type="hidden" name="period" value="<?= $selected_period ?>">

<table>
<tr>
    <th>Roll No</th>
    <th>Name</th>
    <th>Present</th>
</tr>

<?php while ($s=mysqli_fetch_assoc($students)): ?>
<tr>
    <td><?= $s['roll_no'] ?></td>
    <td><?= $s['name'] ?></td>
    <td>
        <label class="checkbox-present">
            <input type="checkbox" name="attendance[<?= $s['student_id'] ?>]">
            Present
        </label>
    </td>
</tr>
<?php endwhile; ?>
</table>

<button type="submit" name="submit" class="submit-btn">
    Save Period <?= $selected_period ?>
</button>
</form>

<?php endif; ?>

<br>
<a href="admin_dashboard.php">⬅ Back to Dashboard</a>

</div>

</body>
</html>
