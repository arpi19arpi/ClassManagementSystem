<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: /ClassManagementSystem/login.php");
    exit;
}

$msg = "";
$error = "";

// Dropdowns
$departments = $conn->query("SELECT dept_ID, dept_name FROM Department ORDER BY dept_name");
$teachers    = $conn->query("SELECT teacher_ID, name FROM Teacher ORDER BY name");
$blackouts   = $conn->query("SELECT blackout_ID, date, start_time, end_time FROM Blackout ORDER BY date, start_time");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $class_name = $_POST["class_name"];
    $section_no = (int)$_POST["section_no"];
    $dept_ID    = (int)$_POST["dept_ID"];
    $teacher_ID = (int)$_POST["teacher_ID"];
    $blackout_ID = (int)$_POST["blackout_ID"];
    $date_slot   = $_POST["date_slot"]; // YYYY-MM-DD

    // if we have the same teacher + same blackout + same date
    $sqlCheck = "
        SELECT COUNT(*) AS cnt
        FROM Class
        WHERE teacher_ID = ?
          AND blackout_ID = ?
          AND date_slot = ?
    ";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("iis", $teacher_ID, $blackout_ID, $date_slot);
    $stmtCheck->execute();
    $conf = $stmtCheck->get_result()->fetch_assoc();

    if ($conf["cnt"] > 0) {
        $error = "This teacher already has a class in that time slot on this date.";
    } else {
        // generate new class ID
        $res = $conn->query("SELECT COALESCE(MAX(class_ID) + 1, 1) AS new_id FROM Class");
        $row = $res->fetch_assoc();
        $newClassId = (int)$row["new_id"];

        // insert a class
        $sqlInsert = "
            INSERT INTO Class (class_ID, class_name, section_no, dept_ID, teacher_ID, blackout_ID, date_slot)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";
        $stmt = $conn->prepare($sqlInsert);
        $stmt->bind_param(
            "isiiiss",
            $newClassId,
            $class_name,
            $section_no,
            $dept_ID,
            $teacher_ID,
            $blackout_ID,
            $date_slot
        );

        if ($stmt->execute()) {
            $msg = "Class added successfully.";
        } else {
            $error = "Error adding class: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add Class</title>
<style>
body { font-family: Arial; background:#eef2ff; margin:0; }
.container { max-width:700px; margin:30px auto; padding:20px; }
.card { background:white; padding:20px; border-radius:12px; box-shadow:0 3px 10px rgba(0,0,0,0.1); }
label { display:block; font-weight:bold; margin-top:10px; }
input, select { width:100%; padding:8px; margin-top:4px; box-sizing:border-box; }
button { margin-top:15px; padding:10px 20px; background:#2b6cb0; color:white; border:none; border-radius:5px; cursor:pointer; }
.msg { color:green; }
.error { color:red; }
a.back { text-decoration:none; color:#2b6cb0; }
</style>
</head>
<body>
<div class="container">
    <a class="back" href="classes_list.php">&larr; Back to Classes</a>
    <div class="card">
        <h2>Add New Class</h2>

        <?php if ($msg): ?><p class="msg"><?= htmlspecialchars($msg); ?></p><?php endif; ?>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error); ?></p><?php endif; ?>

        <form method="POST">
            <label>Class Name</label>
            <input type="text" name="class_name" required>

            <label>Section Number</label>
            <input type="number" name="section_no" min="1" required>

            <label>Department</label>
            <select name="dept_ID" required>
                <option value="">Select Department</option>
                <?php
                // reset pointer
                $departments->data_seek(0);
                while ($d = $departments->fetch_assoc()): ?>
                    <option value="<?= $d['dept_ID']; ?>"><?= htmlspecialchars($d['dept_name']); ?></option>
                <?php endwhile; ?>
            </select>

            <label>Teacher</label>
            <select name="teacher_ID" required>
                <option value="">Select Teacher</option>
                <?php
                $teachers->data_seek(0);
                while ($t = $teachers->fetch_assoc()): ?>
                    <option value="<?= $t['teacher_ID']; ?>"><?= htmlspecialchars($t['name']); ?></option>
                <?php endwhile; ?>
            </select>

            <label>Time Slot (Blackout)</label>
            <select name="blackout_ID" required>
                <option value="">Select Time Slot</option>
                <?php
                $blackouts->data_seek(0);
                while ($b = $blackouts->fetch_assoc()): ?>
                    <option value="<?= $b['blackout_ID']; ?>">
                        <?= $b['date']; ?> — <?= substr($b['start_time'],0,5); ?> to <?= substr($b['end_time'],0,5); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Class Date</label>
            <input type="date" name="date_slot" required>

            <button type="submit">Add Class</button>
        </form>
    </div>
</div>
</body>
</html>

