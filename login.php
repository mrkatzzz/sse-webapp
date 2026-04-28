<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT password_hash, login_attempts, lockout_until FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row) {
        if ($row['lockout_until'] && strtotime($row['lockout_until']) > time()) {
            die("ACCOUNT_LOCKED");
        }

        if (password_verify($pass, $row['password_hash'])) {
            $conn->query("UPDATE users SET login_attempts = 0, lockout_until = NULL WHERE username = '$user'");
            
            // STAGE 1 SUCCESS: Move to 2FA
            $_SESSION['temp_user'] = $user; 
            header("Location: verify_2fa.php");
            exit();
        } else {
            $attempts = $row['login_attempts'] + 1;
            $lockout = ($attempts >= 3) ? date('Y-m-d H:i:s', strtotime('+5 minutes')) : NULL;
            $stmt = $conn->prepare("UPDATE users SET login_attempts = ?, lockout_until = ? WHERE username = ?");
            $stmt->bind_param("iss", $attempts, $lockout, $user);
            $stmt->execute();
            echo "Invalid password. Attempt: $attempts";
        }
    } else { echo "User not found."; }
}
?>
<h2>Login (Step 1)</h2>
<form method="POST">
    Username: <input type="text" name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <input type="submit" value="Next">
</form>
