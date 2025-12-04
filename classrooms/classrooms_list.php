<?php
require "../includes/auth.php";
require "../config/db.php";

if (!in_array($_SESSION["role"], ["Admin", "Secretary"])) {
    header("Location: ../login.php");
    exit;
}


$sql = "SELECT c.room_ID, c.room_No, c.capacity, b.building_name 
        FROM Classroom c 
        JOIN Building b ON c.building_ID = b.building_ID
        ORDER BY c.room_ID";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
<title>Classrooms</title>
<style>
body { font-family: Arial; background: #eef2ff; padding: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 12px; border-bottom: 1px solid #ccc; text-align: left; }
a.button { padding: 10px 14px; background: #2b6cb0; color: white; text-decoration: none; border-radius: 6px; }
a.button:hover { background: #1e4f80; }
</style>
</head>
<body>

<h1>Classrooms</h1>

<a class="button" href="classrooms_add.php">+ Add New Classroom</a>

<table>
<tr>
    <th>ID</th>
    <th>Room Number</th>
    <th>Building</th>
    <th>Capacity</th>
    <th>Actions</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['room_ID'] ?></td>
    <td><?= $row['room_No'] ?></td>
    <td><?= $row['building_name'] ?></td>
    <td><?= $row['capacity'] ?></td>
    <td>
        <a href="classrooms_edit.php?id=<?= $row['room_ID'] ?>">Edit</a> |
        <a href="classrooms_delete.php?id=<?= $row['room_ID'] ?>"
           onclick="return confirm('Delete this classroom?');">Delete</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>

