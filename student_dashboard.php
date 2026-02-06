<?php
session_start();
include 'db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$name = $_SESSION['student_name'] ?? 'Student';

/* Overall attendance */
$total_q = mysqli_query($conn,
    "SELECT COUNT(*) AS total FROM attendance WHERE student_id='$student_id'"
);
$total = mysqli_fetch_assoc($total_q)['total'];

$present_q = mysqli_query($conn,
    "SELECT COUNT(*) AS present FROM attendance WHERE student_id='$student_id' AND status='Present'"
);
$present = mysqli_fetch_assoc($present_q)['present'];

$percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;

/* Daily attendance */
$daily = mysqli_query($conn, "
    SELECT date,
           COUNT(*) AS total,
           SUM(status='Present') AS present
    FROM attendance
    WHERE student_id='$student_id'
    GROUP BY date
    ORDER BY date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
<link rel="stylesheet" href="style.css">

<style>
body {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
    margin: 0;
    padding: 30px;
    background: linear-gradient(135deg,#4facfe,#8f94fb);
    font-family: Arial, sans-serif;
}

.dashboard {
    width: 100%;
    max-width: 1000px;
}

/* HEADER */
.header {
    background: white;
    padding: 20px;
    border-radius: 16px;
    margin-bottom: 20px;
}

/* STATS */
.stats {
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.card {
    background: white;
    padding: 20px;
    border-radius: 16px;
    text-align: center;
    font-weight: bold;
}

.card span {
    font-size: 28px;
    display: block;
    margin-top: 10px;
}

/* PROGRESS */
.progress-box {
    background: white;
    padding: 20px;
    border-radius: 16px;
    margin-bottom: 25px;
}

.progress-bar {
    height: 16px;
    background: #ddd;
    border-radius: 10px;
    overflow: hidden;
}

.progress {
    height: 100%;
    width: <?= $percentage ?>%;
    background: <?= ($percentage >= 75) ? '#2ecc71' : '#e74c3c' ?>;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 16px;
    overflow: hidden;
}

th {
    background: #4a6cf7;
    color: white;
    padding: 12px;
}

td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #eee;
}

.good { color: green; font-weight: bold; }
.bad { color: red; font-weight: bold; }

/* ACTIONS */
.actions {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
}

.actions a {
    text-decoration: none;
    padding: 12px 20px;
    border-radius: 12px;
    background: white;
    font-weight: bold;
    color: #333;
}
</style>
</head>

<body>

<div class="dashboard">

<!-- HEADER -->
<div class="header">
    <h2>👋 Welcome, <?= $name ?></h2>
    <p>Your attendance overview</p>
</div>

<!-- STATS -->
<div class="stats">
    <div class="card">
        Total Classes
        <span><?= $total ?></span>
    </div>

    <div class="card">
        Classes Attended
        <span><?= $present ?></span>
    </div>

    <div class="card">
        Attendance %
        <span><?= $percentage ?>%</span>
    </div>
</div>

<!-- PROGRESS -->
<div class="progress-box">
    <p><b>Overall Attendance Progress</b></p>
    <div class="progress-bar">
        <div class="progress"></div>
    </div>
    <p style="margin-top:8px;">
        <?= ($percentage >= 75) ? '✅ Eligible' : '⚠ Attendance Shortage' ?>
    </p>
</div>

<!-- DAILY TABLE -->
<table>
<tr>
    <th>Date</th>
    <th>Periods Attended</th>
    <th>Total Periods</th>
    <th>Daily %</th>
</tr>

<?php while ($r = mysqli_fetch_assoc($daily)) {
    $daily_per = round(($r['present'] / $r['total']) * 100, 2);
?>
<tr>
    <td><?= $r['date'] ?></td>
    <td><?= $r['present'] ?></td>
    <td><?= $r['total'] ?></td>
    <td class="<?= ($daily_per >= 75) ? 'good' : 'bad' ?>">
        <?= $daily_per ?>%
    </td>
</tr>
<?php } ?>
</table>

<!-- ACTION BUTTONS -->
<div class="actions">
    <a href="student_attendance_report.php">📊 Detailed Report</a>
    <a href="logout.php">🚪 Logout</a>
</div>

</div>

</body>
</html>
