<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"] ?? null;

try {
    $sql = "DELETE FROM Teacher WHERE teacher_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: teachers_list.php");
    exit;

} catch (mysqli_sql_exception $e) {
    echo "<p style='color:red;'>Cannot delete this teacher because a class references them.</p>";
    echo "<a href='teachers_list.php'>Back</a>";
}
?>

