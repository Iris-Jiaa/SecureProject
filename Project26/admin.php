<?php
      include_once 'header.php';
	include_once 'includes/dbh.inc.php';

      if (!isset($_SESSION['u_id']) || $_SESSION['u_admin'] == 0) {
            //Redirecting users who are not logged in or are not administrators back to the homepage.
            header("Location: index.php?login=unauthorized");
            exit(); 
      } else {
            $user_id = $_SESSION['u_id'];
            $user_uid = $_SESSION['u_uid'];
      }
?>

      <section class="main-container">
            <div class="main-wrapper">
                  <h2>Login Events</h2>
                  <div class="admin-entry-count">
                        <?php
                              $entry_total_result = mysqli_query($conn, "SELECT count(event_id) AS num_rows FROM loginevents");
                              $row = mysqli_fetch_object($entry_total_result);
                              $total = $row->num_rows;
                        ?>
                        <p><i>Total entry count: <?php echo $total; ?></i></p>
                  </div>
                  <?php

                        $query = mysqli_query($conn, "SELECT * FROM loginevents");
                        while ($row = mysqli_fetch_array($query)) {
                              // use htmlspecialchars to prevent XSS attacks
                              $ipAddr = htmlspecialchars($row['ip'], ENT_QUOTES, 'UTF-8');
                              $time = htmlspecialchars($row['timeStamp'], ENT_QUOTES, 'UTF-8');
                              $user_id = htmlspecialchars($row['user_id'], ENT_QUOTES, 'UTF-8');
                              $outcome = htmlspecialchars($row['outcome'], ENT_QUOTES, 'UTF-8');

                              echo "<div class='admin-content'>
                                          Entry ID: <b>" . htmlspecialchars($row['event_id']) . "</b>
                                          <br>
                                          <form class='admin-form' method='GET'>
                                                <label>IP Address: </label><input type='text' name='IP' value='$ipAddr' ><br>
                                                <label>Timestamp: </label><input type='text' name='timestamp' value='$time' ><br>
                                                <label>User ID: </label><input type='text' name='timestamp' value='$user_id' ><br>
                                                <label>Outcome: </label><input type='text' name='timestamp' value='$outcome' >
                                          </form>
                                          <br>
                                    </div>";
                        }
                  ?>
            </div>
      </section>
      <?php
            include_once 'footer.php';
      ?>
