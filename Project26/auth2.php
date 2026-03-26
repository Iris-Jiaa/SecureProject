<?php
	include_once 'header.php';
	if (!isset($_SESSION['u_id'])) {
	header("Location: home.php");
	} else {
		$user_id = $_SESSION['u_id'];
		$user_uid = $_SESSION['u_uid'];
	}
?>
        <section class="main-container">
            <div class="main-wrapper">
                <h2>Auth page 2</h2>
				<?php
				$ViewFile = $_GET['FileToView'];
				// Use the basename() function to remove all path information (such as ../ or C:\).
				$safeFile = basename($ViewFile);

				if(file_exists($safeFile)) 
				{
    				$FileData = file_get_contents($safeFile);
    				echo htmlspecialchars($FileData); // 顺便防止 XSS 攻击
				}
				else
				{
    				echo "no file found";
				}
?>
            </div>
        </section>

<?php
	include_once 'footer.php';
?>