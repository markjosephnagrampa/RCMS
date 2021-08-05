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
			
			// Page Load
			if(isset($_POST["QueryItems"])){
				$myObj = new stdClass();
				$myObj->items = array();
				$myObj->itemInfo = array();
				$myObj->tables = array();
				
				$sql = "SELECT * from itemavailability join items on itemavailability.ItemID = items.ItemID
					where ItemIsAvailable = '1'
					and BranchID = '".$_SESSION["User"]["BranchID"]."'
					and ItemIsDeleted = '0'
				";
				
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						array_push($myObj->items,array($row["ItemID"],$row["ItemName"]));
						if(!file_exists($row["ItemImageLocation"])){
							$row["ItemImageLocation"] = "https://via.placeholder.com/32";
						}
						array_push($myObj->itemInfo,array($row["ItemID"],$row["ItemName"],$row["ItemImageLocation"],$row["ItemPrice"]));
					}
				}
				
				// Get all occupied tables in this branch
				$sql = "Select * from tables where
					TableIsOccupied = '1' and
					TableIsDeleted = '0' and
					BranchID = '".$_SESSION["User"]["BranchID"]."'
				";
				
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						array_push($myObj->tables,$row["TableID"]);
					}
				}
					
				$myJSON = json_encode($myObj);
				
				echo $myJSON;
				
				$conn->close();
				exit();
			}
			
			
			else if(isset($_POST["Place_Order"])){
				$cart = json_decode($_POST["cart"]);
				
				// Null Table Check
				if(strlen($_POST["Select_Table"]) == 0){
					$myObj = new stdClass();
					$myObj->alert = '
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
				
				// Null Cart Check
				else if(count($cart) == 0){
					$myObj = new stdClass();
					$myObj->alert = '
									<div id = "alert" class="alert alert-danger fade in">			
										<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
										<strong>No items selected!</strong> Please add items to the order.
									</div>
								';
					$myObj->isSuccess = "false";
					$myJSON = json_encode($myObj);
					
					echo $myJSON;
					
					$conn->close();
					
					exit();
				}
				
				// TakeOut check
				else if(strcmp($_POST["Select_Table"],"TakeOut") == 0){
					
					// Null value check
					if(strlen($_POST["OrderAddressee"]) == 0){
						$myObj = new stdClass();
						$myObj->alert = '
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
					
					else{
						$sql = "INSERT into orders (OrderAddressee,WaiterID,OrderTotal,OrderDateCreated,BranchID,OrderIsDeleted,OrderIsDineIn)
							VALUES ('".$_POST["OrderAddressee"]."','".$_SESSION["User"]["UserID"]."','".$_POST["OrderTotal"]."','".date("Y-m-d H:i:s")."','".$_SESSION["User"]["BranchID"]."','0','0')
						";
						
						if ($result = $conn->query($sql) === TRUE) {
							$last_id = $conn->insert_id;
							
							// Create Order Items and TableOrderItems
							
							foreach ($cart as $value) {
								$sql = "INSERT into orderitems (ItemID,OrderItemQty,OrderItemSubtotal,OrderID,OrderItemIsDeleted)
									VALUES ('".$value[0]."','".$value[4]."','".$value[5]."','".$last_id."','0')
								";
								if ($result = $conn->query($sql) === TRUE) {
									
								}
								else{
									echo "Error: " . $sql . "<br>" . $conn->error;
									exit();
								}
								
								$sql = "INSERT into tableOrderItems (ItemID,RemainingQty,OrderID)
									VALUES ('".$value[0]."','".$value[4]."','".$last_id."');
								";
								if ($result = $conn->query($sql) === TRUE) {
									
								}
								else{
									echo "Error: " . $sql . "<br>" . $conn->error;
									exit();
								}
							}
							
							$myObj = new stdClass();
							$myObj->alert = '
											<div id = "alert" class="alert alert-success fade in">			
												<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
												<strong>Order created!</strong>
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
				}
				
				// Dine in check
				else{
					$addressee = "";
					// Get Addressee name via tableID
					$sql = "SELECT * from tables where TableID = ".$_POST["Select_Table"]."
							";
						if($result = $conn->query($sql)){
							while($row = $result->fetch_assoc()) {
								$addressee = $row["TableAddressee"];
							}
						}
					
					
					$sql = "INSERT into orders (OrderAddressee,WaiterID,OrderTotal,OrderDateCreated,BranchID,OrderIsDeleted,OrderIsDineIn)
							VALUES ('".$addressee."','".$_SESSION["User"]["UserID"]."','".$_POST["OrderTotal"]."','".date("Y-m-d H:i:s") ."','".$_SESSION["User"]["BranchID"]."','0','1')
						";
						
						if ($result = $conn->query($sql) === TRUE) {
							$last_id = $conn->insert_id;
							
							// Create Order Items and TableOrderItems
							
							foreach ($cart as $value) {
								$sql = "INSERT into orderitems (ItemID,OrderItemQty,OrderItemSubtotal,OrderID,OrderItemIsDeleted)
									VALUES ('".$value[0]."','".$value[4]."','".$value[5]."','".$last_id."','0')
								";
								if ($result = $conn->query($sql) === TRUE) {
									
								}
								else{
									echo "Error: " . $sql . "<br>" . $conn->error;
									exit();
								}
								
								$sql = "INSERT into tableOrderItems (ItemID,RemainingQty,TableID)
									VALUES ('".$value[0]."','".$value[4]."','".$_POST["Select_Table"]."');
								";
								if ($result = $conn->query($sql) === TRUE) {
									
								}
								else{
									echo "Error: " . $sql . "<br>" . $conn->error;
									exit();
								}
							}
							
							$myObj = new stdClass();
							$myObj->alert = '
											<div id = "alert" class="alert alert-success fade in">			
												<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
												<strong>Order created!</strong>
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