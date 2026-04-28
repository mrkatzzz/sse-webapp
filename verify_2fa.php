<?php
include 'db.php';
include 'authenticator.php';
session_start();

if (!isset($_SESSION['temp_user'])) { header("Location: login.php"); exit(); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_SESSION['temp_user'];
    $otp = $_POST['otp'];

    $stmt = $conn->prepare("SELECT google_2fa_secret FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    $ga = new GoogleAuthenticator();
    if ($ga->verifyCode($res['google_2fa_secret'], $otp)) {
        $_SESSION['user'] = $user; // FULL LOGIN
        unset($_SESSION['temp_user']);
        header("Location: welcome.php");
    } else {
        echo "Invalid OTP code!";
    }
}
?>
<h2>Two-Factor Authentication</h2>
<p>Enter the 6-digit code from your app:</p>
<form method="POST">
    OTP: <input type="text" name="otp" required autofocus>
    <input type="submit" value="Verify">
</form>
