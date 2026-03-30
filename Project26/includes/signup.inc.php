<?php
session_start();

if (isset($_POST['submit'])) {
    include_once 'dbh.inc.php';

    $uid = $_POST['uid'];
    $pwd = $_POST['pwd'];

    // get client IP address
    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ipAddr=$_SERVER['HTTP_CLIENT_IP'];
    } elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipAddr=$_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ipAddr=$_SERVER['REMOTE_ADDR'];
    }

    // Check the lock status and time window
    $checkClient = "SELECT `failedLoginCount`, UNIX_TIMESTAMP(`timeStamp`) as ts FROM `failedLogins` WHERE `ip` = ?";
    $stmt = $conn->prepare($checkClient);
    $stmt->bind_param("s", $ipAddr);
    $stmt->execute();
    $result = $stmt->get_result(); 

    if ($result->num_rows == 0) {
        // If it's a brand new IP address, insert the initial record.
        $insertIP = "INSERT INTO `failedLogins` (`ip`, `timeStamp`, `failedLoginCount`, `lockOutCount`) VALUES (?, NOW(), 0, 0)";
        $stmt = $conn->prepare($insertIP);
        $stmt->bind_param("s", $ipAddr);
        $stmt->execute();
    } else {
        $row = $result->fetch_assoc();
        $failedCount = $row['failedLoginCount'];
        $lastTime = $row['ts'];
        $timeDiff = time() - $lastTime; // Calculate the time difference in seconds

        // If the failed count reaches 3 times
        if ($failedCount >= 3) {
            if ($timeDiff < 30) { // If the time difference is less than 30 seconds
                $secondsLeft = 30 - $timeDiff;
                $_SESSION['register'] = "Too many attempts. Please try again in " . $secondsLeft . " seconds.";
                header("Location: ../index.php");
                exit();
            } else {
                // If it's been more than 30 seconds, reset the failed count for this IP address, allowing a new attempt
                $resetCount = "UPDATE `failedLogins` SET `failedLoginCount` = 0 WHERE `ip` = ?";
                $stmt = $conn->prepare($resetCount);
                $stmt->bind_param("s", $ipAddr);
                $stmt->execute();
            }
        }
    }

    // Record the failed login attempt
    function recordFailure($conn, $ipAddr) {
        $updateCount = "UPDATE `failedLogins` SET `failedLoginCount` = `failedLoginCount` + 1, `timeStamp` = NOW() WHERE `ip` = ?";
        $stmt = $conn->prepare($updateCount);
        $stmt->bind_param("s", $ipAddr);
        $stmt->execute();
    }

    // Input validation and standardized error messages
    // If the username or password is empty or contains invalid characters, return a generic error message
    if (empty($uid) || empty($pwd) || !preg_match("/^[a-zA-Z]*$/", $uid)) {
        recordFailure($conn, $ipAddr);
        $_SESSION['register'] = "Registration failed. Please check your details and try again.";
        header("Location: ../index.php");
        exit();
    }

    $sql = "SELECT * FROM `sapusers` WHERE `user_uid` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        recordFailure($conn, $ipAddr);
        $_SESSION['register'] = "Registration failed. Please check your details and try again.";
        header("Location: ../index.php");
        exit();
    } 

    // Registration and reset failed count after successful registration
    $hashedPWD = password_hash($pwd, PASSWORD_DEFAULT);
    $sql = "INSERT INTO `sapusers` (`user_uid`, `user_pwd`) VALUES (?, ?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $uid, $hashedPWD);
    
    if($stmt->execute()) {
        // Reset the failed count for this IP address after successful registration
        $resetIP = "UPDATE `failedLogins` SET `failedLoginCount` = 0, `timeStamp` = NOW() WHERE `ip` = ?";
        $stmtReset = $conn->prepare($resetIP);
        $stmtReset->bind_param("s", $ipAddr);
        $stmtReset->execute();

        $_SESSION['register'] = "You've successfully registered as " . htmlspecialchars($uid, ENT_QUOTES, 'UTF-8') . ".";
        header("Location: ../index.php");
        exit();
    } else {
        recordFailure($conn, $ipAddr);
        $_SESSION['register'] = "An unexpected error occurred.";
        header("Location: ../index.php");
        exit();
    }
    
} else {
    header("Location: ../register.php");
    exit();
}
?>