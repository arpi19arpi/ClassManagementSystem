<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

// fetch buildings for dropdown
$buildings = $conn->query("SELECT * FROM Building ORDER BY building_name");

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["room_ID"];
    $room_no = $_POST["room_No"];
    $capacity = $_POST["capacity"];
    $building = $_POST["building_ID"];

    $sql = "INSERT INTO Classroom (room_ID, building_ID, room_No, capacity) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisi", $id, $building, $room_no, $capacity);

    if ($stmt->execute()) {
        header("Location: classrooms_list.php");
        exit;
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add Classroom</title>
<style>
body { font-family: Arial; padding: 20px; }
input, select { padding: 10px; width: 300px; margin-bottom: 15px; }
button { padding: 10px 20px; background: #2b6cb0; color: white; border: none; border-radius: 6px; }
</style>
</head>
<body>

<h1>Add Classroom</h1>

<p style="color:red;"><?= $msg ?></p>

<form method="POST">
    <label>ID:</label><br>
    <input type="number" name="room_ID" required><br>

    <label>Room Number:</label><br>
    <input type="text" name="room_No" required><br>

    <label>Capacity:</label><br>
    <input type="number" name="capacity" required><br>

    <label>Building:</label><br>
    <select name="building_ID" required>
        <option value="">Select a building</option>
        <?php while ($b = $buildings->fetch_assoc()): ?>
            <option value="<?= $b['building_ID'] ?>">
                <?= $b['building_name'] ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    <button type="submit">Add Classroom</button>
</form>

<br>
<a href="classrooms_list.php">Back</a>

</body>
</html>

