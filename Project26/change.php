<?php
    include_once 'header.php';
?>

<section class="main-container">
    <div class="main-wrapper">
        <h2>Change Password</h2>
        <br>
        Please ensure your new password conforms to the complexity rules:
        <br>
        • Be at least 8 characters long<br>
        • Contain a mix of uppercase and lowercase<br>
        • Contain a digit<br>
        <form class="signup-form" action="includes/reset.inc.php" method="POST">
            <input type="password" name="old" value="" placeholder="Old Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
            <input type="password" name="new" value="" placeholder="New Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
            <input type="password" name="new_confirm" value="" placeholder="Confirm New Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
            <button type="submit" name="reset" value="yes">Reset</button>
            <?php 
            // 生成 CSRF token
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            $token = $_SESSION['csrf_token'];
            ?>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>"/>
        </form>
    </div>
</section>

<?php
    include_once 'footer.php';
?>