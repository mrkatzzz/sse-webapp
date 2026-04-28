<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h1>
    <p>You have successfully bypassed Step 1 (Bcrypt) and Step 2 (2FA).</p>
    <a href="logout.php">Logout</a>
</body>
</html>
