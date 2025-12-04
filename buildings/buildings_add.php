<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["building_ID"];
    $name = $_POST["building_name"];
    $location = $_POST["location"];

    $sql = "INSERT INTO Building (building_ID, building_name, location) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $id, $name, $location);

    if ($stmt->execute()) {
        header("Location: buildings_list.php");
        exit;
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add Building</title>
<style>
body { font-family: Arial; background: #fefefe; padding: 20px; }
input { padding: 10px; width: 300px; margin-bottom: 15px; }
button { padding: 10px 20px; background: #2b6cb0; color: white; border: none; border-radius: 6px; }
a { color: #2b6cb0; }
</style>
</head>
<body>

<h1>Add Building</h1>

<p style="color:red;"><?= $msg ?></p>

<form method="POST">
    <label>ID:</label><br>
    <input type="number" name="building_ID" required><br>

    <label>Name:</label><br>
    <input type="text" name="building_name" required><br>

    <label>Location:</label><br>
    <input type="text" name="location"><br>

    <button type="submit">Add Building</button>
</form>

<br>
<a href="buildings_list.php">Back to Buildings</a>

</body>
</html>

