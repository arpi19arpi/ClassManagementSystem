<?php
require "../includes/auth.php";
require "../config/db.php";

//only Admin can auto-assign
if ($_SESSION["role"] !== "Admin" && $_SESSION["role"] !== "Secretary") {
    header("Location: ../login.php");
    exit;
}

$classId = $_GET["class_id"];

//get the class blackout time
$sql = "SELECT blackout_ID FROM Class WHERE class_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $classId);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();

$blackout = $class["blackout_ID"];

//get all classrooms that are not unavailable during this blackout
$sql = "
    SELECT room_ID
    FROM Classroom
    WHERE room_ID NOT IN (
        SELECT room_ID
        FROM Classroom_Blackout
        WHERE blackout_ID = $blackout AND blackout = TRUE
    )
";
$rooms = $conn->query($sql);

$availableRooms = [];

while ($room = $rooms->fetch_assoc()) {
    //checking if room is already assigned at that time
    $roomId = $room["room_ID"];

    $sqlCheck = "
        SELECT *
        FROM Assignment a
        JOIN Class c ON a.class_ID = c.class_ID
        WHERE a.room_ID = $roomId
        AND c.blackout_ID = $blackout
    ";
    
    $check = $conn->query($sqlCheck);

    if ($check->num_rows == 0) {
        $availableRooms[] = $roomId;
    }
}

// If no rooms are free -> error
if (empty($availableRooms)) {
    echo "No available classroom for this time slot!!";
    exit;
}

// ppick the first available room
$selectedRoom = $availableRooms[0];

// insert assignment
$sqlAssign = "INSERT INTO Assignment (class_ID, room_ID, assigned_by) VALUES (?, ?, ?)";
$stmtAssign = $conn->prepare($sqlAssign);
$stmtAssign->bind_param("iii", $classId, $selectedRoom, $_SESSION["user_id"]);
$stmtAssign->execute();

//redirect back to class list
header("Location: classes_list.php");
exit;
?>

