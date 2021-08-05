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
			
			// Minus, Remove, Bill Out
			foreach ($_POST as $a => $b) {
			
				$pattern = "/minus(\d)+/";
				$pattern2 = "/remove(\d)+/";
				$pattern3 = "/billOut(\d)+/";
				$sql = "";
				$ID = 0;
				
				if(preg_match($pattern, $a)){
					$outputString = preg_replace('/[^0-9]/', '', $a);
					$ID = (int)$outputString;
					$sql = "UPDATE tableorderitems SET `RemainingQty`=(`RemainingQty`-1) WHERE `TableOrderItemID` = '".$ID."'";
				}
				else if(preg_match($pattern2, $a)){
					$outputString = preg_replace('/[^0-9]/', '', $a);
					$ID = (int)$outputString;
					$sql = "UPDATE tableorderitems SET `RemainingQty` = 0 WHERE `TableOrderItemID` = '".$ID."'";
				}
				else if(preg_match($pattern3, $a)){
					$outputString = preg_replace('/[^0-9]/', '', $a);
					$ID = (int)$outputString;
					$sql = "UPDATE tables SET TableIsOccupied = 0, TableAddressee = NULL WHERE TableID = '".$ID."'";
				}
				
				if(preg_match($pattern, $a) || preg_match($pattern2, $a) || preg_match($pattern3, $a)){
					
					$result = $conn->query($sql);
					
					// Query all Pending Orders and Leave
					unset($_POST);
					$_POST["Result"] = "";
					
					// Bill Out Customers - Dine In
					
					// Get all tables from this branch
					$sql = "Select * from tables where 
						BranchID = ".$_SESSION["User"]["BranchID"]." and 
						TableIsDeleted = '0' and
						TableIsOccupied = '1'
						";
					
					$result = $conn->query($sql);
					
					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							// Get all order items
							$sql2 = "Select * from tableorderitems where 
								TableID = ".$row["TableID"]." and 
								RemainingQty != 0
								";
							
							$result2 = $conn->query($sql2);
							
							if ($result2->num_rows == 0) {
								$_POST["Result"] .= appendHead($row["TableID"], $row["TableAddressee"], false);
								$_POST["Result"] .= appendBillOut($row["TableID"]);
								$_POST["Result"] .= appendFoot();
							}
						}
					}
					
					// Distinct Dine ins
					
					$sql = "SELECT DISTINCT tableorderitems.TableID
						FROM tableorderitems join tables on tables.TableID = tableorderitems.TableID
						WHERE
						tableorderitems.TableID IS NOT NULL and
						TableIsDeleted = '0' and
						RemainingQty != 0 and
						BranchID = '".$_SESSION["User"]["BranchID"]."'
					";
					
					$result = $conn->query($sql);
					
					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							
							// Get Table Addressee
							$sql2 = "SELECT * FROM tables WHERE TableID = ".$row["TableID"]."";
							
							$result2 = $conn->query($sql2);
							if ($result2->num_rows > 0) {
								while($row2 = $result2->fetch_assoc()) {
									// Get Head
									$_POST["Result"] .= appendHead($row2["TableID"], $row2["TableAddressee"], false);
									
									// Get Pending Orders
									$sql3 = "SELECT * FROM tableorderitems join items on tableorderitems.ItemID = items.ItemID
										WHERE TableID = ".$row2["TableID"]."
										and RemainingQty != 0
										";
							
									$result3 = $conn->query($sql3);
									if ($result3->num_rows > 0) {
										while($row3 = $result3->fetch_assoc()) {
											$_POST["Result"] .= appendResult($row3["ItemImageLocation"],$row3["ItemName"],$row3["RemainingQty"],$row3["TableOrderItemID"]);
										}
									}
									
									$_POST["Result"] .= appendFoot();
								}
							}
						}
					}
					
					// All Takeouts
					
					$sql = "SELECT DISTINCT tableorderitems.OrderID as oid
						FROM tableorderitems join orders on tableorderitems.OrderID = orders.OrderID
						WHERE
						tableorderitems.OrderID IS NOT NULL and
						RemainingQty != 0 and
						BranchID = '".$_SESSION["User"]["BranchID"]."'
					";
					
					$result = $conn->query($sql);
					
					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							
							// Get Order Addressee
							$sql2 = "Select * from orders where OrderID = ".$row["oid"]."";
							$result2 = $conn->query($sql2);
					
							if ($result2->num_rows > 0) {
								while($row2 = $result2->fetch_assoc()) {
									// Get Head
									$_POST["Result"] .= appendHead($row2["OrderID"], $row2["OrderAddressee"], true);
									
									// Get Pending Orders
									$sql3 = "SELECT * FROM tableorderitems join items on tableorderitems.ItemID = items.ItemID
										WHERE OrderID = ".$row2["OrderID"]."
										and RemainingQty != 0
										";
							
									$result3 = $conn->query($sql3);
									if ($result3->num_rows > 0) {
										while($row3 = $result3->fetch_assoc()) {
											$_POST["Result"] .= appendResult($row3["ItemImageLocation"],$row3["ItemName"],$row3["RemainingQty"],$row3["TableOrderItemID"]);
										}
									}
									
									$_POST["Result"] .= appendFoot();
								}
							}
							
						}
					}
					
					$_POST["alert"] =
						'
							<div id = "alert" class="alert alert-success fade in">			
								<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
								<strong>Pending Orders Updated!</strong> 
							</div>
						';
					$_POST["TableStatus"] = "set";
					
					$conn->close();
					redirectPOSTdata("TableStatus.php");
					exit();
					
				}
			}
			
			// Page Load
			
			if(!isset($_POST["TableStatus"])){
				
				// Query all Pending Orders and Leave
					
				unset($_POST);
				$_POST["Result"] = "";
				
				// Bill Out Customers - Dine In
				
				// Get all tables from this branch
				$sql = "Select * from tables where 
					BranchID = ".$_SESSION["User"]["BranchID"]." and 
					TableIsDeleted = '0' and
					TableIsOccupied = '1'
					";
				
				$result = $conn->query($sql);
				
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						// Get all order items
						$sql2 = "Select * from tableorderitems where 
							TableID = ".$row["TableID"]." and 
							RemainingQty != 0
							";
						
						$result2 = $conn->query($sql2);
						
						if ($result2->num_rows == 0) {
							$_POST["Result"] .= appendHead($row["TableID"], $row["TableAddressee"], false);
							$_POST["Result"] .= appendBillOut($row["TableID"]);
							$_POST["Result"] .= appendFoot();
						}
					}
				}
				
				// Distinct Dine ins
				
				$sql = "SELECT DISTINCT tableorderitems.TableID
					FROM tableorderitems join tables on tables.TableID = tableorderitems.TableID
					WHERE
					tableorderitems.TableID IS NOT NULL and
					TableIsDeleted = '0' and
					RemainingQty != 0 and
					BranchID = '".$_SESSION["User"]["BranchID"]."'
				";
				
				$result = $conn->query($sql);
				
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						
						// Get Table Addressee
						$sql2 = "SELECT * FROM tables WHERE TableID = ".$row["TableID"]."";
						
						$result2 = $conn->query($sql2);
						if ($result2->num_rows > 0) {
							while($row2 = $result2->fetch_assoc()) {
								// Get Head
								$_POST["Result"] .= appendHead($row2["TableID"], $row2["TableAddressee"], false);
								
								// Get Pending Orders
								$sql3 = "SELECT * FROM tableorderitems join items on tableorderitems.ItemID = items.ItemID
									WHERE TableID = ".$row2["TableID"]."
									and RemainingQty != 0
									";
						
								$result3 = $conn->query($sql3);
								if ($result3->num_rows > 0) {
									while($row3 = $result3->fetch_assoc()) {
										$_POST["Result"] .= appendResult($row3["ItemImageLocation"],$row3["ItemName"],$row3["RemainingQty"],$row3["TableOrderItemID"]);
									}
								}
								
								$_POST["Result"] .= appendFoot();
							}
						}
					}
				}
				
				// All Takeouts
				
				$sql = "SELECT DISTINCT tableorderitems.OrderID as oid
					FROM tableorderitems join orders on tableorderitems.OrderID = orders.OrderID
					WHERE
					tableorderitems.OrderID IS NOT NULL and
					RemainingQty != 0 and
					BranchID = '".$_SESSION["User"]["BranchID"]."'
				";
				
				$result = $conn->query($sql);
				
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						
						// Get Order Addressee
						$sql2 = "Select * from orders where OrderID = ".$row["oid"]."";
						$result2 = $conn->query($sql2);
				
						if ($result2->num_rows > 0) {
							while($row2 = $result2->fetch_assoc()) {
								// Get Head
								$_POST["Result"] .= appendHead($row2["OrderID"], $row2["OrderAddressee"], true);
								
								// Get Pending Orders
								$sql3 = "SELECT * FROM tableorderitems join items on tableorderitems.ItemID = items.ItemID
									WHERE OrderID = ".$row2["OrderID"]."
									and RemainingQty != 0
									";
						
								$result3 = $conn->query($sql3);
								if ($result3->num_rows > 0) {
									while($row3 = $result3->fetch_assoc()) {
										$_POST["Result"] .= appendResult($row3["ItemImageLocation"],$row3["ItemName"],$row3["RemainingQty"],$row3["TableOrderItemID"]);
									}
								}
								
								$_POST["Result"] .= appendFoot();
							}
						}
						
					}
				}
				
				$_POST["TableStatus"] = "set";
				
				$conn->close();
				redirectPOSTdata("TableStatus.php");
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
	
	function appendHead($ID, $TableAddressee, $isTakeout){
		$display = "";
		if($isTakeout){
			$display = "Takeout - ".$TableAddressee;
		}
		else{
			$display = "Table ".$ID." - ".$TableAddressee;
		}
		$str = 
			'
			<div class="panel panel-default">
					<div class="panel-heading"><span style = "font-weight: bold; font-size: 1.25em;">'.$display.'</span></div>
					<div class="panel-body">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Image</th>
									<th>Item Name</th>
									<th>Qty</th>
									<th>Lessen</th>
									<th>Remove</th>
								</tr>
							</thead>
							<tbody>
			';
		return $str;
	}
	
	function appendResult($ItemImageLocation,$ItemName,$RemainingQty,$TableOrderItemID){
		
		if(!file_exists($ItemImageLocation)){
			$ItemImageLocation = "https://via.placeholder.com/32";
		}
		$str = 
			'
							<tr>
								<td><img src = "'.$ItemImageLocation.'" class="img-circle small-image"></td>
								<td>'.$ItemName.'</td>
								<td>'.$RemainingQty.'</td>
								<td><button type = "submit" name = "minus'.$TableOrderItemID.'" value = "minus'.$TableOrderItemID.'" class = "btn btn-default"><span class = "glyphicon glyphicon-minus"></span></button></td>
								<td><button type = "submit" name = "remove'.$TableOrderItemID.'" value = "remove'.$TableOrderItemID.'" class = "btn btn-default"><span class = "glyphicon glyphicon-remove"></span></button></td>
							</tr>
			';
		return $str;
	}
	function appendFoot(){
		$str = 
			'
			</tbody>
						</table>
					</div>
				</div>
			';
		return $str;
	}
	function appendBillOut($TableID){
		$str = 
			'
				<td colspan = "5" style = "text-align: center;"><button type = "submit" name = "billOut'.$TableID.'" value = "billOut'.$TableID.'" class = "btn btn-danger">Bill Out</button></td>
			';
		return $str;
		
	}
	
?> 