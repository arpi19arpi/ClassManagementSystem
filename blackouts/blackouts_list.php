<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$sql = "SELECT * FROM Blackout ORDER BY date, start_time";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Blackout Times</title>
<style>
body { font-family: Arial; background:#eef2ff; margin:0; }
.header { background:#2b6cb0; color:white; padding:20px; }
.container { padding:30px; }
.card { background:white; padding:20px; border-radius:12px; box-shadow:0 3px 10px rgba(0,0,0,0.08); }
table { width:100%; border-collapse:collapse; margin-top:15px; }
th,td { padding:10px; border-bottom:1px solid #ddd; }
th { background:#edf2ff; }
a.button { padding:8px 12px; background:#2b6cb0; color:white; text-decoration:none; border-radius:5px; }
a.delete { color:red; }
</style>
</head>
<body>

<div class="header">
    <h1>Blackout Time Slots</h1>
</div>

<div class="container">
    <a class="button" href="blackouts_add.php">+ Add Blackout</a>

    <div class="card">
        <table>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Start</th>
                <th>End</th>
                <th>Actions</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['blackout_ID']; ?></td>
                <td><?= $row['date']; ?></td>
                <td><?= substr($row['start_time'],0,5); ?></td>
                <td><?= substr($row['end_time'],0,5); ?></td>
                <td>
                    <a href="blackouts_edit.php?id=<?= $row['blackout_ID']; ?>">Edit</a> |
                    <a class="delete" href="blackouts_delete.php?id=<?= $row['blackout_ID']; ?>"
                       onclick="return confirm('Delete this blackout?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

</body>
</html>

