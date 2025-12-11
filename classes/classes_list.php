<?php
require "../includes/auth.php";
require "../config/db.php";

if (!in_array($_SESSION["role"], ["Admin", "Secretary", "Student"])) {
    header("Location: ../login.php");
    exit;
}

$role = $_SESSION["role"];

// all class names for dropdown
$classNames = $conn->query("SELECT DISTINCT class_name FROM Class ORDER BY class_name");

// all departments for dropdown
$departments = $conn->query("
    SELECT dept_ID, dept_name 
    FROM Department
    ORDER BY dept_name
");
$selectedClass = $_GET['class_name'] ?? '';
$selectedDept  = $_GET['dept_ID'] ?? '';

// bbase query
$sql = "
    SELECT 
        c.class_ID, 
        c.class_name, 
        c.section_no, 
        c.date_slot,
        d.dept_name,
        t.name AS teacher_name,
        b.start_time,
        b.end_time
    FROM Class c
    JOIN Department d ON c.dept_ID = d.dept_ID
    JOIN Teacher t ON c.teacher_ID = t.teacher_ID
    JOIN Blackout b ON c.blackout_ID = b.blackout_ID
    WHERE 1=1
";

//parameters array
$params = [];
$types  = "";

// add filters
if ($selectedClass !== '') {
    $sql .= " AND c.class_name = ? ";
    $params[] = $selectedClass;
    $types .= "s";
}

if ($selectedDept !== '') {
    $sql .= " AND d.dept_ID = ? ";
    $params[] = $selectedDept;
    $types .= "i";
}

$sql .= " ORDER BY c.class_ID ";

$stmt = $conn->prepare($sql);

if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

//gives the optional messages
$msg   = $_GET['msg'] ?? '';
$error = ($_GET['error'] ?? '') === '1';
?>
<!DOCTYPE html>
<html>
<head>
<title>Classes</title>
<style>
body { font-family: Arial; background: #eef2ff; padding: 20px; margin: 0; }
h1 { margin-top: 0; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; }
th, td { padding: 12px; border-bottom: 1px solid #ccc; text-align: left; }
th { background: #e5edff; }
a.button, button.button {
    padding: 10px 14px;
    background: #2b6cb0;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-size: 14px;
}
a.button:hover, button.button:hover { background: #1e4f80; }

.actions a { margin-right: 8px; }

.message {
    padding: 12px;
    border-radius: 6px;
    margin-top: 10px;
    max-width: 600px;
}
.message.success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
.message.error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

.search-form {
    margin-top: 10px;
    margin-bottom: 15px;
}
.search-form select {
    padding: 8px 10px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    min-width: 180px;
}
.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
.back-link {
    text-decoration: none;
    color: #2b6cb0;
    font-size: 14px;
}
</style>
</head>
<body>

<div class="top-bar">
    <div>
        <h1>Classes</h1>

        <?php if ($role === "Student"): ?>
            <a href="../student_dashboard.php" class="back-link">&larr; Back to Student Dashboard</a>
        <?php endif; ?>
    </div>

    <div>
        <?php if (in_array($role, ["Admin", "Secretary"])): ?>
            <a class="button" href="classes_add.php">+ Add New Class</a>
        <?php endif; ?>
    </div>
</div>

<!-- Flash Messages -->
<?php if ($msg): ?>
    <div class="message <?= $error ? 'error' : 'success' ?>">
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- FILTER FORM -->
<form method="get" class="search-form">

    <label><strong>Class Name:</strong></label>
    <select name="class_name" onchange="this.form.submit()">
        <option value="">-- All Classes --</option>
        <?php while ($cn = $classNames->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($cn['class_name']) ?>"
                <?= ($selectedClass == $cn['class_name']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cn['class_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    &nbsp;&nbsp;&nbsp;

    <label><strong>Department:</strong></label>
    <select name="dept_ID" onchange="this.form.submit()">
        <option value="">-- All Departments --</option>
        <?php while ($dp = $departments->fetch_assoc()): ?>
            <option value="<?= $dp['dept_ID'] ?>"
                <?= ($selectedDept == $dp['dept_ID']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($dp['dept_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <noscript><button type="submit" class="button">Filter</button></noscript>
</form>

<!-- CLASSES TABLE -->
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

<?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['class_ID'] ?></td>
        <td><?= htmlspecialchars($row['class_name']) ?></td>
        <td><?= htmlspecialchars($row['section_no']) ?></td>
        <td><?= htmlspecialchars($row['dept_name']) ?></td>
        <td><?= htmlspecialchars($row['teacher_name']) ?></td>
        <td><?= htmlspecialchars($row['date_slot']) ?></td>
        <td>
            <?= substr($row['start_time'], 0, 5) ?> -
            <?= substr($row['end_time'], 0, 5) ?>
        </td>

        <td class="actions">
            <?php if (in_array($role, ["Admin", "Secretary"])): ?>
                <a href="classes_edit.php?id=<?= $row['class_ID'] ?>">Edit</a> |
                <a href="classes_delete.php?id=<?= $row['class_ID'] ?>"
                   onclick="return confirm('Delete this class?');">Delete</a>

            <?php elseif ($role === "Student"): ?>
                <form method="post" action="enroll.php" style="display:inline;">
                    <input type="hidden" name="class_id" value="<?= $row['class_ID'] ?>">
                    <button type="submit" class="button">Enroll</button>
                </form>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>

<?php else: ?>
    <tr><td colspan="8">No classes available.</td></tr>
<?php endif; ?>

</table>

</body>
</html>
