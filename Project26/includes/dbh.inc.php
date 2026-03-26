<?php

    $dbServername = "localhost:3307";
    $dbUsername = "TEST";
    $dbPassword = "";
    $dbName = "secureappdev";

	try{
    	$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);
	}
	catch (PDOException $e) {
            //echo "Error: " . $e->getMessage();
	}
	
?>