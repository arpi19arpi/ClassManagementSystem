<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"];

// Fetch the record
$sql = "SELECT * FROM Building WHERE building_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$building = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["building_name"];
    $location = $_POST["location"];

    $sql = "UPDATE Building SET building_name=?, location=? WHERE building_ID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $location, $id);

    if ($stmt->execute()) {
        header("Location: buildings_list.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Building</title>
<style>
body { font-family: Arial; padding: 20px; }
input { width: 300px; padding: 10px; margin-bottom: 15px; }
button { padding: 10px 20px; background: #2b6cb0; color: white; border: none; }
</style>
</head>
<body>

<h1>Edit Building</h1>

<form method="POST">
    <label>Name:</label><br>
    <input type="text" name="building_name" value="<?= $building['building_name'] ?>" required><br>

    <label>Location:</label><br>
    <input type="text" name="location" value="<?= $building['location'] ?>"><br>

    <button type="submit">Save Changes</button>
</form>

<br>
<a href="buildings_list.php">Back</a>

</body>
</html>

