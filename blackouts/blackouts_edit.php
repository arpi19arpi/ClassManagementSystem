<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"];

$sql = "SELECT * FROM Blackout WHERE blackout_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$blackout = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST["date"];
    $start = $_POST["start_time"];
    $end = $_POST["end_time"];

    $sqlUpdate = "UPDATE Blackout SET date=?, start_time=?, end_time=? WHERE blackout_ID=?";
    $stmt2 = $conn->prepare($sqlUpdate);
    $stmt2->bind_param("sssi", $date, $start, $end, $id);
    $stmt2->execute();

    header("Location: blackouts_list.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Blackout</title></head>
<body>

<h1>Edit Blackout Time Slot</h1>

<form method="POST">
    Date: <input type="date" name="date" value="<?= $blackout['date']; ?>" required><br><br>
    Start Time: <input type="time" name="start_time" value="<?= substr($blackout['start_time'],0,5); ?>" required><br><br>
    End Time: <input type="time" name="end_time" value="<?= substr($blackout['end_time'],0,5); ?>" required><br><br>

    <button type="submit">Save Changes</button>
</form>

</body>
</html>

