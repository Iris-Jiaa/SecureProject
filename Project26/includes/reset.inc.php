<?php
session_start();
include 'dbh.inc.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['resetError'] = "Invalid request method.";
    header("Location: ../index.php");
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $_SESSION['resetError'] = "CSRF token validation failed.";
    header("Location: ../index.php");
    exit();
}

if (!isset($_SESSION['u_uid'])) {
    $_SESSION['resetError'] = "You must be logged in.";
    header("Location: ../index.php");
    exit();
}

$oldpass = $_POST['old'] ?? '';
$newpass = $_POST['new'] ?? '';
$newConfirm = $_POST['new_confirm'] ?? '';

if (empty($oldpass) || empty($newpass) || empty($newConfirm)) {
    $_SESSION['resetError'] = "All fields are required.";
    header("Location: ../index.php");
    exit();
}

$uid = $_SESSION['u_uid'];

$stmt = $conn->prepare("SELECT * FROM sapusers WHERE user_uid = ?");
$stmt->bind_param("s", $uid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['resetError'] = "User not found.";
    header("Location: ../index.php");
    exit();
}

$row = $result->fetch_assoc();

// 6. 验证旧密码
if (!password_verify($oldpass, $row['user_pwd'])) {
    $_SESSION['resetError'] = "Incorrect old password.";
    header("Location: ../index.php");
    exit();
}

// 7. 验证新密码一致性
if ($newpass !== $newConfirm) {
    $_SESSION['resetError'] = "New passwords do not match.";
    header("Location: ../index.php");
    exit();
}

// 8. 更新密码（哈希存储）
$newHashed = password_hash($newpass, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE sapusers SET user_pwd = ? WHERE user_uid = ?");
$stmt->bind_param("ss", $newHashed, $uid);
if (!$stmt->execute()) {
    $_SESSION['resetError'] = "Database update failed.";
    header("Location: ../index.php");
    exit();
}

// 9. 重置成功，销毁会话并退出登录（要求重新登录）
// 可选：销毁 CSRF token 以提高安全性
unset($_SESSION['csrf_token']);
session_destroy();  // 或调用 logout.inc.php
header("Location: ./logout.inc.php");
exit();
?>