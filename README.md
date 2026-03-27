# SecureProject - Vulnerability Mitigation Project

This project demonstrates the identification and remediation of common web application vulnerabilities in a PHP-based application. It was developed as part of a security module to apply secure coding practices and protect against real-world attacks.

## Project Overview

The original application contained several security vulnerabilities. This project mitigated these vulnerabilities one by one by applying industry-standard security controls.

### Key Improvements:
- Prevented SQL injection using parameterized queries.

- Implemented password hashing (bcrypt) and authentication.

- Added CSRF tokens for sensitive operations.

- Forced session regeneration after login.

- Applied input sanitization and output encoding to prevent XSS attacks.

- Restricted file access and command execution.

- Added brute-force protection for registration.

- Disabled browser caching for authenticated pages.

## Installation & Setup

### Prerequisites
- PHP 7.4 or higher

- MySQL 5.7 or higher (running on port 3307)

- Apache web server (configured to listen on port 81)

### Steps
1. Clone the repository into your web server's document root (e.g., `C:\xampp\htdocs\project26`).

2. Ensure the MySQL server is running on port `3307` with the `TEST` user (password empty). The database will be created automatically.

3. Configure Apache to listen on port `81`.

4.Access the application at: `http://localhost:81/project26/`

5. Click **"Create / Reset Database & Table"** to initialize the database and seed default users:

- Admin: `admin` / `AdminPass1!`

- Regular user: `user1` / `Password1!`
