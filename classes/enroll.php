<?php
require "../includes/auth.php";
require "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "Student") {
    header("Location: ../login.php");
    exit;
}

$studentId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['class_id'])) {
    header("Location: classes_list.php?error=1&msg=" . urlencode("Invalid request."));
    exit;
}

$classId = (int) $_POST['class_id'];

//if class exists and get its time info
$sql = "
    SELECT 
        c.class_ID,
        c.class_name,
        c.section_no,
        c.date_slot,
        b.start_time,
        b.end_time
    FROM Class c
    JOIN Blackout b ON c.blackout_ID = b.blackout_ID
    WHERE c.class_ID = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $classId);
$stmt->execute();
$classResult = $stmt->get_result();

if ($classResult->num_rows === 0) {
    header("Location: classes_list.php?error=1&msg=" . urlencode("Selected class does not exist."));
    exit;
}

$classRow   = $classResult->fetch_assoc();
$dateSlot   = $classRow['date_slot'];
$startTime  = $classRow['start_time'];
$endTime    = $classRow['end_time'];

//if student already enrolled in this class
$sql = "SELECT 1 FROM Student_Class WHERE student_ID = ? AND class_ID = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $studentId, $classId);
$stmt->execute();
$dupResult = $stmt->get_result();

if ($dupResult->num_rows > 0) {
    header("Location: classes_list.php?error=1&msg=" . urlencode("You are already enrolled in this class."));
    exit;
}

//how many classes the student is enrolled in (max 6)
$sql = "SELECT COUNT(*) AS cnt FROM Student_Class WHERE student_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$countResult = $stmt->get_result();
$countRow    = $countResult->fetch_assoc();

if ($countRow['cnt'] >= 6) {
    header("Location: classes_list.php?error=1&msg=" . urlencode("You cannot enroll in more than 6 classes."));
    exit;
}

//time conflict with existing classes on the same date_slot
$sql = "
    SELECT 1
    FROM Student_Class sc
    JOIN Class c2      ON sc.class_ID = c2.class_ID
    JOIN Blackout b2   ON c2.blackout_ID = b2.blackout_ID
    WHERE sc.student_ID = ?
      AND c2.date_slot = ?
      AND (
            (b2.start_time < ? AND b2.end_time > ?)   -- existing spans new start
         OR (b2.start_time < ? AND b2.end_time > ?)   -- existing spans new end
         OR (? <= b2.start_time AND ? >= b2.end_time) -- new spans existing
      )
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "isssssss",
    $studentId,
    $dateSlot,
    $startTime, $startTime,
    $endTime,   $endTime,
    $startTime, $endTime
);
$stmt->execute();
$conflictResult = $stmt->get_result();

if ($conflictResult->num_rows > 0) {
    $msg = "Time conflict: you already have a class during {$dateSlot} "
         . "between " . substr($startTime, 0, 5) . " and " . substr($endTime, 0, 5) . ".";
    header("Location: classes_list.php?error=1&msg=" . urlencode($msg));
    exit;
}

//insert enrollment
$sql = "INSERT INTO Student_Class (student_ID, class_ID) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $studentId, $classId);

if ($stmt->execute()) {
    $msg = "Successfully enrolled in {$classRow['class_name']} (Section {$classRow['section_no']}).";
    header("Location: classes_list.php?msg=" . urlencode($msg));
    exit;
} else {
    header("Location: classes_list.php?error=1&msg=" . urlencode("Failed to enroll. Please try again."));
    exit;
}

