<?php
	session_start();
	date_default_timezone_set('Etc/GMT-8'); // Set time zone to Philippine time
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "rcms_db";

	if(!isset($_SESSION["User"])){
		// 1.1. If he isn't logged in, redirect to the LogIn page
		header("Location: ../LogIn.php");
	}
	else{
		// 1.1. If he is logged in, but not the proper account type, redirect to an access restriction page
		if($_SESSION["User"]["UserType"] != 2){
			header("Location: ../InvalidAccess.html");
		}
		else{
			// Create connection
			$conn = new mysqli($servername, $username, $password, $dbname);
			// Check connection
			if ($conn->connect_error) {
			  die("Connection failed: " . $conn->connect_error);
			}
			
			foreach ($_POST as $a => $b) {
				$a = test_input($b);
			}
			
			// Edit/Delete Table
			foreach ($_POST as $a => $b) {
			
				$pattern = "/edit(\d)+/";
				$pattern2 = "/delete(\d)+/";
				
				// Edit
				if(preg_match($pattern, $a)){
					
					
					$outputString = preg_replace('/[^0-9]/', '', $a);
					$ID = (int)$outputString;
					
					// Null Value Check
					if(strlen($_POST["TableSeatingCapacity".$ID]) == 0){
						
						// Query all tables and leave
						unset($_POST);
						$_POST["Result"] = "";
						
						$sql = "SELECT * from tables where TableIsDeleted = '0' and BranchID = ".$_SESSION["User"]["BranchID"]."
						";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								while($row = $result->fetch_assoc()) {
									$_POST["Result"] .= appendResult($row["TableID"], $row["TableSeatingCapacity"],$row["TableIsOccupied"]);
								}
							}
						
						$_POST["alert"] =
							'
								<div id = "alert" class="alert alert-danger fade in">			
									<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
									<strong>Required Fields Blank!</strong> Please fill out all required data.
								</div>
							';
						$_POST["ManageTables"] = "set";
						
						$conn->close();
						redirectPOSTdata("ManageTables.php");
						exit();
					}
					
					// Edit table
					else{
						$sql = "UPDATE tables SET TableSeatingCapacity='".$_POST["TableSeatingCapacity".$ID]."' WHERE TableID = ".$ID;
					
						if ($conn->query($sql) === TRUE) {
							// Query all tables and leave
							unset($_POST);
							$_POST["Result"] = "";
							
							$sql = "SELECT * from tables where TableIsDeleted = '0' and BranchID = ".$_SESSION["User"]["BranchID"]."
							";
								if($result = $conn->query($sql)){
									if ($result->num_rows > 0) {
										while($row = $result->fetch_assoc()) {
											$_POST["Result"] .= appendResult($row["TableID"], $row["TableSeatingCapacity"],$row["TableIsOccupied"]);
										}
									}
								
									$_POST["alert"] =
										'
											<div id = "alert" class="alert alert-success fade in">			
												<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
												<strong>Table Edited!</strong>
											</div>
										';
									$_POST["ManageTables"] = "set";
									
									$conn->close();
									redirectPOSTdata("ManageTables.php");
									exit();
								}
								else{
									echo "Error: " . $sql . "<br>" . $conn->error;
									exit();
								}
						}
						else{
							echo "Error: " . $sql . "<br>" . $conn->error;
							exit();
						}
					}
				}
				// Delete
				else if(preg_match($pattern2, $a)){
					$outputString = preg_replace('/[^0-9]/', '', $a);
					$ID = (int)$outputString;
					
					$sql = "UPDATE tables SET TableIsDeleted='1' WHERE TableID = ".$ID;
					
					if ($conn->query($sql) === TRUE) {
						// Query all tables and leave
						unset($_POST);
						$_POST["Result"] = "";
						
						$sql = "SELECT * from tables where TableIsDeleted = '0' and BranchID = ".$_SESSION["User"]["BranchID"]."
						";
							if($result = $conn->query($sql)){
								if ($result->num_rows > 0) {
									while($row = $result->fetch_assoc()) {
										$_POST["Result"] .= appendResult($row["TableID"], $row["TableSeatingCapacity"],$row["TableIsOccupied"]);
									}
								}
							
								$_POST["alert"] =
									'
										<div id = "alert" class="alert alert-success fade in">			
											<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
											<strong>Table Deleted!</strong>
										</div>
									';
								$_POST["ManageTables"] = "set";
								
								$conn->close();
								redirectPOSTdata("ManageTables.php");
								exit();
							}
							else{
								echo "Error: " . $sql . "<br>" . $conn->error;
								exit();
							}
					}
					else{
						echo "Error: " . $sql . "<br>" . $conn->error;
						exit();
					}
				}
			}
			
			// Insert Table
			if(isset($_POST["Add_Table"])){
				
				// Null Value Check
				if(strlen($_POST["TableSeatingCapacity"]) == 0){
					
					// Query all tables and leave
					unset($_POST);
					$_POST["Result"] = "";
					
					$sql = "SELECT * from tables where TableIsDeleted = '0' and BranchID = ".$_SESSION["User"]["BranchID"]."
					";
						$result = $conn->query($sql);

						if ($result->num_rows > 0) {
							while($row = $result->fetch_assoc()) {
								$_POST["Result"] .= appendResult($row["TableID"], $row["TableSeatingCapacity"],$row["TableIsOccupied"]);
							}
						}
					
					$_POST["alert"] =
						'
							<div id = "alert" class="alert alert-danger fade in">			
								<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
								<strong>Required Fields Blank!</strong> Please fill out all required data.
							</div>
						';
					$_POST["ManageTables"] = "set";
					
					$conn->close();
					redirectPOSTdata("ManageTables.php");
					exit();
				}
				
				
				else{
					$sql = "INSERT into tables (TableSeatingCapacity,TableIsOccupied,TableIsDeleted,BranchID)
						VALUES ('".$_POST["TableSeatingCapacity"]."','0','0','".$_SESSION["User"]["BranchID"]."')
					";
					
					if ($result = $conn->query($sql) === TRUE) {
						// Query all tables and leave
						unset($_POST);
						$_POST["Result"] = "";
						
						$sql = "SELECT * from tables where TableIsDeleted = '0' and BranchID = ".$_SESSION["User"]["BranchID"]."
						";
							if($result = $conn->query($sql)){
								if ($result->num_rows > 0) {
									while($row = $result->fetch_assoc()) {
										$_POST["Result"] .= appendResult($row["TableID"], $row["TableSeatingCapacity"],$row["TableIsOccupied"]);
									}
								}
							
								$_POST["alert"] =
									'
										<div id = "alert" class="alert alert-success fade in">			
											<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
											<strong>Table Added!</strong>
										</div>
									';
								$_POST["ManageTables"] = "set";
								
								$conn->close();
								redirectPOSTdata("ManageTables.php");
								exit();
							}
							else{
								echo "Error: " . $sql . "<br>" . $conn->error;
								exit();
							}
					}
					else{
						echo "Error: " . $sql . "<br>" . $conn->error;
						exit();
					}
				}
			}
			
			else if(!isset($_POST["ManageTables"])){
				
				// Query all tables and leave
				unset($_POST);
				$_POST["Result"] = "";
				
				$sql = "SELECT * from tables where TableIsDeleted = '0' and BranchID = ".$_SESSION["User"]["BranchID"]."
				";
				if($result = $conn->query($sql)){
					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							$_POST["Result"] .= appendResult($row["TableID"], $row["TableSeatingCapacity"],$row["TableIsOccupied"]);
						}
					}
					$_POST["ManageTables"] = "set";
					
					$conn->close();
					redirectPOSTdata("ManageTables.php");
					exit();
				}
				else{
					echo "Error: " . $sql . "<br>" . $conn->error;
					exit();
				}
				
			}
			
			$conn->close();
		}
	}
	function test_input($data) {
	  $data = trim($data);
	  $data = stripslashes($data);
	  $data = htmlspecialchars($data);
	  return $data;
	}
	function redirectPOSTdata($page){
		echo '<form id="myForm" action="'.$page.'" method="post">';
			foreach ($_POST as $a => $b) {
				echo '<input type="hidden" name="'.htmlentities($a).'" value="'.htmlentities($b).'">';
			}
		echo '<noscript><input type="submit" value="Click here if you are not redirected."/></noscript>
		</form>
		<script type="text/javascript">
			document.getElementById("myForm").submit();
		</script>';
	}
	
	function appendResult($TableID, $TableSeatingCapacity,$TableIsOccupied){
		$status = "";
		$disableDelBtn = "";
		if($TableIsOccupied){
			$status = "Occupied";
			$disableDelBtn = "disabled";
		}
		else{
			$status = "Vacant";
		}
		$str = 
			'
			<tr>
				<td>'.$TableID.'</td>
				<td>
					<div class = "form-group row">
						<div class = "col-sm-4"><input name = "TableSeatingCapacity'.$TableID.'" id = "TableSeatingCapacity'.$TableID.'" '.$disableDelBtn.' class = "form-control" type = "number" min = "1" value = "'.$TableSeatingCapacity.'"></div>
					</div>
				</td>
				<td>'.$status.'</td>
				<td><button type="submit" name = "edit'.$TableID.'" '.$disableDelBtn.' value = "edit'.$TableID.'" class="btn btn-default"><span class = "glyphicon glyphicon-edit"></span></button></td>
				<td><button type="submit" name = "delete'.$TableID.'" '.$disableDelBtn.' value = "delete'.$TableID.'" class="btn btn-default"><span class = "glyphicon glyphicon-remove"></span></button></td>
			</tr>
			';
		return $str;
	}
?> 