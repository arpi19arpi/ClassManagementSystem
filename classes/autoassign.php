<?php
require "../includes/auth.php";
require "../config/db.php";

if (!in_array($_SESSION["role"], ["Admin", "Secretary"])) {
    header("Location: ../login.php");
    exit;
}

$assigned_by = $_SESSION['user_id'];
$log = []; // store messages for popup

// gets all unassigned classes
$sql = "
    SELECT c.class_ID, c.class_name, c.blackout_ID
    FROM Class c
    LEFT JOIN Assignment a ON c.class_ID = a.class_ID
    WHERE a.class_ID IS NULL
";
$classes = $conn->query($sql);

if ($classes->num_rows == 0) {
    header("Location: ../admin_dashboard.php?msg=" . urlencode("All classes already have classroom assignments."));
    exit;
}

$nextAssignID = 1 + ($conn->query("SELECT IFNULL(MAX(assign_ID),0) FROM Assignment")->fetch_row()[0]);

while ($class = $classes->fetch_assoc()) {
    $classID = $class['class_ID'];
    $className = $class['class_name'];
    $blackoutID = $class['blackout_ID'];

    //find available room
    $sqlRoom = "
        SELECT cr.room_ID, cr.room_No
        FROM Classroom cr
        LEFT JOIN Assignment a 
               ON cr.room_ID = a.room_ID
              AND a.class_ID IN (
                   SELECT class_ID 
                   FROM Class 
                   WHERE blackout_ID = $blackoutID
              )
        LEFT JOIN Classroom_Blackout cb 
               ON cb.room_ID = cr.room_ID 
              AND cb.blackout_ID = $blackoutID
        WHERE a.room_ID IS NULL  
          AND (cb.blackout IS NULL OR cb.blackout = FALSE)
        ORDER BY cr.capacity DESC
        LIMIT 1
    ";

    $roomResult = $conn->query($sqlRoom);

    if ($roomResult->num_rows == 0) {
        $log[] = "$className -> No available classroom";
        continue;
    }

    $room = $roomResult->fetch_assoc();
    $roomID = $room['room_ID'];
    $roomNo = $room['room_No'];

    // insert assignment
    $stmt = $conn->prepare("
        INSERT INTO Assignment(assign_ID, class_ID, room_ID, assigned_by)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("iiii", $nextAssignID, $classID, $roomID, $assigned_by);
    $stmt->execute();

    $log[] = "$className → $roomNo";

    $nextAssignID++;
}

//send log to dashboard
header("Location: ../admin_dashboard.php?assign_log=" . urlencode(implode("|", $log)));
exit;
?>
