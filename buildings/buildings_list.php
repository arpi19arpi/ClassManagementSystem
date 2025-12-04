<?php
require "../includes/auth.php";
require "../config/db.php";

// Only Admin should see this
if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

// Fetching all buildings
$sql = "SELECT * FROM Building ORDER BY building_ID";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
<title>Buildings</title>
<style>
body { font-family: Arial; background: #f4f7ff; padding: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 12px; border-bottom: 1px solid #ccc; text-align: left; }
a.button { padding: 10px 14px; background: #2b6cb0; color: white; text-decoration: none; border-radius: 6px; }
a.button:hover { background: #1e4f80; }
</style>
</head>
<body>

<h1>Buildings</h1>

<a class="button" href="buildings_add.php">+ Add New Building</a>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Location</th>
    <th>Actions</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['building_ID'] ?></td>
    <td><?= $row['building_name'] ?></td>
    <td><?= $row['location'] ?></td>
    <td>
        <a href="buildings_edit.php?id=<?= $row['building_ID'] ?>">Edit</a> |
        <a href="buildings_delete.php?id=<?= $row['building_ID'] ?>"
           onclick="return confirm('Delete this building?');">
            Delete
        </a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>

