<?php
require "../includes/auth.php";
require "../config/db.php";

if ($_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"];

// Set status to Approved
$sql = "UPDATE Request SET status = 'Approved' WHERE request_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: requests_list.php");
exit;

