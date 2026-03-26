<?php

if (isset($_POST['submit'])) {

    session_start();
    include_once 'dbh.inc.php';

    $uid = $_POST['uid'];
    $pwd = $_POST['pwd'];

    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ipAddr=$_SERVER['HTTP_CLIENT_IP'];
    } elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipAddr=$_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ipAddr=$_SERVER['REMOTE_ADDR'];
    }

    $checkClient = "SELECT `failedLoginCount` FROM `failedLogins` WHERE `ip` = ?";
    $stmt = $conn->prepare($checkClient);
    $stmt->bind_param("s", $ipAddr);
    $stmt->execute();
    $result = $stmt->get_result(); 

    if ($result->num_rows == 0) {
        $insertIP = "INSERT INTO `failedLogins` (`ip`, `timeStamp`, `failedLoginCount`, `lockOutCount`) VALUES (?, NOW(), 1, 0)";
        $stmt = $conn->prepare($insertIP);
        $stmt->bind_param("s", $ipAddr);
        $stmt->execute();
    } else {
        $row = $result->fetch_row();
        $currentCount = $row[0];

        // if the current count is greater than or equal to 3, lock out the IP
        if ($currentCount >= 3) {
            $_SESSION['register'] = "Error: Too many registration attempts. Access denied.";
            header("Location: ../index.php");
            exit();
        }

        $updateCount = "UPDATE `failedLogins` SET `failedLoginCount` = `failedLoginCount` + 1, `timeStamp` = NOW() WHERE `ip` = ?";
        $stmt = $conn->prepare($updateCount);
        $stmt->bind_param("s", $ipAddr);
        $stmt->execute();
    }
    
    if (empty($uid) || empty($pwd)) {
        $_SESSION['register'] = "Cannot submit empty username or password.";
        header("Location: ../index.php");
        exit();
    } else {
        if (!preg_match("/^[a-zA-Z]*$/", $uid)) {
            $_SESSION['register'] = "Username must only contain alphabetic characters.";
            header("Location: ../index.php");
            exit();
        } else {
            $sql = "SELECT * FROM `sapusers` WHERE `user_uid` = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $uid);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $_SESSION['register'] = "Error: Username already exists.";
                header("Location: ../index.php");
                exit();
            } else {
                $hashedPWD = password_hash($pwd, PASSWORD_DEFAULT);

                $sql = "INSERT INTO `sapusers` (`user_uid`, `user_pwd`) VALUES (?, ?)"; 
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $uid, $hashedPWD);
                
                if(!$stmt->execute()) {
                    echo "Error: " . $stmt->error;
                }

                $_SESSION['register'] = "You've successfully registered as " . htmlspecialchars($uid) . ".";
                header("Location: ../index.php");
                exit();
            }
        }   
    }
}