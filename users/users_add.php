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
    $id = $_POST["user_ID"];
    $name = $_POST["name"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $role = $_POST["role"];

    // role-specific fields
    $dept = ($_POST["dept_ID"] !== "") ? $_POST["dept_ID"] : NULL;
    $semester = ($_POST["semester"] !== "") ? $_POST["semester"] : NULL;
    $enroll = ($_POST["enroll_no"] !== "") ? $_POST["enroll_no"] : NULL;

    $sql = "INSERT INTO User (user_ID, name, username, password, email, role, dept_ID, semester, enroll_no)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssiii",
        $id,        // int
        $name,      // string
        $username,  // string
        $password,  // string
        $email,     // string
        $role,      // string
        $dept,      // int (or null)
        $semester,  // int (or null)
        $enroll     // int (or null)
    );


    if ($stmt->execute()) {
        header("Location: users_list.php");
        exit;
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add User</title>
<style>
body { font-family: Arial; padding: 20px; }
input, select { padding: 10px; width: 300px; margin-bottom: 15px; }
button { padding: 10px 20px; background: #2b6cb0; color: white; border: none; border-radius: 6px; }
</style>

<script>
// Show/hide fields based on role
function updateFields() {
    const role = document.getElementById("role").value;

    document.getElementById("deptField").style.display = (role === "Secretary") ? "block" : "none";
    document.getElementById("studentFields").style.display = (role === "Student") ? "block" : "none";
}
</script>

</head>
<body>

<h1>Add User</h1>

<p style="color:red;"><?= $msg ?></p>

<form method="POST">

    <label>ID:</label><br>
    <input type="number" name="user_ID" required><br>

    <label>Name:</label><br>
    <input type="text" name="name" required><br>

    <label>Username:</label><br>
    <input type="text" name="username" required><br>

    <label>Password:</label><br>
    <input type="text" name="password" required><br>

    <label>Email:</label><br>
    <input type="email" name="email"><br>

    <label>Role:</label><br>
    <select name="role" id="role" onchange="updateFields()" required>
        <option value="">Select role</option>
        <option value="Admin">Admin</option>
        <option value="Secretary">Secretary</option>
        <option value="Student">Student</option>
    </select><br>

    <!-- Secretary: Department -->
    <div id="deptField" style="display:none;">
        <label>Department:</label><br>
        <select name="dept_ID">
            <option value="">Select department</option>
            <?php while ($d = $departments->fetch_assoc()): ?>
                <option value="<?= $d['dept_ID'] ?>"><?= $d['dept_name'] ?></option>
            <?php endwhile; ?>
        </select><br>
    </div>

    <!-- Student: Semester + Enroll No -->
    <div id="studentFields" style="display:none;">
        <label>Semester:</label><br>
        <input type="number" name="semester"><br>

        <label>Enrollment Number:</label><br>
        <input type="number" name="enroll_no"><br>
    </div>

    <button type="submit">Add User</button>
</form>

<br>
<a href="users_list.php">Back</a>

<script>updateFields();</script>

</body>
</html>

