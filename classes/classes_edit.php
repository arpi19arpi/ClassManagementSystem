<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: /ClassManagementSystem/login.php");
    exit;
}

if (!isset($_GET["id"])) {
    header("Location: classes_list.php");
    exit;
}

$classId = (int)$_GET["id"];
$msg = "";
$error = "";

$sql = "SELECT * FROM Class WHERE class_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $classId);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();

if (!$class) {
    header("Location: classes_list.php");
    exit;
}

$departments = $conn->query("SELECT dept_ID, dept_name FROM Department ORDER BY dept_name");
$teachers    = $conn->query("SELECT teacher_ID, name FROM Teacher ORDER BY name");
$blackouts   = $conn->query("SELECT blackout_ID, date, start_time, end_time FROM Blackout ORDER BY date, start_time");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $class_name = $_POST["class_name"];
    $section_no = (int)$_POST["section_no"];
    $dept_ID    = (int)$_POST["dept_ID"];
    $teacher_ID = (int)$_POST["teacher_ID"];
    $blackout_ID = (int)$_POST["blackout_ID"];
    $date_slot   = $_POST["date_slot"];

    $sqlCheck = "
        SELECT COUNT(*) AS cnt
        FROM Class
        WHERE teacher_ID = ?
          AND blackout_ID = ?
          AND date_slot = ?
          AND class_ID <> ?
    ";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("iisi", $teacher_ID, $blackout_ID, $date_slot, $classId);
    $stmtCheck->execute();
    $conf = $stmtCheck->get_result()->fetch_assoc();

    if ($conf["cnt"] > 0) {
        $error = "This teacher already has a different class in that time slot on this date.";
    } else {
        $sqlUpdate = "
            UPDATE Class
            SET class_name = ?, section_no = ?, dept_ID = ?, teacher_ID = ?, blackout_ID = ?, date_slot = ?
            WHERE class_ID = ?
        ";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param(
            "siiissi",
            $class_name,
            $section_no,
            $dept_ID,
            $teacher_ID,
            $blackout_ID,
            $date_slot,
            $classId
        );

        if ($stmtUpdate->execute()) {
            $msg = "Class updated successfully.";
            $stmt = $conn->prepare("SELECT * FROM Class WHERE class_ID = ?");
            $stmt->bind_param("i", $classId);
            $stmt->execute();
            $class = $stmt->get_result()->fetch_assoc();
        } else {
            $error = "Error updating class: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Class</title>
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
        <h2>Edit Class</h2>

        <?php if ($msg): ?><p class="msg"><?= htmlspecialchars($msg); ?></p><?php endif; ?>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error); ?></p><?php endif; ?>

        <form method="POST">
            <label>Class Name</label>
            <input type="text" name="class_name" value="<?= htmlspecialchars($class['class_name']); ?>" required>

            <label>Section Number</label>
            <input type="number" name="section_no" value="<?= htmlspecialchars($class['section_no']); ?>" min="1" required>

            <label>Department</label>
            <select name="dept_ID" required>
                <?php
                $departments->data_seek(0);
                while ($d = $departments->fetch_assoc()):
                    $selected = "";
                    if ($class['dept_ID'] == $d['dept_ID']) {
                        $selected = "selected";
                    }
                ?>
                    <option value="<?= $d['dept_ID']; ?>" <?= $selected; ?>>
                        <?= htmlspecialchars($d['dept_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Teacher</label>
            <select name="teacher_ID" required>
                <?php
                $teachers->data_seek(0);
                while ($t = $teachers->fetch_assoc()):
                    $selected = "";
                    if ($class['teacher_ID'] == $t['teacher_ID']) {
                        $selected = "selected";
                    }
                ?>
                    <option value="<?= $t['teacher_ID']; ?>" <?= $selected; ?>>
                        <?= htmlspecialchars($t['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Time Slot (Blackout)</label>
            <select name="blackout_ID" required>
                <?php
                $blackouts->data_seek(0);
                while ($b = $blackouts->fetch_assoc()):
                    $selected = "";
                    if ($class['blackout_ID'] == $b['blackout_ID']) {
                        $selected = "selected";
                    }
                ?>
                    <option value="<?= $b['blackout_ID']; ?>" <?= $selected; ?>>
                        <?= $b['date']; ?> — <?= substr($b['start_time'],0,5); ?> to <?= substr($b['end_time'],0,5); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Class Date</label>
            <input type="date" name="date_slot" value="<?= htmlspecialchars($class['date_slot']); ?>" required>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>
</body>
</html>
