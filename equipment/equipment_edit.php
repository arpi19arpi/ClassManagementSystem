<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"];

// Fetch the equipment
$sql = "SELECT * FROM Equipment WHERE equipment_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$equipment = $stmt->get_result()->fetch_assoc();

// Fetch rooms
$rooms = $conn->query("SELECT r.room_ID, r.room_No, b.building_name 
                       FROM Classroom r
                       JOIN Building b ON r.building_ID = b.building_ID
                       ORDER BY b.building_name, r.room_No");

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $room = $_POST["classroom_ID"];

    $sql = "UPDATE Equipment SET name = ?, classroom_ID = ? WHERE equipment_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $name, $room, $id);

    if ($stmt->execute()) {
        header("Location: equipment_list.php");
        exit;
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Equipment</title>
<style>
body { font-family: Arial; padding: 20px; }
input, select { padding: 10px; width: 300px; margin-bottom: 15px; }
button {
    padding: 10px 20px; background: #2b6cb0; color: white;
    border: none; border-radius: 6px;
}
</style>
</head>
<body>

<h1>Edit Equipment</h1>

<p style="color:red;"><?= $msg ?></p>

<form method="POST">

    <label>Equipment Name:</label><br>
    <input type="text" name="name" value="<?= $equipment['name'] ?>" required><br>

    <label>Assign to Classroom:</label><br>
    <select name="classroom_ID" required>
        <?php while ($r = $rooms->fetch_assoc()): ?>
            <option value="<?= $r['room_ID'] ?>"
                <?= ($equipment['classroom_ID'] == $r['room_ID']) ? "selected" : "" ?>>
                <?= $r['building_name'] ?> - Room <?= $r['room_No'] ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    <button type="submit">Save Changes</button>

</form>

<br>
<a href="equipment_list.php">Back</a>

</body>
</html>

