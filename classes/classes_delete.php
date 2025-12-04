<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"];

try {
    $sql = "DELETE FROM Class WHERE class_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: classes_list.php");
    exit;

} catch (mysqli_sql_exception $e) {
    echo "<p style='color:red;'>This class cannot be deleted because it is referenced by other records (assignments or student enrollments).</p>";
    echo "<a href='classes_list.php'>Back</a>";
}
?>

