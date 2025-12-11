<?php
require "../includes/auth.php";
require "../config/db.php";

//ensure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "Student") {
    header("Location: ../login.php");
    exit;
}

$studentId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['class_id'])) {
    header("Location: ../myclasses.php?error=1&msg=" . urlencode("Invalid request."));
    exit;
}

$classId = (int) $_POST['class_id'];

//telete enrollment
$sql = "DELETE FROM Student_Class WHERE student_ID = ? AND class_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $studentId, $classId);

if ($stmt->execute()) {
    header("Location: ../myclasses.php?msg=" . urlencode("Class dropped successfully."));
    exit;
} else {
    header("Location: ../myclasses.php?error=1&msg=" . urlencode("Error dropping class."));
    exit;
}
?>
