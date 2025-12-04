<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

// Fetch dropdown data
$classes = $conn->query("SELECT class_ID, class_name, section_no FROM Class ORDER BY class_name");
$rooms   = $conn->query("SELECT r.room_ID, r.room_No, b.building_name 
                         FROM Classroom r 
                         JOIN Building b ON r.building_ID = b.building_ID
                         ORDER BY b.building_name, r.room_No");

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["assign_ID"];
    $class = $_POST["class_ID"];
    $room = $_POST["room_ID"];
    $assigned_by = $_SESSION["user_id"];

    $sql = "INSERT INTO Assignment (assign_ID, class_ID, room_ID, assigned_by)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $id, $class, $room, $assigned_by);

    if ($stmt->execute()) {
        header("Location: assignments_list.php");
        exit;
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add Assignment</title>
<style>
body { font-family: Arial; padding: 20px; }
input, select { padding: 10px; width: 300px; margin-bottom: 15px; }
button { padding: 10px 20px; background: #2b6cb0; color: white; border: none; border-radius: 6px; }
</style>
</head>
<body>

<h1>Add Assignment</h1>

<p style="color:red;"><?= $msg ?></p>

<form method="POST">

    <label>Assignment ID:</label><br>
    <input type="number" name="assign_ID" required><br>

    <label>Select Class:</label><br>
    <select name="class_ID" required>
        <option value="">Choose class</option>
        <?php while ($c = $classes->fetch_assoc()): ?>
            <option value="<?= $c['class_ID'] ?>">
                <?= $c['class_name'] ?> (Section <?= $c['section_no'] ?>)
            </option>
        <?php endwhile; ?>
    </select><br>

    <label>Select Room:</label><br>
    <select name="room_ID" required>
        <option value="">Choose room</option>
        <?php while ($r = $rooms->fetch_assoc()): ?>
            <option value="<?= $r['room_ID'] ?>">
                <?= $r['building_name'] ?> - Room <?= $r['room_No'] ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    <button type="submit">Add Assignment</button>

</form>

<br>
<a href="assignments_list.php">Back</a>

</body>
</html>

