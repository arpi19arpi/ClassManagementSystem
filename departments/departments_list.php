<?php
require "../includes/auth.php";
require "../config/db.php";

if (!in_array($_SESSION["role"], ["Admin", "Secretary"])) {
    header("Location: ../login.php");
    exit;
}

// Fetch all departments with building names
$sql = "SELECT d.dept_ID, d.dept_name, b.building_name 
        FROM Department d
        JOIN Building b ON d.building_ID = b.building_ID
        ORDER BY d.dept_ID";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
<title>Departments</title>
<style>
body { font-family: Arial; background: #f4f6ff; padding: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 12px; border-bottom: 1px solid #ccc; text-align: left; }
a.button { padding: 10px 14px; background: #2b6cb0; color: white; text-decoration: none; border-radius: 6px; }
a.button:hover { background: #1e4f80; }
</style>
</head>
<body>

<h1>Departments</h1>

<a class="button" href="departments_add.php">+ Add New Department</a>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Building</th>
    <th>Actions</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row["dept_ID"] ?></td>
    <td><?= $row["dept_name"] ?></td>
    <td><?= $row["building_name"] ?></td>
    <td>
        <a href="departments_edit.php?id=<?= $row['dept_ID'] ?>">Edit</a> |
        <a href="departments_delete.php?id=<?= $row['dept_ID'] ?>"
           onclick="return confirm('Delete this department?');">Delete</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>

