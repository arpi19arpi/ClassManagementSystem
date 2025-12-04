<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"];

// Fetch department
$sql = "SELECT * FROM Department WHERE dept_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$dept = $stmt->get_result()->fetch_assoc();

// Fetch buildings
$buildings = $conn->query("SELECT * FROM Building ORDER BY building_name");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["dept_name"];
    $building = $_POST["building_ID"];

    $sql = "UPDATE Department SET dept_name=?, building_ID=? WHERE dept_ID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $name, $building, $id);

    if ($stmt->execute()) {
        header("Location: departments_list.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Department</title>
<style>
body { font-family: Arial; padding: 20px; }
input, select { padding: 10px; width: 300px; margin-bottom: 15px; }
button { padding: 10px 20px; background: #2b6cb0; color: white; border: none; }
</style>
</head>
<body>

<h1>Edit Department</h1>

<form method="POST">
    <label>Name:</label><br>
    <input type="text" name="dept_name" value="<?= $dept['dept_name'] ?>" required><br>

    <label>Building:</label><br>
    <select name="building_ID">
        <?php while ($b = $buildings->fetch_assoc()): ?>
            <option value="<?= $b['building_ID'] ?>"
                <?= $dept['building_ID'] == $b['building_ID'] ? 'selected' : '' ?>>
                <?= $b['building_name'] ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    <button type="submit">Save Changes</button>
</form>

<br>
<a href="departments_list.php">Back</a>

</body>
</html>

