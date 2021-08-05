<?php
	session_start();
	date_default_timezone_set('Etc/GMT-8'); // Set time zone to Philippine time
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "rcms_db";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	  die("Connection failed: " . $conn->connect_error);
	}
	
	foreach ($_POST as $a => $b) {
		$a = test_input($b);
	}

	$sql = "SELECT * from users where UserEmail = '".$_POST["UserEmail"]."' and UserPassword = '".$_POST["UserPassword"]."'";
	$result = $conn->query($sql);

	if ($result->num_rows == 1) {
	  // output data of each row
	  while($row = $result->fetch_assoc()) {
		  
		// Deactivated account check
		if($row["UserIsActive"] == false){
			echo '<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
			<strong>Account Deactivated!</strong> Please contact administration to reactivate your account.';
		}
		
		else{
			
			// Join Branch Details on Non Owner Accounts
			
			if($row["UserType"] != 1){
			
				$sql = "SELECT * from users join branches on users.BranchID = branches.BranchID where UserEmail = '".$_POST["UserEmail"]."' and UserPassword = '".$_POST["UserPassword"]."'";
				$result = $conn->query($sql);
				
				if ($result->num_rows == 1) {
				  // output data of each row
				  while($row = $result->fetch_assoc()) {
					  // Store user info in session
						$_SESSION["User"] = $row;
						
						// Redirect Based on Account Type
						echo $_SESSION["User"]["UserType"];
				  }
				}
			}
			else{
				// Store user info in session
				$_SESSION["User"] = $row;
				
				// Redirect Based on Account Type
				echo $_SESSION["User"]["UserType"];
			}
		}
	  }
	} 
	else {
	  echo '<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
			<strong>Invalid Credentials!</strong> Please make sure that your login details are correct.';
	}
	$conn->close();

	function test_input($data) {
	  $data = trim($data);
	  $data = stripslashes($data);
	  $data = htmlspecialchars($data);
	  return $data;
	}
?> 