<?php
session_start();
include '../Common.php';
$common = new Common();
if ($common->is_admin_logged_in($_SESSION['admin_authToken'] ?? null)){
    header("Location: ../dashboard/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | BSquareSuperMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-container">
    <h1>BSquareSuperMart</h1>
    <h2>Login</h2>
    <form action="loginHandler.php" method="POST">
        <label for="phone">Phone Number</label>
        <input type="tel" name="PHONE" id="phone" placeholder="Enter your phone number" pattern="[0-9]{10}" required>

        <label for="password">Password</label>
        <input type="password" name="PASSWORD" id="password" placeholder="Enter your password" required>

        <input type="submit" value="Sign In" />
    </form>
    <div class="extra-links">
        <a href="#">Forgot Password?</a> |
    </div>
</div>
</body>
</html>
