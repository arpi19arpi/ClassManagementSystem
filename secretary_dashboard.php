<?php
require "includes/auth.php";

if ($_SESSION["role"] !== "Secretary") {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Secretary Dashboard</title>
<style>
body { font-family: Arial; background: #f7faff; margin: 0; }
.header { background: #4a5568; color: white; padding: 20px; }
.container { padding: 30px; }
.card {
    background: white; padding: 20px; border-radius: 12px;
    margin-bottom: 25px; box-shadow: 0 3px 10px rgba(0,0,0,0.15);
}
nav a {
    margin-right: 15px;
    color: #2b6cb0;
    text-decoration: none;
    font-weight: bold;
}
nav a:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="header">
    <h1>Secretary Dashboard</h1>
    <p>Welcome, <?= $_SESSION["name"]; ?>!</p>
</div>

<!-- ⭐ INSERT THE NAVIGATION BAR HERE -->
<nav style="padding: 15px 30px; background: #edf2f7;">
    <a href="departments/departments_list.php">Departments</a>
    <a href="classrooms/classrooms_list.php">Classrooms</a>
    <a href="requests/requests_list.php">Requests</a>
    <a href="logout.php" style="color:red;">Logout</a>
</nav>

<div class="container">

    <div class="card">
        <h2>Class Assignments</h2>
        <p>View class schedules and room assignments.</p>
        <a href="assignments/assignments_list.php">Go to Assignments</a>
    </div>

    <div class="card">
        <h2>Requests</h2>
        <p>Submit and review classroom scheduling requests.</p>
        <a href="requests/requests_list.php">Manage Requests</a>
    </div>

</div>

</body>
</html>
