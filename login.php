<?php
session_start();
require_once "config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $sql = "SELECT * FROM User WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // For this project: plain-text password match
        if ($password === $row["password"]) {
            $_SESSION["user_id"] = $row["user_ID"];
            $_SESSION["role"] = $row["role"];
            $_SESSION["name"] = $row["name"];

            // Redirect by role
            if ($row["role"] === "Admin") {
                header("Location: admin_dashboard.php");
                exit;
            } else if ($row["role"] === "Secretary") {
                header("Location: secretary_dashboard.php");
                exit;
            } else if ($row["role"] === "Student") {
                header("Location: student_dashboard.php");
                exit;
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>University Login</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f0f4ff;
    display: flex;
    height: 100vh;
    justify-content: center;
    align-items: center;
    margin: 0;
}
.login-box {
    background: white;
    padding: 30px;
    width: 350px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
h2 {
    text-align: center;
    margin-bottom: 25px;
}
input {
    width: 100%;
    padding: 10px;
    margin: 8px 0 18px 0;
    border: 1px solid #ccc;
    border-radius: 6px;
}
button {
    width: 100%;
    padding: 12px;
    background: #2b6cb0;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
}
button:hover {
    background: #1e4f80;
}
.error {
    color: red;
    text-align: center;
    margin-bottom: 15px;
}
</style>
</head>
<body>

<div class="login-box">
    <h2>Login</h2>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>

