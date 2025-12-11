<?php
require "../includes/auth.php";
require "../config/db.php";

if (!in_array($_SESSION["role"], ["Admin", "Secretary"])) {
    header("Location: ../login.php");
    exit;
}

$sql = "
    SELECT 
        c.class_name, c.section_no, c.date_slot,
        d.dept_name,
        t.name AS teacher_name,
        b.start_time, b.end_time,
        cr.room_No, cr.capacity,
        bl.building_name
    FROM Assignment a
    JOIN Class c ON a.class_ID = c.class_ID
    JOIN Department d ON c.dept_ID = d.dept_ID
    JOIN Teacher t ON c.teacher_ID = t.teacher_ID
    JOIN Blackout b ON c.blackout_ID = b.blackout_ID
    JOIN Classroom cr ON a.room_ID = cr.room_ID
    JOIN Building bl ON cr.building_ID = bl.building_ID
    ORDER BY c.date_slot, b.start_time
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Room Assignment Report</title>
<style>
body { font-family: Arial; background:#eef2ff; padding:20px; }
table { width:100%; border-collapse:collapse; background:white; }
th,td { padding:12px; border-bottom:1px solid #ccc; }
th { background:#dbeafe; }
</style>
</head>
<body>

<h1>Room Assignment Report</h1>

<table>
<tr>
    <th>Class</th>
    <th>Section</th>
    <th>Department</th>
    <th>Teacher</th>
    <th>Date</th>
    <th>Time</th>
    <th>Room</th>
    <th>Building</th>
    <th>Capacity</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['class_name'] ?></td>
    <td><?= $row['section_no'] ?></td>
    <td><?= $row['dept_name'] ?></td>
    <td><?= $row['teacher_name'] ?></td>
    <td><?= $row['date_slot'] ?></td>
    <td><?= substr($row['start_time'],0,5) ?> - <?= substr($row['end_time'],0,5) ?></td>
    <td><?= $row['room_No'] ?></td>
    <td><?= $row['building_name'] ?></td>
    <td><?= $row['capacity'] ?></td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>

