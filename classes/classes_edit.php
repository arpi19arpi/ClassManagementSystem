<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: classes_list.php");
    exit;
}

// Fetch the class record
$sql = "SELECT * FROM Class WHERE class_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();

if (!$class) {
    echo "Class not found.";
    exit;
}

// Fetch dropdown data
$departments = $conn->query("SELECT * FROM Department ORDER BY dept_name");
$teachers    = $conn->query("SELECT * FROM Teacher ORDER BY name");
$blackouts   = $conn->query("SELECT * FROM Blackout ORDER BY date");

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name      = $_POST["class_name"];
    $section   = $_POST["section_no"];
    $dept      = $_POST["dept_ID"];
    $teacher   = $_POST["teacher_ID"];
    $blackout  = $_POST["blackout_ID"];
    $date_slot = $_POST["date_slot"];

    $sql = "UPDATE Class 
            SET class_name = ?, 
                section_no = ?, 
                dept_ID = ?, 
                teacher_ID = ?, 
                blackout_ID = ?, 
                date_slot = ?
            WHERE class_ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiissi",
        $name, $section, $dept,
        $teacher, $blackout, $date_slot, $id
    );

    if ($stmt->execute()) {
        header("Location: classes_list.php");
        exit;
    } else {
        $msg = "Error updating class: " . $conn->error;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Class</title>
<style>
body { font-family: Arial; padding: 20px; }
input, select { padding: 10px; width: 300px; margin-bottom: 15px; }
button { padding: 10px 20px; background: #2b6cb0; color: white; border: none; border-radius: 6px; }
</style>
</head>
<body>

<h1>Edit Class</h1>

<p style="color:red;"><?= $msg ?></p>

<form method="POST">

    <label>Class Name:</label><br>
    <input type="text" name="class_name" value="<?= $class['class_name'] ?>" required><br>

    <label>Section Number:</label><br>
    <input type="number" name="section_no" value="<?= $class['section_no'] ?>" required><br>

    <label>Department:</label><br>
    <select name="dept_ID" required>
        <?php while ($d = $departments->fetch_assoc()): ?>
            <option value="<?= $d['dept_ID'] ?>"
                <?= ($class['dept_ID'] == $d['dept_ID']) ? "selected" : "" ?>>
                <?= $d['dept_name'] ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    <label>Teacher:</label><br>
    <select name="teacher_ID" required>
        <?php while ($t = $teachers->fetch_assoc()): ?>
            <option value="<?= $t['teacher_ID'] ?>"
                <?= ($class['teacher_ID'] == $t['teacher_ID']) ? "selected" : "" ?>>
                <?= $t['name'] ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    <label>Blackout Schedule:</label><br>
    <select name="blackout_ID" required>
        <?php while ($b = $blackouts->fetch_assoc()): ?>
            <option value="<?= $b['blackout_ID'] ?>"
                <?= ($class['blackout_ID'] == $b['blackout_ID']) ? "selected" : "" ?>>
                <?= $b['date'] . " | " . $b['start_time'] . " - " . $b['end_time'] ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    <label>Date Slot:</label><br>
    <input type="date" name="date_slot" value="<?= $class['date_slot'] ?>" required><br>

    <button type="submit">Save Changes</button>

</form>

<br>
<a href="classes_list.php">Back to Class List</a>

</body>
</html>

