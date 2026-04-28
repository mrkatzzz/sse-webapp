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
```

# 🔐 Authentication System with 2FA

## 📂 File Structure

| File                  | Description                                                                                             |
| --------------------- | ------------------------------------------------------------------------------------------------------- |
| **db.php**            | Establishes the MySQLi connection logic used across the application.                                    |
| **authenticator.php** | Core library that computes TOTP codes using a shared Base32 secret and the current 30-second time step. |
| **register.php**      | Handles user creation; hashes passwords using `PASSWORD_BCRYPT`.                                        |
| **setup_2fa.php**     | Generates a unique 2FA secret and renders a QR code for Google Authenticator synchronization.           |
| **login.php**         | Phase 1 Security: Validates credentials and manages the Rate Limiter logic.                             |
| **verify_2fa.php**    | Phase 2 Security: Requires the 6-digit TOTP code before granting final session access.                  |
| **welcome.php**       | The restricted dashboard accessible only upon successful completion of both security phases.            |
| **logout.php**        | Destroys the user session and performs a secure cleanup.                                                |

---

## 🛡 Security Mechanisms

### **1. Password Hashing**

Plaintext passwords are never stored. The system utilizes PHP’s `password_hash()` with the **Bcrypt** algorithm.
Verification is performed using `password_verify()`, which is resistant to rainbow table attacks.

---

### **2. Rate Limiting & Account Lockout**

To mitigate **Brute-Force** and **Credential Stuffing** attacks, the system logs failed attempts.

* Upon reaching **3 failed attempts**, the account is flagged.
* A `lockout_until` timestamp is generated, denying all access attempts for a duration of **5 minutes**.

---

### **3. TOTP 2FA (Two-Factor Authentication)**

Implementation of **Time-based One-Time Passwords (TOTP)** ensures that a compromised password alone is insufficient for account access.

* **Shared Secret:** A unique Base32 key is shared between the server and the user's mobile device.
* **Time-Sync:** Codes are generated based on the current Unix time.

---

### **4. Replay Protection**

Each TOTP code is only valid for a **30-second window**.
This prevents **replay attacks**, where an intercepted code is reused by an unauthorized party.

---
