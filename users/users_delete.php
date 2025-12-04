<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"] ?? null;
if (!$id) {
    header("Location: users_list.php");
    exit;
}

$error_message = "";

try {
    $sql = "DELETE FROM User WHERE user_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: users_list.php");
    exit;

} catch (mysqli_sql_exception $e) {
    // Foreign key failure — user is referenced in Student_Class or other tables
    $error_message = "❌ This user cannot be deleted because they are linked to other records 
                      (e.g., student enrollments, assignments, or requests). 
                      Remove their related records first.";
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Delete User</title>
<style>
body { font-family: Arial; padding: 20px; }
.error { color: red; font-size: 18px; margin-bottom: 20px; }
a { color: #2b6cb0; font-weight: bold; }
</style>
</head>
<body>

<h1>User Deletion Failed</h1>

<p class="error"><?= $error_message ?></p>

<a href="users_list.php">← Back to Users</a>

</body>
</html>
