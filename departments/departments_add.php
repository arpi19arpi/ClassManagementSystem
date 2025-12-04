<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

// Fetch buildings for dropdown
$buildings = $conn->query("SELECT * FROM Building ORDER BY building_name");

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["dept_ID"];
    $name = $_POST["dept_name"];
    $building = $_POST["building_ID"];

    $sql = "INSERT INTO Department (dept_ID, dept_name, building_ID) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $id, $name, $building);

    if ($stmt->execute()) {
        header("Location: departments_list.php");
        exit;
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add Department</title>
<style>
body { font-family: Arial; padding: 20px; }
input, select { padding: 10px; width: 300px; margin-bottom: 15px; }
button { padding: 10px 20px; background: #2b6cb0; color: white; border: none; border-radius: 6px; }
</style>
</head>
<body>

<h1>Add Department</h1>

<p style="color:red;"><?= $msg ?></p>

<form method="POST">
    <label>ID:</label><br>
    <input type="number" name="dept_ID" required><br>

    <label>Name:</label><br>
    <input type="text" name="dept_name" required><br>

    <label>Building:</label><br>
    <select name="building_ID" required>
        <option value="">Select a building</option>
        <?php while ($b = $buildings->fetch_assoc()): ?>
            <option value="<?= $b['building_ID'] ?>">
                <?= $b['building_name'] ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    <button type="submit">Add Department</button>
</form>

<br>
<a href="departments_list.php">Back</a>

</body>
</html>

