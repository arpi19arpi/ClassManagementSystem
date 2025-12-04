<?php
require "../includes/auth.php";
require "../config/db.php";

// Allow Admin and Secretary to access this page
if (!in_array($_SESSION["role"], ["Admin", "Secretary"])) {
    header("Location: ../login.php");
    exit;
}

// Secretary sees ONLY their own requests
if ($_SESSION["role"] === "Secretary") {

    $sql = "SELECT r.request_ID,
                   r.status,
                   c.class_name,
                   c.section_no,
                   d.dept_name,
                   b.date AS blackout_date,
                   b.start_time,
                   b.end_time,
                   r.date_slot,
                   u.name AS submitted_by,
                   u.user_ID AS submitted_by_id
            FROM Request r
            JOIN Class c ON r.class_ID = c.class_ID
            JOIN Department d ON r.dept_ID = d.dept_ID
            JOIN Blackout b ON r.blackout_ID = b.blackout_ID
            JOIN User u ON r.submitted_by = u.user_ID
            WHERE r.submitted_by = " . $_SESSION["user_id"] . "
            ORDER BY r.request_ID";

} else {

    // Admin sees all requests
    $sql = "SELECT r.request_ID,
                   r.status,
                   c.class_name,
                   c.section_no,
                   d.dept_name,
                   b.date AS blackout_date,
                   b.start_time,
                   b.end_time,
                   r.date_slot,
                   u.name AS submitted_by,
                   u.user_ID AS submitted_by_id
            FROM Request r
            JOIN Class c ON r.class_ID = c.class_ID
            JOIN Department d ON r.dept_ID = d.dept_ID
            JOIN Blackout b ON r.blackout_ID = b.blackout_ID
            JOIN User u ON r.submitted_by = u.user_ID
            ORDER BY r.request_ID";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
<title>Requests</title>
<style>
body { font-family: Arial; background: #f0f4ff; padding: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 12px; border-bottom: 1px solid #ccc; }
a.button { padding: 10px 14px; background: #2b6cb0; color: white; border-radius: 6px; text-decoration: none; }
a.button:hover { background: #1e4f80; }
</style>
</head>
<body>

<h1>Scheduling Requests</h1>

<?php if ($_SESSION["role"] === "Secretary"): ?>
    <a class="button" href="requests_add.php">+ Submit New Request</a>
<?php endif; ?>

<table>
<tr>
    <th>ID</th>
    <th>Class</th>
    <th>Section</th>
    <th>Department</th>
    <th>Requested Date</th>
    <th>Requested Time</th>
    <th>Submitted By</th>
    <th>Status</th>
    <th>Actions</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['request_ID'] ?></td>
    <td><?= $row['class_name'] ?></td>
    <td><?= $row['section_no'] ?></td>
    <td><?= $row['dept_name'] ?></td>
    <td><?= $row['date_slot'] ?></td>
    <td><?= $row['start_time'] . " - " . $row['end_time'] ?></td>
    <td><?= $row['submitted_by'] ?></td>
    <td><?= $row['status'] ?></td>

    <td>
        <?php if ($_SESSION["role"] === "Admin"): ?>

            <?php if ($row['status'] === "Pending"): ?>
                <a href="request_approve.php?id=<?= $row['request_ID'] ?>">Approve</a> |
            <?php endif; ?>

            <a href="requests_edit.php?id=<?= $row['request_ID'] ?>">Edit</a> |
            <a href="requests_delete.php?id=<?= $row['request_ID'] ?>"
               onclick="return confirm('Delete this request?');">Delete</a>

        <?php elseif ($row['submitted_by_id'] == $_SESSION["user_id"]): ?>

            <a href="requests_edit.php?id=<?= $row['request_ID'] ?>">Edit</a> |
            <a href="requests_delete.php?id=<?= $row['request_ID'] ?>"
               onclick="return confirm('Delete this request?');">Delete</a>

        <?php else: ?>
            <em>No actions</em>
        <?php endif; ?>
    </td>

</tr>
<?php endwhile; ?>

</table>

</body>
</html>
