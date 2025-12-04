<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$sql = "SELECT t.teacher_ID, t.name, t.teacher_code, d.dept_name
        FROM Teacher t
        JOIN Department d ON t.dept_ID = d.dept_ID
        ORDER BY t.teacher_ID";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
<title>Teachers</title>
<style>
body { font-family: Arial; background: #eef2ff; padding: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 12px; border-bottom: 1px solid #ccc; text-align: left; }
a.button { padding: 10px 14px; background: #2b6cb0; color: white; text-decoration: none; border-radius: 6px; }
a.button:hover { background: #1e4f80; }
</style>
</head>
<body>

<h1>Teachers</h1>

<a class="button" href="teachers_add.php">+ Add New Teacher</a>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Code</th>
    <th>Department</th>
    <th>Actions</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['teacher_ID'] ?></td>
    <td><?= $row['name'] ?></td>
    <td><?= $row['teacher_code'] ?></td>
    <td><?= $row['dept_name'] ?></td>
    <td>
        <a href="teachers_edit.php?id=<?= $row['teacher_ID'] ?>">Edit</a> |
        <a href="teachers_delete.php?id=<?= $row['teacher_ID'] ?>"
           onclick="return confirm('Delete this teacher?');">Delete</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>

