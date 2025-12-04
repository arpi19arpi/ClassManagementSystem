<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"];

// get classroom
$sql = "SELECT * FROM Classroom WHERE room_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$classroom = $stmt->get_result()->fetch_assoc();

// buildings for dropdown
$buildings = $conn->query("SELECT * FROM Building ORDER BY building_name");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $room_no = $_POST["room_No"];
    $capacity = $_POST["capacity"];
    $building = $_POST["building_ID"];

    $sql = "UPDATE Classroom SET room_No=?, capacity=?, building_ID=? WHERE room_ID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $room_no, $capacity, $building, $id);

    if ($stmt->execute()) {
        header("Location: classrooms_list.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Classroom</title>
<style>
body { font-family: Arial; padding: 20px; }
input, select { padding: 10px; width: 300px; margin-bottom: 15px; }
button { padding: 10px 20px; background: #2b6cb0; color: white; border: none; }
</style>
</head>
<body>

<h1>Edit Classroom</h1>

<form method="POST">
    <label>Room Number:</label><br>
    <input type="text" name="room_No" value="<?= $classroom['room_No'] ?>" required><br>

    <label>Capacity:</label><br>
    <input type="number" name="capacity" value="<?= $classroom['capacity'] ?>" required><br>

    <label>Building:</label><br>
    <select name="building_ID" required>
        <?php while ($b = $buildings->fetch_assoc()): ?>
            <option value="<?= $b['building_ID'] ?>"
                <?= $classroom['building_ID'] == $b['building_ID'] ? "selected" : "" ?>>
                <?= $b['building_name'] ?>
            </option>
        <?php endwhile; ?>
    </select>
    <br>

    <button type="submit">Save Changes</button>
</form>

<br>
<a href="classrooms_list.php">Back</a>

</body>
</html>

