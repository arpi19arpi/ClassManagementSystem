<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST["date"];
    $start = $_POST["start_time"];
    $end = $_POST["end_time"];

    $sql = "INSERT INTO Blackout (date, start_time, end_time) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $date, $start, $end);
    $stmt->execute();

    header("Location: blackouts_list.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head><title>Add Blackout</title></head>
<body>

<h1>Add Blackout Time Slot</h1>

<form method="POST">
    Date: <input type="date" name="date" required><br><br>
    Start Time: <input type="time" name="start_time" required><br><br>
    End Time: <input type="time" name="end_time" required><br><br>

    <button type="submit">Add</button>
</form>

</body>
</html>

