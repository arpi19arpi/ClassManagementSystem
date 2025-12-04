<?php
require "../includes/auth.php";
require "../config/db.php";

if (!in_array($_SESSION["role"], ["Admin", "Secretary", "Student"])) {
    header("Location: ../login.php");
    exit;
}


$sql = "SELECT c.class_ID, c.class_name, c.section_no, c.date_slot,
               d.dept_name,
               t.name AS teacher_name,
               b.date AS blackout_date,
               b.start_time,
               b.end_time
        FROM Class c
        JOIN Department d ON c.dept_ID = d.dept_ID
        JOIN Teacher t ON c.teacher_ID = t.teacher_ID
        JOIN Blackout b ON c.blackout_ID = b.blackout_ID
        ORDER BY c.class_ID";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
<title>Classes</title>
<style>
body { font-family: Arial; background: #eef2ff; padding: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 12px; border-bottom: 1px solid #ccc; text-align: left; }
a.button { padding: 10px 14px; background: #2b6cb0; color: white; text-decoration: none; border-radius: 6px; }
a.button:hover { background: #1e4f80; }
</style>
</head>
<body>

<h1>Classes</h1>

<a class="button" href="classes_add.php">+ Add New Class</a>

<table>
<tr>
    <th>ID</th>
    <th>Class Name</th>
    <th>Section</th>
    <th>Department</th>
    <th>Teacher</th>
    <th>Date Slot</th>
    <th>Time</th>
    <th>Actions</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['class_ID'] ?></td>
    <td><?= $row['class_name'] ?></td>
    <td><?= $row['section_no'] ?></td>
    <td><?= $row['dept_name'] ?></td>
    <td><?= $row['teacher_name'] ?></td>
    <td><?= $row['date_slot'] ?></td>
    <td><?= $row['start_time'] . " - " . $row['end_time'] ?></td>
    <td>
        <a href="classes_edit.php?id=<?= $row['class_ID'] ?>">Edit</a> |
        <a href="classes_delete.php?id=<?= $row['class_ID'] ?>"
           onclick="return confirm('Delete this class?');">Delete</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>

