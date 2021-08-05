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
			
			// Edit Item Availability
			foreach ($_POST as $a => $b) {
			
				$pattern = "/edit(\d)+/";
				$pattern2 = "/delete(\d)+/";
				if(preg_match($pattern, $a)){
					$outputString = preg_replace('/[^0-9]/', '', $a);
					$ID = (int)$outputString;
					
					$open = -1;
					
					if(isset($_POST["ItemIsAvailable".$ID]) && $_POST["ItemIsAvailable".$ID] == "Available"){
						$open = 1;
					}
					else if(isset($_POST["ItemIsAvailable".$ID]) && $_POST["ItemIsAvailable".$ID] == "Unavailable"){
						$open = 0;
					}
					
					$sql = "UPDATE itemavailability SET ItemIsAvailable='".$open."' WHERE ItemAvailabilityID = ".$ID;
					
					if ($conn->query($sql) === TRUE) {
						unset($_POST);
						$_POST["Result"] = "";
						
						// Query all itemavailability and leave
						$sql = "SELECT * from itemavailability join items on itemavailability.ItemID = items.ItemID
							where BranchID = ".$_SESSION["User"]["BranchID"]."
							and items.ItemIsDeleted = '0'
						";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								while($row = $result->fetch_assoc()) {
									$_POST["Result"] .= appendResult($row["ItemName"],$row["ItemImageLocation"],$row["ItemPrice"],$row["ItemAvailabilityID"],$row["ItemIsAvailable"]);
								}
							}
						
						$_POST["alert"] =
							'
								<div id = "alert" class="alert alert-success fade in">			
									<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
									<strong>Item Availability Edited!</strong>
								</div>
							';
						$_POST["ManageMenu"] = "set";
						
						$conn->close();
						redirectPOSTdata("ManageMenu.php");
						exit();
					}
					else{
						echo "Error: " . $sql . "<br>" . $conn->error;
						exit();
					}
				}
			}
			
			// Filter by search
			if(isset($_POST["Search"])){
				$_POST["Result"] = "";
						
				// Query all itemavailability and leave
				$sql = "SELECT * from itemavailability join items on itemavailability.ItemID = items.ItemID
					where BranchID = ".$_SESSION["User"]["BranchID"]."
					and items.ItemIsDeleted = '0'
					and lower(items.ItemName) like '%".strtolower($_POST["search_text"])."%'
				";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							$_POST["Result"] .= appendResult($row["ItemName"],$row["ItemImageLocation"],$row["ItemPrice"],$row["ItemAvailabilityID"],$row["ItemIsAvailable"]);
						}
					}
					
				$_POST["ManageMenu"] = "set";
				
				$conn->close();
				redirectPOSTdata("ManageMenu.php");
				exit();
				
			}
			
			else if(!isset($_POST["ManageMenu"])){
				
				// Create ItemAvailability Records for new items
				
				$sql = "SELECT * from itemavailability join items on itemavailability.ItemID = items.ItemID
					where BranchID = ".$_SESSION["User"]["BranchID"]."
					and items.ItemIsDeleted = '0'
				";
				$result = $conn->query($sql);
				$itemAvailabilityCount = $result->num_rows;
					
				$sql = "SELECT * from items
					where ItemIsDeleted = '0'
				";
				if($result = $conn->query($sql)){
					$itemCount = $result->num_rows;

					if($itemAvailabilityCount != $itemCount){
						while($row = $result->fetch_assoc()) {
							
							// Check IA creation need
							$sql2 = "SELECT * from itemavailability join items on itemavailability.ItemID = items.ItemID
								where BranchID = ".$_SESSION["User"]["BranchID"]."
								and itemavailability.ItemID =  ".$row["ItemID"]."
							";
							if($result2 = $conn->query($sql2)){							
								// Don't create
								if($result2->num_rows > 0){
									
								}
								// Create
								else{
									$sql3 = "INSERT INTO itemavailability (ItemID,BranchID,ItemIsAvailable)
									VALUES ('".$row["ItemID"]."','".$_SESSION["User"]["BranchID"]."','1')";
									
									if($result3 = $conn->query($sql3)){
										
									}
									else{
										echo "Error: " . $sql3 . "<br>" . $conn->error;
										exit();
									}
								}
							}
							else{
								echo "Error: " . $sql2 . "<br>" . $conn->error;
								exit();
							}
						}
					}
				}
				else{
					echo "Error: " . $sql . "<br>" . $conn->error;
					exit();
				}
				
				
				unset($_POST);
				$_POST["Result"] = "";
				
				// Query all itemavailability and leave
				$sql = "SELECT * from itemavailability join items on itemavailability.ItemID = items.ItemID
					where BranchID = ".$_SESSION["User"]["BranchID"]."
					and items.ItemIsDeleted = '0'
				";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							$_POST["Result"] .= appendResult($row["ItemName"],$row["ItemImageLocation"],$row["ItemPrice"],$row["ItemAvailabilityID"],$row["ItemIsAvailable"]);
						}
					}
					
				$_POST["ManageMenu"] = "set";
				
				$conn->close();
				redirectPOSTdata("ManageMenu.php");
				exit();
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
	
	function appendResult($ItemName,$ItemImageLocation,$ItemPrice,$ItemAvailabilityID,$ItemIsAvailable){
		$openCheck = "";
		$closedCheck = "";
		if($ItemIsAvailable){
			$openCheck = "checked";
		}
		else{
			$closedCheck = "checked";
		}
		if(!file_exists($ItemImageLocation)){
			$ItemImageLocation = "https://via.placeholder.com/32";
		}
		$str = 
			'
			<tr>
				<td>'.$ItemName.'</td>
				<td><img src = "'.$ItemImageLocation.'" class="img-circle small-image"></td>
				<td>Php '.$ItemPrice.'</td>
				<td>
					<div class="radio">
						<label><input type="radio" name="ItemIsAvailable'.$ItemAvailabilityID.'" value = "Available" '.$openCheck.'>Available</label>
					</div>
					<div class="radio">
						<label><input type="radio" name="ItemIsAvailable'.$ItemAvailabilityID.'" value = "Unavailable" '.$closedCheck.'>Unavailable</label>
					</div>
				</td>
				<td><button type="submit" name = "edit'.$ItemAvailabilityID.'" value = "edit'.$ItemAvailabilityID.'" class="btn btn-default"><span class = "glyphicon glyphicon-edit"></span></button></td>
			</tr>
			';
		return $str;
	}
?> 