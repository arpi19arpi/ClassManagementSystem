<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"];

try {
    $sql = "DELETE FROM Equipment WHERE equipment_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: equipment_list.php");
    exit;

} catch (mysqli_sql_exception $e) {
    echo "<p style='color:red;'>Cannot delete this equipment. It may be referenced elsewhere.</p>";
    echo "<a href='equipment_list.php'>Back</a>";
}
?>

