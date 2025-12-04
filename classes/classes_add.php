<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$departments = $conn->query("SELECT * FROM Department ORDER BY dept_name");
$teachers = $conn->query("SELECT * FROM Teacher ORDER BY name");
$blackouts = $conn->query("SELECT * FROM Blackout ORDER BY date");

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["class_ID"];
    $name = $_POST["class_name"];
    $section = $_POST["section_no"];
    $dept = $_POST["dept_ID"];
    $teacher = $_POST["teacher_ID"];
    $blackout = $_POST["blackout_ID"];
    $date_slot = $_POST["date_slot"];

    $sql = "INSERT INTO Class (class_ID, class_name, section_no, dept_ID, teacher_ID, blackout_ID, date_slot)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isiiiss", $id, $name, $section, $dept, $teacher, $blackout, $date_slot);

    if ($stmt->execute()) {
        header("Location: classes_list.php");
        exit;
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add Class</title>
<style>
body { font-family: Arial; padding: 20px; }
input, select { padding: 10px; width: 300px; margin-bottom: 15px; }
button { padding: 10px 20px; background: #2b6cb0; color: white; border: none; border-radius: 6px; }
</style>
</head>
<body>

<h1>Add Class</h1>

<p style="color:red;"><?= $msg ?></p>

<form method="POST">
    <label>ID:</label><br>
    <input type="number" name="class_ID" required><br>

    <label>Class Name:</label><br>
    <input type="text" name="class_name" required><br>

    <label>Section Number:</label><br>
    <input type="number" name="section_no" required><br>

    <label>Department:</label><br>
    <select name="dept_ID" required>
        <option value="">Select Department</option>
        <?php while ($d = $departments->fetch_assoc()): ?>
            <option value="<?= $d['dept_ID'] ?>"><?= $d['dept_name'] ?></option>
        <?php endwhile; ?>
    </select><br>

    <label>Teacher:</label><br>
    <select name="teacher_ID" required>
        <option value="">Select Teacher</option>
        <?php while ($t = $teachers->fetch_assoc()): ?>
            <option value="<?= $t['teacher_ID'] ?>"><?= $t['name'] ?></option>
        <?php endwhile; ?>
    </select><br>

    <label>Blackout Schedule:</label><br>
    <select name="blackout_ID" required>
        <option value="">Select Time</option>
        <?php while ($b = $blackouts->fetch_assoc()): ?>
            <option value="<?= $b['blackout_ID'] ?>">
                <?= $b['date'] . " | " . $b['start_time'] . " - " . $b['end_time'] ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    <label>Date Slot:</label><br>
    <input type="date" name="date_slot" required><br>

    <button type="submit">Add Class</button>
</form>

<br>
<a href="classes_list.php">Back</a>

</body>
</html>

