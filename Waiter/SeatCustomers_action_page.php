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
		if($_SESSION["User"]["UserType"] != 3){
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
			
			// Query available tables
			if(isset($_POST["NumberOfCustomers"]) && $_POST["NumberOfCustomers"] > 0){
				
				$myObj = new stdClass();
				$myObj->seats = array();
				
				$sql = "SELECT * from tables where TableIsDeleted = '0' 
					and BranchID = ".$_SESSION["User"]["BranchID"]."
					and TableIsOccupied = '0'
					and TableSeatingCapacity >= ".$_POST["NumberOfCustomers"]."
				";
				
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						array_push($myObj->seats,$row["TableID"]);
					}
				}
				
				$myObj->isFilterAction = "true";	
				$myJSON = json_encode($myObj);
				
				echo $myJSON;
				
				$conn->close();
				exit();
			}
			
			// Seat Customers
			else if(isset($_POST["TableAddressee"]) && isset($_POST["TableID"])){
				
				// Valid fields
				if(strlen($_POST["TableAddressee"]) != 0 && $_POST["TableID"] > 0){
					
					$sql = "UPDATE tables SET TableIsOccupied = '1', TableAddressee = '".$_POST["TableAddressee"]."' WHERE TableID = ".$_POST["TableID"];
					
					if ($conn->query($sql) === TRUE) {
						$myObj = new stdClass();
						$myObj->isSeatedAction = "true";
						$myObj->alert = 
						'
							<div id = "alert" class="alert alert-success fade in">			
									<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
									<strong>Table Assigned!</strong>
							</div>
						';
						$myObj->isSuccess = "true";
						
						$myJSON = json_encode($myObj);
					
						echo $myJSON;
						
						$conn->close();
						exit();
					}
					else{
						echo "Error: " . $sql . "<br>" . $conn->error;
						exit();
					}
				}
				//Null Handling
				else{
					$myObj = new stdClass();
					$myObj->isSeatedAction = "true";
					$myObj->alert = 
					'
						<div id = "alert" class="alert alert-danger fade in">			
								<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
								<strong>Required Fields Blank!</strong> Please fill out all required data.
						</div>
					';
					$myObj->isSuccess = "false";
					$myJSON = json_encode($myObj);
				
					echo $myJSON;
					
					$conn->close();
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
	function appendResult($ItemID, $ItemName, $ItemPrice, $ItemImageLocation){
		if(!file_exists($ItemImageLocation)){
			$ItemImageLocation = "https://via.placeholder.com/32";
		}
		$str = 
			'
			<tr>
				<td>'.$ItemName.'</td>
				<td><input type="number" name = "ItemPrice'.$ItemID.'" step = "any" class="form-control" id="ItemPrice'.$ItemID.'" placeholder="Php '.$ItemPrice.'"></td>
				<td><img id = "output'.$ItemID.'" src = "'.$ItemImageLocation.'" class="img-circle small-image"></td>
				<td><input class = "form-control" type="file" accept="image/*" name="image'.$ItemID.'" id="file'.$ItemID.'"  onchange="loadFile2(event,'.$ItemID.')"></td>
				<td>
					<div class = "form-group"><button type="submit" name = "edit'.$ItemID.'" class="btn btn-default"><span class="glyphicon glyphicon-edit"></span></button></div>
				</td>
				<td>
					<div class = "form-group"><button type="submit" name = "delete'.$ItemID.'" class="btn btn-default"><span class="glyphicon glyphicon-remove"></span></button></div>
				</td>
			</tr>
			';
		return $str;
	}
?> 