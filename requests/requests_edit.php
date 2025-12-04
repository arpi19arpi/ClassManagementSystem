<?php
require "../includes/auth.php";
require "../config/db.php";

// Allow Admin and Secretary
if (!in_array($_SESSION["role"], ["Admin", "Secretary"])) {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"];

// Fetch the request
$sql = "SELECT * FROM Request WHERE request_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();

// If request doesn't exist
if (!$request) {
    header("Location: requests_list.php");
    exit;
}

// ⭐ Secretary can only edit their own request
if ($_SESSION["role"] === "Secretary" && $request['submitted_by'] != $_SESSION["user_id"]) {
    header("Location: requests_list.php");
    exit;
}

// Dropdown data
$classes = $conn->query("SELECT class_ID, class_name, section_no FROM Class ORDER BY class_name");
$departments = $conn->query("SELECT dept_ID, dept_name FROM Department ORDER BY dept_name");
$blackouts = $conn->query("SELECT blackout_ID, date, start_time, end_time FROM Blackout ORDER BY date");

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $class = $_POST["class_ID"];
    $dept = $_POST["dept_ID"];
    $blackout = $_POST["blackout_ID"];
    $date_slot = $_POST["date_slot"];

    $sql = "UPDATE Request 
            SET class_ID = ?, dept_ID = ?, blackout_ID = ?, date_slot = ?
            WHERE request_ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiisi", $class, $dept, $blackout, $date_slot, $id);

    if ($stmt->execute()) {
        header("Location: requests_list.php");
        exit;
    } else {
        $msg = "Error updating request: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Request</title>
<style>
body { font-family: Arial; padding: 20px; }
input, select { width: 300px; padding: 10px; margin-bottom: 12px; }
button { padding: 10px 20px; background: #2b6cb0; border: none; color: white; border-radius: 5px; }
</style>
</head>
<body>

<h1>Edit Request</h1>

<p style="color:red;"><?= $msg ?></p>

<form method="POST">

<label>Class:</label><br>
<select name="class_ID" required>
    <?php while ($c = $classes->fetch_assoc()): ?>
        <option value="<?= $c['class_ID'] ?>"
            <?= ($request['class_ID'] == $c['class_ID']) ? "selected" : "" ?>>
            <?= $c['class_name'] ?> (Section <?= $c['section_no'] ?>)
        </option>
    <?php endwhile; ?>
</select><br>

<label>Department:</label><br>
<select name="dept_ID" required>
    <?php while ($d = $departments->fetch_assoc()): ?>
        <option value="<?= $d['dept_ID'] ?>"
            <?= ($request['dept_ID'] == $d['dept_ID']) ? "selected" : "" ?>>
            <?= $d['dept_name'] ?>
        </option>
    <?php endwhile; ?>
</select><br>

<label>Time Slot:</label><br>
<select name="blackout_ID" required>
    <?php while ($b = $blackouts->fetch_assoc()): ?>
        <option value="<?= $b['blackout_ID'] ?>"
            <?= ($request['blackout_ID'] == $b['blackout_ID']) ? "selected" : "" ?>>
            <?= $b['date'] ?> — <?= $b['start_time'] ?> to <?= $b['end_time'] ?>
        </option>
    <?php endwhile; ?>
</select><br>

<label>Date Slot:</label><br>
<input type="date" name="date_slot" value="<?= $request['date_slot'] ?>" required><br>

<button type="submit">Save Changes</button>

</form>

<br>
<a href="requests_list.php">Back</a>

</body>
</html>

