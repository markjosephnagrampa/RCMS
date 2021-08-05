<?php
	session_start();
    // 1. Check if the session variable is set
	if(isset($_SESSION["User"])){
		// 1.1. If so, destroy the session and redirect to the log in page
		session_unset();
		session_destroy();
	}
	header("Location: LogIn.php");
?>