<?php
require "includes/auth.php";

if ($_SESSION["role"] !== "Student") {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
<style>
body { font-family: Arial; background: #fff9e6; margin: 0; }
.header { background: #d97706; color: white; padding: 20px; }
.container { padding: 30px; }
.card {
    background: white; padding: 20px; border-radius: 12px;
    margin-bottom: 25px; box-shadow: 0 3px 10px rgba(0,0,0,0.15);
}
nav a {
    margin-right: 15px;
    color: #b45309;
    text-decoration: none;
    font-weight: bold;
}
nav a:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="header">
    <h1>Student Dashboard</h1>
    <p>Welcome, <?= $_SESSION["name"]; ?>!</p>
</div>

<!-- Student Navigation -->
<nav style="padding: 15px 30px; background: #fde68a;">
    <a href="classes/classes_list.php">All Classes</a>
    <a href="myclasses.php">My Classes</a>
    <a href="logout.php" style="color:red;">Logout</a>
</nav>

<div class="container">

    <div class="card">
        <h2>My Classes</h2>
        <p>View all your enrolled courses.</p>
        <a href="myclasses.php">Go to My Classes</a>
    </div>

    <div class="card">
        <h2>Browse All Classes</h2>
        <p>See all available class offerings.</p>
        <a href="classes/classes_list.php">Browse Classes</a>
    </div>

</div>


</body>
</html>
