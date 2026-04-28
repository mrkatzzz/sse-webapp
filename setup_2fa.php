<?php
include 'db.php';
include 'authenticator.php';
session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }

$ga = new GoogleAuthenticator();
$user = $_SESSION['user'];

// Generate secret if not exists
$secret = $ga->createSecret();
$stmt = $conn->prepare("UPDATE users SET google_2fa_secret = ? WHERE username = ?");
$stmt->bind_param("ss", $secret, $user);
$stmt->execute();

$qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?data=otpauth://totp/CyberLab:$user%3Fsecret=$secret%26issuer=CyberLab&size=200x200";
?>

<h2>Setup 2FA</h2>
<p>Scan this QR code with Google Authenticator:</p>
<img src="<?php echo $qrCodeUrl; ?>">
<p>Secret: <?php echo $secret; ?></p>
<form action="verify_2fa.php" method="POST">
    Enter OTP: <input type="text" name="otp">
    <button type="submit">Verify & Activate</button>
</form>
