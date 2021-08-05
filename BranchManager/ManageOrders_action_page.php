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
			
				$pattern = "/view(\d)+/";
				$pattern2 = "/deleteOrder(\d)+/";
				$pattern3 = "/deleteOrderItem(\d)+/";
				$pattern4 = "/printSummary(\d)+/";
				
				// View
				if(preg_match($pattern, $a)){
					
					$from3 = ""; $to3 = ""; $sqlRange = "";
					
					if(strlen($_POST["from"]) > 0){
						$from_dateStr=date_create($_POST["from"]);
						$from2 = date_format($from_dateStr,"Y-m-d H:i:s");
						$exampleDate = strtotime($from2);
						$from3 = date('Y-m-d\TH:i', $exampleDate);
					}
					if(strlen($_POST["to"]) > 0){
						$to_dateStr=date_create($_POST["to"]);
						$to2 = date_format($to_dateStr,"Y-m-d H:i:s");
						$exampleDate = strtotime($to2);
						$to3 = date('Y-m-d\TH:i', $exampleDate);
					}
					
					if(strlen($_POST["from"]) > 0 && strlen($_POST["to"]) > 0){
						$sqlRange = "SELECT * from orders
							where OrderIsDeleted = '0' and
							OrderDateCreated >= '".$from2."' and
							OrderDateCreated <= '".$to2."' and
							BranchID = '".$_SESSION["User"]["BranchID"]."'
						";
					}
					
					$outputString = preg_replace('/[^0-9]/', '', $a);
					$ID = (int)$outputString;
					
					// Query all Order Items
					
					$orderitems = "";
					$sql = "Select * from orderitems join items on orderitems.ItemID = items.ItemID where OrderID = ".$ID." and OrderItemIsDeleted = '0'";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							$orderitems .= appendOrderItem($row["ItemName"],$row["ItemImageLocation"],$row["OrderItemQty"],$row["OrderItemSubtotal"],$row["OrderItemID"]);
						}	
					}
					
					// Query Order Total
					$sql = "Select * from orders where OrderID = ".$ID."";
					$result = $conn->query($sql);
					$orderTotal = 0.0;

					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							$orderTotal = $row["OrderTotal"];
						}
					}
					
					
					queryAndRedirect(true,"",$sqlRange,$from3,$to3,$orderitems,$orderTotal,$ID);
				}	
					
				// Delete Order
				else if(preg_match($pattern2, $a)){
					
					$outputString = preg_replace('/[^0-9]/', '', $a);
					$ID = (int)$outputString;
					
					$from3 = ""; $to3 = ""; $sqlRange = "";
					
					if(strlen($_POST["from"]) > 0){
						$from_dateStr=date_create($_POST["from"]);
						$from2 = date_format($from_dateStr,"Y-m-d H:i:s");
						$exampleDate = strtotime($from2);
						$from3 = date('Y-m-d\TH:i', $exampleDate);
					}
					if(strlen($_POST["to"]) > 0){
						$to_dateStr=date_create($_POST["to"]);
						$to2 = date_format($to_dateStr,"Y-m-d H:i:s");
						$exampleDate = strtotime($to2);
						$to3 = date('Y-m-d\TH:i', $exampleDate);
					}
					
					if(strlen($_POST["from"]) > 0 && strlen($_POST["to"]) > 0){
						$sqlRange = "SELECT * from orders
							where OrderIsDeleted = '0' and
							OrderDateCreated >= '".$from2."' and
							OrderDateCreated <= '".$to2."' and
							BranchID = '".$_SESSION["User"]["BranchID"]."'
						";
					}
					
					
					$sql = "Update orders set OrderIsDeleted = '1' where OrderID = ".$ID."";
					if ($conn->query($sql) === TRUE) {
						queryAndRedirect(false,"<strong>Order deleted!</strong>",$sqlRange,$from3,$to3,"","","");
					} else {
					  echo "Error: " . $sql . "<br>" . $conn->error;
					  exit();
					}
				}
				
				// Delete Order Item
				else if(preg_match($pattern3, $a)){
					$outputString = preg_replace('/[^0-9]/', '', $a);
					$ID = (int)$outputString;
					
					$from3 = ""; $to3 = ""; $sqlRange = ""; $orderitems = "";
					
					if(strlen($_POST["from"]) > 0){
						$from_dateStr=date_create($_POST["from"]);
						$from2 = date_format($from_dateStr,"Y-m-d H:i:s");
						$exampleDate = strtotime($from2);
						$from3 = date('Y-m-d\TH:i', $exampleDate);
					}
					if(strlen($_POST["to"]) > 0){
						$to_dateStr=date_create($_POST["to"]);
						$to2 = date_format($to_dateStr,"Y-m-d H:i:s");
						$exampleDate = strtotime($to2);
						$to3 = date('Y-m-d\TH:i', $exampleDate);
					}
					
					if(strlen($_POST["from"]) > 0 && strlen($_POST["to"]) > 0){
						$sqlRange = "SELECT * from orders
							where OrderIsDeleted = '0' and
							OrderDateCreated >= '".$from2."' and
							OrderDateCreated <= '".$to2."' and
							BranchID = '".$_SESSION["User"]["BranchID"]."'
						";
					}
					
					$sql = "Select * from orderitems where OrderItemID = ".$ID."";
					$orderSubtotal = 0.0;
					$orderID = 0;
					$result = $conn->query($sql);
					
					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							$orderSubtotal = $row["OrderItemSubtotal"];
							$orderID = $row["OrderID"];
						}
					}
					
					$sql = "Update orders set OrderTotal = (OrderTotal - ".$orderSubtotal.") where OrderID = ".$orderID."";
					
					if ($conn->query($sql) === TRUE) {
					} else {
					  echo "Error: " . $sql . "<br>" . $conn->error;
					  exit();
					}
					
					$sql = "Select * from orders where OrderID = ".$orderID."";
					$ordertotal = 0.0;
					$result = $conn->query($sql);
					
					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							$ordertotal = $row["OrderTotal"];
						}
					}
					
					$sql = "Update orderitems set OrderItemIsDeleted = '1' where OrderItemID = ".$ID."";
					if ($conn->query($sql) === TRUE) {
						
					} else {
					  echo "Error: " . $sql . "<br>" . $conn->error;
					  exit();
					}
					
					$sql = "Select * from orderitems join items on orderitems.ItemID = items.ItemID where OrderID = ".$orderID." and OrderItemIsDeleted = '0'";
					$result = $conn->query($sql);
					
					$psm = "";
					
					if ($result->num_rows == 0) {
						$sql = "Update orders set OrderIsDeleted = '1' where OrderID = ".$orderID."";
						if ($conn->query($sql) === TRUE) {
							
						} else {
						  echo "Error: " . $sql . "<br>" . $conn->error;
						  exit();
						}
					}
					else{
						while($row = $result->fetch_assoc()) {
							$psm = $row["OrderID"];
							$orderitems .= appendOrderItem($row["ItemName"],$row["ItemImageLocation"],$row["OrderItemQty"],$row["OrderItemSubtotal"],$row["OrderItemID"]);
						}
					}
					
					queryAndRedirect(false,"<strong>Order Item Deleted!</strong>",$sqlRange,$from3,$to3,$orderitems,$ordertotal,$psm);
				}
				
				// PDF Generation
				else if(preg_match($pattern4, $a)){
					$outputString = preg_replace('/[^0-9]/', '', $a);
					$ID = (int)$outputString;
					
					queryForPDF($ID);
				}
			}
			
			if(isset($_POST["search"])){
				// Required Fields Check
				
				if(!isset($_POST["from"]) || !isset($_POST["to"]) || strlen($_POST["from"]) == 0 || strlen($_POST["to"]) == 0){
					queryAndRedirect(true,"<strong>Required Fields Blank!</strong> Please fill out all required data.","","","","","","");
				}
				
				// Valid Date Range Check
				else{
					$from_dateStr=date_create($_POST["from"]);
					$to_dateStr=date_create($_POST["to"]);
					
					$from2 = date_format($from_dateStr,"Y-m-d H:i:s");
					$to2 = date_format($to_dateStr,"Y-m-d H:i:s");
					
					$exampleDate = strtotime($from2);
					$from3 = date('Y-m-d\TH:i', $exampleDate);
					
					$exampleDate = strtotime($to2);
					$to3 = date('Y-m-d\TH:i', $exampleDate);
					
					
					if($from_dateStr > $to_dateStr){
						queryAndRedirect(true,"<strong>Invalid Date Range!</strong> Please select a valid duration.","","","","","","");
					}
					// Query within range 
					else{
						$sql = "SELECT * from orders
							where OrderIsDeleted = '0' and
							OrderDateCreated >= '".$from2."' and
							OrderDateCreated <= '".$to2."' and
							BranchID = '".$_SESSION["User"]["BranchID"]."'
						";
						
						queryAndRedirect(true,"",$sql,$from3,$to3,"","","");
					}
				}
			}
			
			// Page Load
			else if(!isset($_POST["ManageOrders"])){
				
				queryAndRedirect(false,"","","","","","","");
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
	
	function appendResult($OrderDateCreated,$OrderAddressee,$itemCount,$OrderTotal,$OrderID){
		$dateStr=date_create($OrderDateCreated);
		$date = date_format($dateStr, "M d, Y");
		$time = date_format($dateStr, "h:i a");
		
		$str = 
			'
			<tr>
				<td>'.$date.'</td>
				<td>'.$time.'</td>
				<td>'.$OrderAddressee.'</td>
				<td>'.$itemCount.'</td>
				<td>Php '.$OrderTotal.'</td>
				<td><button type="submit" name = "view'.$OrderID.'" value = "view'.$OrderID.'" class="btn btn-default"><span class = "glyphicon glyphicon-eye-open"></span></button></td>
				<td><button type="submit" name = "deleteOrder'.$OrderID.'" value = "deleteOrder'.$OrderID.'" class="btn btn-default"><span class = "glyphicon glyphicon-remove"></span></button></td>
			</tr>
			';
		return $str;
	}
	
	function appendOrderItem($ItemName,$ItemImageLocation,$OrderItemQty,$OrderItemSubtotal,$OrderItemID){
		if(!file_exists($ItemImageLocation)){
			$ItemImageLocation = "https://via.placeholder.com/32";
		}
		$str = 
			'
				<tr>
					<td>'.$ItemName.'</td>
					<td><img src = "'.$ItemImageLocation.'" class="img-circle small-image"></td>
					<td>'.$OrderItemQty.'</td>
					<td>Php '.$OrderItemSubtotal.'</td>
					<td><button type = "submit" name = "deleteOrderItem'.$OrderItemID.'" value = "deleteOrderItem'.$OrderItemID.'" class = "btn btn-default"><span class = "glyphicon glyphicon-remove"></span></button></td>
				</tr>
			';
		return $str;
	}
	
	function queryForPDF($OrderID){
		
		global $conn;
		
		// Query Order
		unset($_POST);
		$_POST["Result"] = array();
		$_POST["Summary"] = array();
		
		$sql = "SELECT * from orders
			where OrderID = ".$OrderID."
		";
		
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				
				$itemCount = 0;
				
				// Get no. of items
				$sql2 = "SELECT * from orderitems join items on orderitems.ItemID = items.ItemID
					where OrderItemIsDeleted = '0' and
					OrderID = ".$row["OrderID"]."
				";
				
				$result2 = $conn->query($sql2);

				if ($result2->num_rows > 0) {
					while($row2 = $result2->fetch_assoc()) {
						$itemCount += $row2["OrderItemQty"];
						$_POST["Result"][] = array($row2["ItemName"],$row2["OrderItemQty"],"Php ".number_format($row2["OrderItemSubtotal"], 2, '.', ','));
					}
				}
				
				$dateStr=date_create($row["OrderDateCreated"]);
				$date = date_format($dateStr, "M d, Y");
				$time = date_format($dateStr, "h:i a");
				
				$_POST["Summary"][] = array($row["OrderID"],$date,$time,$row["OrderAddressee"],$itemCount,"Php ".number_format($row["OrderTotal"], 2, '.', ','));
				$_POST["BranchAddress"] = $_SESSION["User"]["BranchAddress"];
			}
		}
		
		$_POST["Summary"] = json_encode($_POST["Summary"]);
		$_POST["Result"] = json_encode($_POST["Result"]);
		
		$conn->close();
		redirectPOSTdata("invoice.php");
		exit();
	}
	
	function queryAndRedirect($alertIsDanger,$alertText,$altSql,$from,$to,$orderitems,$ordertotal,$orderID){
		
		global $conn;
		
		// Query all Orders and Leave
		unset($_POST);
		$_POST["Result"] = "";
		
		$sql = "SELECT * from orders
			where OrderIsDeleted = '0' and
			BranchID = '".$_SESSION["User"]["BranchID"]."'
		";
		
		if(strlen($altSql) > 0){
			$sql = $altSql;
		}
		
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				
				$itemCount = 0;
				
				// Get no. of items
				$sql2 = "SELECT * from orderitems
					where OrderItemIsDeleted = '0' and
					OrderID = ".$row["OrderID"]."
				";
				
				$result2 = $conn->query($sql2);

				if ($result2->num_rows > 0) {
					while($row2 = $result2->fetch_assoc()) {
						$itemCount += $row2["OrderItemQty"];
					}
				}
				
				// Append Result
				$_POST["Result"] .= appendResult($row["OrderDateCreated"],$row["OrderAddressee"],$itemCount,$row["OrderTotal"],$row["OrderID"]);
				
			}
		}
		$at = "";
		if($alertIsDanger){
			$at = "alert-danger";
		}
		else{
			$at = "alert-success";
		}
		
		if(strlen($alertText) == 0){
			$_POST["alert"] = "";
		}
		else{
		
			$_POST["alert"] =
			'
				<div id = "alert" class="alert '.$at.' fade in">			
					<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
					'.$alertText.'
				</div>
			';
		}
		
		if(strlen($from) != 0){
			$_POST["from"] = $from;
		}
		if(strlen($to) != 0){
			$_POST["to"] = $to;
		}
		
		if(strlen($orderitems) != 0){
			$_POST["orderitems"] = $orderitems;
		}
		if(strlen($ordertotal) != 0){
			$_POST["OrderTotal"] = "Total: Php " .$ordertotal;
		}
		if(strlen($orderID) != 0){
			$_POST["psm"] = $orderID;
		}
		
		$_POST["ManageOrders"] = "set";
		$conn->close();
		redirectPOSTdata("ManageOrders.php");
		exit();
	}
?> 