<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$sql = "SELECT a.assign_ID, 
               c.class_name, 
               c.section_no,
               r.room_No,
               b.building_name,
               u.name AS assigned_by
        FROM Assignment a
        JOIN Class c ON a.class_ID = c.class_ID
        JOIN Classroom r ON a.room_ID = r.room_ID
        JOIN Building b ON r.building_ID = b.building_ID
        JOIN User u ON a.assigned_by = u.user_ID
        ORDER BY a.assign_ID";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
<title>Assignments</title>
<style>
body { font-family: Arial; background: #eef2ff; padding: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 12px; border-bottom: 1px solid #ccc; text-align: left; }
a.button { padding: 10px 14px; background: #2b6cb0; color: white; text-decoration: none; border-radius: 6px; }
a.button:hover { background: #1e4f80; }
</style>
</head>
<body>

<h1>Classroom Assignments</h1>

<a class="button" href="assignments_add.php">+ Add New Assignment</a>

<table>
<tr>
    <th>ID</th>
    <th>Class</th>
    <th>Section</th>
    <th>Room</th>
    <th>Building</th>
    <th>Assigned By</th>
    <th>Actions</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['assign_ID'] ?></td>
    <td><?= $row['class_name'] ?></td>
    <td><?= $row['section_no'] ?></td>
    <td><?= $row['room_No'] ?></td>
    <td><?= $row['building_name'] ?></td>
    <td><?= $row['assigned_by'] ?></td>
    <td>
        <a href="assignments_edit.php?id=<?= $row['assign_ID'] ?>">Edit</a> |
        <a href="assignments_delete.php?id=<?= $row['assign_ID'] ?>"
           onclick="return confirm('Delete this assignment?');">Delete</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>
