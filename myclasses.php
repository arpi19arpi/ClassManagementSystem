<?php
require "includes/auth.php";
require "config/db.php";

// only allow logged-in students
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "Student") {
    header("Location: login.php");
    exit;
}

$studentId = $_SESSION['user_id'];

//all classes enrolled by this student
$sql = "
    SELECT 
        c.class_ID,
        c.class_name,
        c.section_no,
        c.date_slot,
        d.dept_name,
        t.name AS teacher_name,
        b.start_time,
        b.end_time,
        cr.room_No,
        bl.building_name
    FROM Student_Class sc
    INNER JOIN Class c ON sc.class_ID = c.class_ID
    INNER JOIN Department d ON c.dept_ID = d.dept_ID
    INNER JOIN Teacher t ON c.teacher_ID = t.teacher_ID
    INNER JOIN Blackout b ON c.blackout_ID = b.blackout_ID
    LEFT JOIN Assignment a ON c.class_ID = a.class_ID
    LEFT JOIN Classroom cr ON a.room_ID = cr.room_ID
    LEFT JOIN Building bl ON cr.building_ID = bl.building_ID
    WHERE sc.student_ID = ?
    ORDER BY c.date_slot, b.start_time
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Classes</title>
    <style>
        body { font-family: Arial; background: #eef2ff; margin: 0; }
        .header { background: #2b6cb0; color: white; padding: 20px; }
        .container { padding: 30px; }

        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            max-width: 1100px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        th { background: #edf2ff; text-align: left; }
        tr:hover { background: #f8faff; }

        .back { color: #2b6cb0; text-decoration: none; font-size: 14px; }

        .drop-btn {
            background: #c62828;
            color: white;
            padding: 6px 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .drop-btn:hover {
            background: #8e0000;
        }

        .msg-success {
            padding: 12px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .msg-error {
            padding: 12px;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            margin-bottom: 20px;
        }

    </style>
</head>

<body>

<div class="header">
    <h1>My Classes</h1>
</div>

<div class="container">
    <a href="student_dashboard.php" class="back">&larr; Back to Dashboard</a>

    <div class="card">

        <!-- Success / Error Messages -->
        <?php if (isset($_GET['msg']) && !isset($_GET['error'])): ?>
            <div class="msg-success"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="msg-error"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <h2>Enrolled Classes</h2>

        <table>
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Section</th>
                    <th>Department</th>
                    <th>Teacher</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Room</th>
                    <th>Building</th>
                    <th>Drop</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($result->num_rows === 0): ?>
                    <tr>
                        <td colspan="9">You are not enrolled in any classes.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['class_name']); ?></td>
                            <td><?= htmlspecialchars($row['section_no']); ?></td>
                            <td><?= htmlspecialchars($row['dept_name']); ?></td>
                            <td><?= htmlspecialchars($row['teacher_name']); ?></td>
                            <td><?= htmlspecialchars($row['date_slot']); ?></td>
                            <td>
                                <?= substr($row['start_time'], 0, 5) ?> -
                                <?= substr($row['end_time'], 0, 5) ?>
                            </td>
                            <td><?= htmlspecialchars($row['room_No'] ?? 'TBA'); ?></td>
                            <td><?= htmlspecialchars($row['building_name'] ?? 'TBA'); ?></td>
                            <td>
                                <form method="post" action="classes/drop.php" onsubmit="return confirm('Are you sure you want to drop this class?');">
                                    <input type="hidden" name="class_id" value="<?= $row['class_ID']; ?>">
                                    <button class="drop-btn">Drop</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>

        </table>

    </div>
</div>

</body>
</html>
