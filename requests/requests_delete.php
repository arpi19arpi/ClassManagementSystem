<?php
require "../includes/auth.php";
require "../config/db.php";

// Allow Admin and Secretary
if (!in_array($_SESSION["role"], ["Admin", "Secretary"])) {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"];

// Fetch the request to check ownership
$sql_check = "SELECT submitted_by FROM Request WHERE request_ID = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $id);
$stmt_check->execute();
$request = $stmt_check->get_result()->fetch_assoc();

// If the request doesn't exist
if (!$request) {
    header("Location: requests_list.php");
    exit;
}

// Secretary can ONLY delete their own requests
if ($_SESSION["role"] === "Secretary" && $request['submitted_by'] != $_SESSION["user_id"]) {
    header("Location: requests_list.php");
    exit;
}

try {
    $sql = "DELETE FROM Request WHERE request_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: requests_list.php");
    exit;

} catch (mysqli_sql_exception $e) {
    echo "<p style='color:red;'>Error deleting request. It may be linked to other data.</p>";
    echo "<a href='requests_list.php'>Back</a>";
}
?>

