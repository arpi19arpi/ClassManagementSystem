<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"] ?? null;

try {
    $sql = "DELETE FROM Assignment WHERE assign_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: assignments_list.php");
    exit;

} catch (mysqli_sql_exception $e) {
    echo "<p style='color:red;'>Cannot delete this assignment because it is referenced by other records.</p>";
    echo "<a href='assignments_list.php'>Back</a>";
}
?>

