<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: teachers_list.php");
    exit;
}

$sql = "SELECT * FROM Teacher WHERE teacher_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();

if (!$teacher) {
    echo "Teacher not found.";
    exit;
}

$departments = $conn->query("SELECT * FROM Department ORDER BY dept_name");

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $password = $_POST["password"];
    $code = $_POST["teacher_code"];
    $dept = $_POST["dept_ID"];

    $sql = "UPDATE Teacher SET name = ?, password = ?, teacher_code = ?, dept_ID = ?
            WHERE teacher_ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $name, $password, $code, $dept, $id);

    if ($stmt->execute()) {
        header("Location: teachers_list.php");
        exit;
    } else {
        $msg = "Error updating teacher: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Teacher</title>
<style>
body { font-family: Arial; padding: 20px; }
input, select { padding: 10px; width: 300px; margin-bottom: 15px; }
button { padding: 10px 20px; background: #2b6cb0; color: white; border: none; border-radius: 6px; }
</style>
</head>
<body>

<h1>Edit Teacher</h1>

<p style="color:red;"><?= $msg ?></p>

<form method="POST">

    <label>Name:</label><br>
    <input type="text" name="name" value="<?= $teacher['name'] ?>" required><br>

    <label>Password:</label><br>
    <input type="text" name="password" value="<?= $teacher['password'] ?>" required><br>

    <label>Teacher Code:</label><br>
    <input type="text" name="teacher_code" value="<?= $teacher['teacher_code'] ?>" required><br>

    <label>Department:</label><br>
    <select name="dept_ID" required>
        <?php while ($d = $departments->fetch_assoc()): ?>
            <option value="<?= $d['dept_ID'] ?>"
                <?= ($teacher['dept_ID'] == $d['dept_ID']) ? "selected" : "" ?>>
                <?= $d['dept_name'] ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    <button type="submit">Save Changes</button>
</form>

<br>
<a href="teachers_list.php">Back</a>

</body>
</html>

