# Secure Authentication System (Bcrypt + TOTP 2FA)

This project demonstrates a secure multi-stage authentication flow including password hashing, automated account lockout (rate limiting), and Two-Factor Authentication (TOTP).

## 🛠 Database Setup

Run the following SQL commands in **phpMyAdmin** or your MySQL terminal to set up the environment:

```sql
-- 1. Create Database
CREATE DATABASE lab_assignment;
USE lab_assignment;

-- 2. Create Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    login_attempts INT DEFAULT 0,
    lockout_until DATETIME DEFAULT NULL
);

-- 3. Alter Table for 2FA Support
ALTER TABLE users ADD COLUMN google_2fa_secret VARCHAR(32) DEFAULT NULL;

File,Description
db.php,Handles the MySQLi connection logic for the entire application.
authenticator.php,A core library that calculates TOTP codes based on a shared secret and current timestamp.
register.php,Hashes passwords using PASSWORD_BCRYPT and initializes the user record.
setup_2fa.php,Generates a unique Base32 secret and displays a QR code via Google Charts API.
login.php,Phase 1 Security: Verifies password and implements the Rate Limiter. It locks the account if login_attempts >= 3.
verify_2fa.php,Phase 2 Security: Validates the 6-digit OTP code against the stored secret before granting session access.
welcome.php,The protected landing page (Dashboard) which requires a valid $_SESSION['user'].
logout.php,Securely destroys the session and clears all user data.

🛡 Security Mechanisms
Password Hashing: Plaintext is never stored. Verification is handled via password_verify().

Rate Limiting: Failed attempts are logged. Upon 3 failures, the lockout_until timestamp prevents further attempts for 5 minutes, mitigating brute-force and credential stuffing.

TOTP 2FA: Implements Time-based One-Time Passwords. Even with a compromised password, an attacker cannot gain access without the physical 2FA device.

Replay Protection: OTP codes are valid only for a 30-second window.
