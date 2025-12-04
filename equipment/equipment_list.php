<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$sql = "SELECT e.equipment_ID, e.name, 
               c.room_No, b.building_name
        FROM Equipment e
        JOIN Classroom c ON e.classroom_ID = c.room_ID
        JOIN Building b ON c.building_ID = b.building_ID
        ORDER BY e.equipment_ID";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
<title>Equipment</title>
<style>
body { font-family: Arial; background: #eef2ff; padding: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 12px; border-bottom: 1px solid #ccc; }
a.button {
    padding: 10px 14px; background: #2b6cb0; color: white;
    text-decoration: none; border-radius: 6px;
}
a.button:hover { background: #1e4f80; }
</style>
</head>
<body>

<h1>Equipment List</h1>

<a class="button" href="equipment_add.php">+ Add Equipment</a>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Room</th>
    <th>Building</th>
    <th>Actions</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['equipment_ID'] ?></td>
    <td><?= $row['name'] ?></td>
    <td><?= $row['room_No'] ?></td>
    <td><?= $row['building_name'] ?></td>
    <td>
        <a href="equipment_edit.php?id=<?= $row['equipment_ID'] ?>">Edit</a> |
        <a href="equipment_delete.php?id=<?= $row['equipment_ID'] ?>"
           onclick="return confirm('Delete this equipment?');">Delete</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>

