<?php
require "../includes/auth.php";
require "../config/db.php";

// Only Secretaries submit requests
if ($_SESSION["role"] !== "Secretary") {
    header("Location: ../login.php");
    exit;
}

$classes = $conn->query("SELECT class_ID, class_name, section_no FROM Class ORDER BY class_name");
$departments = $conn->query("SELECT * FROM Department ORDER BY dept_name");
$blackouts = $conn->query("SELECT * FROM Blackout ORDER BY date");

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["request_ID"];
    $class = $_POST["class_ID"];
    $dept = $_POST["dept_ID"];
    $blackout = $_POST["blackout_ID"];
    $date_slot = $_POST["date_slot"];
    $user = $_SESSION["user_id"];

    $sql = "INSERT INTO Request (request_ID, class_ID, dept_ID, blackout_ID, date_slot, submitted_by)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiisi", $id, $class, $dept, $blackout, $date_slot, $user);

    if ($stmt->execute()) {
        header("Location: requests_list.php");
        exit;
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Submit Request</title>
<style>
body { font-family: Arial; padding: 20px; }
input, select { padding: 10px; width: 300px; margin-bottom: 15px; }
button { padding: 10px 20px; background: #2b6cb0; color: white; border-radius: 6px; border: none; }
</style>
</head>
<body>

<h1>Submit New Scheduling Request</h1>

<p style="color:red;"><?= $msg ?></p>

<form method="POST">

    <label>Request ID:</label><br>
    <input type="number" name="request_ID" required><br>

    <label>Select Class:</label><br>
    <select name="class_ID" required>
        <option value="">Choose class</option>
        <?php while ($c = $classes->fetch_assoc()): ?>
            <option value="<?= $c['class_ID'] ?>">
                <?= $c['class_name'] ?> (Section <?= $c['section_no'] ?>)
            </option>
        <?php endwhile; ?>
    </select><br>

    <label>Select Department:</label><br>
    <select name="dept_ID" required>
        <option value="">Choose department</option>
        <?php while ($d = $departments->fetch_assoc()): ?>
            <option value="<?= $d['dept_ID'] ?>">
                <?= $d['dept_name'] ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    <label>Select Time:</label><br>
    <select name="blackout_ID" required>
        <option value="">Choose time</option>
        <?php while ($b = $blackouts->fetch_assoc()): ?>
            <option value="<?= $b['blackout_ID'] ?>">
                <?= $b['date'] . " | " . $b['start_time'] . " - " . $b['end_time'] ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    <label>Date Slot:</label><br>
    <input type="date" name="date_slot" required><br>

    <button type="submit">Submit Request</button>

</form>

<br>
<a href="requests_list.php">Back</a>

</body>
</html>

