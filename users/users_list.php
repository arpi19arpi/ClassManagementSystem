<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

// Fetch all users + department names
$sql = "SELECT u.*, d.dept_name 
        FROM User u
        LEFT JOIN Department d ON u.dept_ID = d.dept_ID
        ORDER BY u.user_ID";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
<title>Users</title>
<style>
body { font-family: Arial; background: #eef2ff; padding: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 12px; border-bottom: 1px solid #ccc; text-align: left; }
a.button { padding: 10px 14px; background: #2b6cb0; color: white; text-decoration: none; border-radius: 6px; }
a.button:hover { background: #1e4f80; }
</style>
</head>
<body>

<h1>Users</h1>

<a class="button" href="users_add.php">+ Add New User</a>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Username</th>
    <th>Email</th>
    <th>Role</th>
    <th>Department</th>
    <th>Semester</th>
    <th>Enroll No</th>
    <th>Actions</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['user_ID'] ?></td>
    <td><?= $row['name'] ?></td>
    <td><?= $row['username'] ?></td>
    <td><?= $row['email'] ?></td>
    <td><?= $row['role'] ?></td>
    <td><?= $row['dept_name'] ?? "-" ?></td>
    <td><?= $row['semester'] ?? "-" ?></td>
    <td><?= $row['enroll_no'] ?? "-" ?></td>
    <td>
        <a href="users_edit.php?id=<?= $row['user_ID'] ?>">Edit</a> |
        <a href="users_delete.php?id=<?= $row['user_ID'] ?>"
           onclick="return confirm('Delete this user?');">Delete</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>

