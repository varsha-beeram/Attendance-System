<?php
session_start();
include 'db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

/* Overall stats */
$total_q = mysqli_query($conn,
    "SELECT COUNT(*) AS total FROM attendance WHERE student_id='$student_id'"
);
$total = mysqli_fetch_assoc($total_q)['total'];

$present_q = mysqli_query($conn,
    "SELECT COUNT(*) AS present FROM attendance WHERE student_id='$student_id' AND status='Present'"
);
$present = mysqli_fetch_assoc($present_q)['present'];

$percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;

/* Date-wise attendance */
$dates = mysqli_query($conn, "
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
<html>
<head>
<title>Attendance Report</title>
<link rel="stylesheet" href="style.css">

<style>
body {
    display: flex;
    justify-content: center;
    padding: 30px;
    background: linear-gradient(135deg,#4facfe,#8f94fb);
    font-family: Arial, sans-serif;
}

.report {
    width: 100%;
    max-width: 900px;
}

/* HEADER */
.header {
    background: white;
    padding: 20px;
    border-radius: 16px;
    text-align: center;
    margin-bottom: 25px;
}

/* SUMMARY */
.summary {
    display: flex;
    justify-content: space-around;
    background: white;
    padding: 20px;
    border-radius: 16px;
    margin-bottom: 25px;
    font-weight: bold;
}

/* DATE CARD */
.date-card {
    background: white;
    padding: 20px;
    border-radius: 16px;
    margin-bottom: 20px;
}

.date-header {
    display: flex;
    justify-content: space-between;
    font-weight: bold;
    margin-bottom: 10px;
}

.bad { color: red; }
.good { color: green; }

.periods {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 10px;
}

.period {
    padding: 6px 10px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 14px;
}

.present {
    background: green;
    color: white;
}

.absent {
    background:red;
    color:white;
}

.back {
    display: inline-block;
    margin-top: 20px;
    background: white;
    padding: 12px 20px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: bold;
    color: #333;
}
</style>
</head>

<body>

<div class="report">

<!-- HEADER -->
<div class="header">
    <h2>📊 My Attendance Report</h2>
    <p>Overall performance</p>
</div>

<!-- SUMMARY -->
<div class="summary">
    <div>Total Classes: <?= $total ?></div>
    <div>Attended: <?= $present ?></div>
    <div>Percentage: <?= $percentage ?>%</div>
</div>

<!-- DATE WISE -->
<?php while ($d = mysqli_fetch_assoc($dates)) {

    $date = $d['date'];
    $daily_total = $d['total'];
    $daily_present = $d['present'];
    $daily_percent = round(($daily_present / $daily_total) * 100, 2);

    $periods_q = mysqli_query($conn, "
        SELECT period, status
        FROM attendance
        WHERE student_id='$student_id' AND date='$date'
        ORDER BY period
    ");
?>

<div class="date-card">
    <div class="date-header">
        <span>📅 <?= $date ?></span>
        <span class="<?= ($daily_percent >= 75) ? 'good' : 'bad' ?>">
            <?= $daily_present ?>/<?= $daily_total ?> (<?= $daily_percent ?>%)
        </span>
    </div>

    <div class="periods">
        <?php while ($p = mysqli_fetch_assoc($periods_q)) { ?>
            <div class="period <?= strtolower($p['status']) ?>">
                P<?= $p['period'] ?>
            </div>
        <?php } ?>
    </div>
</div>

<?php } ?>

<a href="student_dashboard.php" class="back">⬅ Back to Dashboard</a>

</div>

</body>
</html>
