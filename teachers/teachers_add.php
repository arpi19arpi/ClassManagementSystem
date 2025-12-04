<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

// Fetch departments
$departments = $conn->query("SELECT * FROM Department ORDER BY dept_name");

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["teacher_ID"];
    $name = $_POST["name"];
    $password = $_POST["password"];
    $code = $_POST["teacher_code"];
    $dept = $_POST["dept_ID"];

    $sql = "INSERT INTO Teacher (teacher_ID, name, password, teacher_code, dept_ID)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi", $id, $name, $password, $code, $dept);

    if ($stmt->execute()) {
        header("Location: teachers_list.php");
        exit;
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add Teacher</title>
<style>
body { font-family: Arial; padding: 20px; }
input, select { padding: 10px; width: 300px; margin-bottom: 15px; }
button { padding: 10px 20px; background: #2b6cb0; color: white; border: none; border-radius: 6px; }
</style>
</head>
<body>

<h1>Add Teacher</h1>

<p style="color:red;"><?= $msg ?></p>

<form method="POST">
    <label>ID:</label><br>
    <input type="number" name="teacher_ID" required><br>

    <label>Name:</label><br>
    <input type="text" name="name" required><br>

    <label>Password:</label><br>
    <input type="text" name="password" required><br>

    <label>Teacher Code:</label><br>
    <input type="text" name="teacher_code" required><br>

    <label>Department:</label><br>
    <select name="dept_ID" required>
        <option value="">Select Department</option>
        <?php while ($d = $departments->fetch_assoc()): ?>
            <option value="<?= $d['dept_ID'] ?>"><?= $d['dept_name'] ?></option>
        <?php endwhile; ?>
    </select><br>

    <button type="submit">Add Teacher</button>
</form>

<br>
<a href="teachers_list.php">Back</a>

</body>
</html>

