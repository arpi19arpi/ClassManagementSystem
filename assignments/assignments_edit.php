<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: assignments_list.php");
    exit;
}

$sql = "SELECT * FROM Assignment WHERE assign_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$assignment = $stmt->get_result()->fetch_assoc();

if (!$assignment) {
    echo "Assignment not found.";
    exit;
}

$classes = $conn->query("SELECT class_ID, class_name, section_no FROM Class ORDER BY class_name");
$rooms = $conn->query("SELECT r.room_ID, r.room_No, b.building_name 
                       FROM Classroom r 
                       JOIN Building b ON r.building_ID = b.building_ID
                       ORDER BY b.building_name, r.room_No");

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $class = $_POST["class_ID"];
    $room = $_POST["room_ID"];

    $sql = "UPDATE Assignment 
            SET class_ID = ?, room_ID = ?
            WHERE assign_ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $class, $room, $id);

    if ($stmt->execute()) {
        header("Location: assignments_list.php");
        exit;
    } else {
        $msg = "Error updating assignment: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Assignment</title>
<style>
body { font-family: Arial; padding: 20px; }
input, select { padding: 10px; width: 300px; margin-bottom: 15px; }
button { padding: 10px 20px; background: #2b6cb0; color: white; border: none; border-radius: 6px; }
</style>
</head>
<body>

<h1>Edit Assignment</h1>

<p style="color:red;"><?= $msg ?></p>

<form method="POST">

    <label>Edit Class:</label><br>
    <select name="class_ID" required>
        <?php while ($c = $classes->fetch_assoc()): ?>
            <option value="<?= $c['class_ID'] ?>"
                <?= ($assignment['class_ID'] == $c['class_ID']) ? 'selected' : '' ?>>
                <?= $c['class_name'] ?> (Section <?= $c['section_no'] ?>)
            </option>
        <?php endwhile; ?>
    </select><br>

    <label>Edit Room:</label><br>
    <select name="room_ID" required>
        <?php while ($r = $rooms->fetch_assoc()): ?>
            <option value="<?= $r['room_ID'] ?>"
                <?= ($assignment['room_ID'] == $r['room_ID']) ? 'selected' : '' ?>>
                <?= $r['building_name'] ?> - Room <?= $r['room_No'] ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    <button type="submit">Save</button>

</form>

<br>
<a href="assignments_list.php">Back</a>

</body>
</html>

