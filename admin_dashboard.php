<?php
require "includes/auth.php";
require "config/db.php";

// count records for summary
function countTable($conn, $table) {
    $sql = "SELECT COUNT(*) AS total FROM $table";
    $result = $conn->query($sql);
    return $result->fetch_assoc()['total'];
}

$buildings = countTable($conn, "Building");
$departments = countTable($conn, "Department");
$users = countTable($conn, "User");
$classrooms = countTable($conn, "Classroom");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<style>
body { font-family: Arial; background: #eef2ff; margin: 0; }
.header { background: #2b6cb0; color: white; padding: 20px; }
.container { padding: 30px; }
.card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
}
h2 { margin-top: 0; }
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}
.stat-box {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
.stat-box h3 { margin: 0; }
nav a {
    margin-right: 15px;
    color: #2b6cb0;
    text-decoration: none;
    font-weight: bold;
}
.logout-link {
    color: red !important;
}
.logout-link:hover {
    color: darkred !important;
}

</style>
</head>
<body>

<div class="header">
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?= $_SESSION["name"]; ?>!</p>
</div>

<div class="container">

    <nav style="margin-bottom: 30px;">
        <a href="buildings/buildings_list.php">Buildings</a> |
        <a href="departments/departments_list.php">Departments</a> |
        <a href="users/users_list.php">Users</a> |
        <a href="teachers/teachers_list.php">Teachers</a> |
        <a href="classrooms/classrooms_list.php">Classrooms</a> |
        <a href="classes/classes_list.php">Classes</a> |
        <a href="equipment/equipment_list.php">Equipment</a> |
        <a href="requests/requests_list.php">Requests</a> |
        <a href="blackouts/blackouts_list.php">Blackout Times</a> |
        <a href="logout.php" class="logout-link">Logout</a>

    </nav>


    <h2>System Overview</h2>

    <div class="stats-grid">
        <div class="stat-box">
            <h3><?= $buildings ?></h3>
            <p>Buildings</p>
        </div>
        <div class="stat-box">
            <h3><?= $departments ?></h3>
            <p>Departments</p>
        </div>
        <div class="stat-box">
            <h3><?= $users ?></h3>
            <p>Users</p>
        </div>
        <div class="stat-box">
            <h3><?= $classrooms ?></h3>
            <p>Classrooms</p>
        </div>
    </div>

</div>

</body>
</html>

