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
		if($_SESSION["User"]["UserType"] != 1){
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
			
			if(isset($_POST["search"])){
				// Required Fields Check
				
				if(!isset($_POST["from"]) || !isset($_POST["to"]) || strlen($_POST["from"]) == 0 || strlen($_POST["to"]) == 0){
					queryAndRedirect(true,"<strong>Required Fields Blank!</strong> Please fill out all required data.","","","","");
				}
				
				// Valid Date Range Check
				else{
					$from_dateStr=date_create($_POST["from"]);
					$to_dateStr=date_create($_POST["to"]);
					
					$from2 = date_format($from_dateStr,"Y-m-d");
					$to2 = date_format($to_dateStr,"Y-m-d");
					
					if($from_dateStr > $to_dateStr){
						queryAndRedirect(true,"<strong>Invalid Date Range!</strong> Please select a valid duration.","","","","");
					}
					// Query within range 
					else{
						$sql = "SELECT * from orders
							where OrderIsDeleted = '0' and
							DATE(OrderDateCreated) >= '".$from2."' and
							DATE(OrderDateCreated) <= '".$to2."' and
						";
						
						$sql2 = "SELECT * from expenses
							where ExpenseIsDeleted = '0' and
							ExpenseDateIncurred >= '".$from2."' and
							ExpenseDateIncurred <= '".$to2."' and
						";
						
						queryAndRedirect(true,"",$sql,$sql2,$from2,$to2);
					}
				}
			}
			
			// PDF Generation
			else if(isset($_POST["printSummary"])){
				// Required Fields Check
				
				if(!isset($_POST["from"]) || !isset($_POST["to"]) || strlen($_POST["from"]) == 0 || strlen($_POST["to"]) == 0){
					queryForPDF("","","","");
				}
				
				// Valid Date Range Check
				else{
					$from_dateStr=date_create($_POST["from"]);
					$to_dateStr=date_create($_POST["to"]);
					
					$from2 = date_format($from_dateStr,"Y-m-d");
					$to2 = date_format($to_dateStr,"Y-m-d");
					
					if($from_dateStr > $to_dateStr){
						queryAndRedirect(true,"<strong>Invalid Date Range!</strong> Please select a valid duration.","","","","");
					}
					// Query within range 
					else{
						$sql = "SELECT * from orders
							where OrderIsDeleted = '0' and
							DATE(OrderDateCreated) >= '".$from2."' and
							DATE(OrderDateCreated) <= '".$to2."' and
						";
						
						$sql2 = "SELECT * from expenses
							where ExpenseIsDeleted = '0' and
							ExpenseDateIncurred >= '".$from2."' and
							ExpenseDateIncurred <= '".$to2."' and
						";
						queryForPDF($sql,$sql2,$from2,$to2);
					}
				}
			}
			
			// Page Load
			else if(!isset($_POST["ChainInfo"])){
				queryAndRedirect("","","","","","");
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
	
	function appendResult($BranchName,$UserName,$NetProfit,$TotalSales,$TotalExpenses,$TotalOrders){
		$NetProfit = number_format($NetProfit, 2, '.', ',');
		$TotalSales = number_format($TotalSales, 2, '.', ',');
		$TotalExpenses = number_format($TotalExpenses, 2, '.', ',');
		$str = 
			'
			<tr>
				<td>'.$BranchName.'</td>
				<td>'.$UserName.'</td>
				<td>Php '.$NetProfit.'</td>
				<td>Php '.$TotalSales.'</td>
				<td>Php '.$TotalExpenses.'</td>
				<td>'.$TotalOrders.'</td>
			</tr>
			';
		return $str;
	}
	
	function appendHead($color,$NetProfit,$TotalSales,$TotalExpenses,$TotalOrders){
		$NetProfit = number_format($NetProfit, 2, '.', ',');
		$TotalSales = number_format($TotalSales, 2, '.', ',');
		$TotalExpenses = number_format($TotalExpenses, 2, '.', ',');
		$str = 
		'
			<tr class = "'.$color.'">
				<td>Php '.$NetProfit.'</td>
				<td>Php '.$TotalSales.'</td>
				<td>Php '.$TotalExpenses.'</td>
				<td>'.$TotalOrders.'</td>
			</tr>
		';
		return $str;
	}
	
	function queryForPDF($altSql,$altSql2,$from,$to){
		global $conn;
		$TotalSales = 0.0;
		$TotalExpenses = 0.0;
		$TotalOrders = 0;
		$i = 0;
		
		// Query all branches
		unset($_POST);
		$_POST["Branches"] = array();
		
		$_POST["Summary"] = array();
		
		$sql = "Select * from branches join users on branches.ManagerID = users.UserID";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				
				$bTotalSales = 0.0;
				$bTotalExpenses = 0.0;
				$bTotalOrders = 0;
				
				$sql2 = "SELECT * from orders
					where OrderIsDeleted = '0' and
					BranchID = '".$row["BranchID"]."'
				";
				
				if(strlen($altSql) > 0){
					$sql2 = $altSql;
					$sql2 .= "BranchID = '".$row["BranchID"]."'";
				}
				
				$result2 = $conn->query($sql2);

				if ($result2->num_rows > 0) {
					while($row2 = $result2->fetch_assoc()) {
						
						$bTotalOrders++;
						$bTotalSales += $row2["OrderTotal"];
						
						$TotalOrders++;
						$TotalSales += $row2["OrderTotal"];
					}
				}
				
				// Query all expenses
				$sql2 = "SELECT * from expenses
					where ExpenseIsDeleted = '0' and
					BranchID = '".$row["BranchID"]."'
				";
				
				if(strlen($altSql2) > 0){
					$sql2 = $altSql2;
					$sql2 .= "BranchID = '".$row["BranchID"]."'";
				}
				
				$result2 = $conn->query($sql2);

				if ($result2->num_rows > 0) {
					while($row2 = $result2->fetch_assoc()) {
						
						$bTotalExpenses += $row2["ExpenseAmount"];
						$TotalExpenses += $row2["ExpenseAmount"];
					}
				}
				
				$bNetProfit = $bTotalSales - $bTotalExpenses;
				
				$_POST["Branches"][$i++] = array($row["BranchAddress"],$row["UserName"],"Php ".number_format($bNetProfit, 2, '.', ','),"Php ".number_format($bTotalSales, 2, '.', ','),"Php ".number_format($bTotalExpenses, 2, '.', ','),$bTotalOrders);
			}
		}
		
		$NetProfit = $TotalSales - $TotalExpenses;
		
		$_POST["Summary"][0] = array("Php ".number_format($NetProfit, 2, '.', ','),"Php ".number_format($TotalSales, 2, '.', ','),"Php ".number_format($TotalExpenses, 2, '.', ','),$TotalOrders);
		
		if(strlen($from) > 0){
			$dateStr=date_create($from);
			$date = date_format($dateStr, "M d, Y");
			
			$_POST["from"] = $date;
		}
		
		if(strlen($to) > 0){
			$dateStr=date_create($to);
			$date = date_format($dateStr, "M d, Y");
			
			$_POST["to"] = $date;
		}
		
		$_POST["Summary"] = json_encode($_POST["Summary"]);
		$_POST["Branches"] = json_encode($_POST["Branches"]);
		
		$conn->close();
		redirectPOSTdata("report.php");
		exit();
	}
	
	function queryAndRedirect($alertIsDanger,$alertText,$altSql,$altSql2,$from,$to){
		
		global $conn;
		$TotalSales = 0.0;
		$TotalExpenses = 0.0;
		$TotalOrders = 0;
		$monthSales = array(0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0);
		$monthExpenses = array(0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0);
		$netIncomes = array(0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0);
		
		// Query all branches
		unset($_POST);
		$_POST["Result"] = "";
		
		$sql = "Select * from branches join users on branches.ManagerID = users.UserID";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				
				$bTotalSales = 0.0;
				$bTotalExpenses = 0.0;
				$bTotalOrders = 0;
				
				$sql2 = "SELECT * from orders
					where OrderIsDeleted = '0' and
					BranchID = '".$row["BranchID"]."'
				";
				
				if(strlen($altSql) > 0){
					$sql2 = $altSql;
					$sql2 .= "BranchID = '".$row["BranchID"]."'";
				}
				
				$result2 = $conn->query($sql2);

				if ($result2->num_rows > 0) {
					while($row2 = $result2->fetch_assoc()) {
						
						$bTotalOrders++;
						$bTotalSales += $row2["OrderTotal"];
						
						$TotalOrders++;
						$TotalSales += $row2["OrderTotal"];
						
						// Get month
						$dateStr=date_create($row2["OrderDateCreated"]);
						$date = date_format($dateStr, "m");
						
						$index = intval($date);
						$monthSales[$index - 1] += $row2["OrderTotal"];
						
					}
				}
				
				// Query all expenses
				$sql2 = "SELECT * from expenses
					where ExpenseIsDeleted = '0' and
					BranchID = '".$row["BranchID"]."'
				";
				
				if(strlen($altSql2) > 0){
					$sql2 = $altSql2;
					$sql2 .= "BranchID = '".$row["BranchID"]."'";
				}
				
				$result2 = $conn->query($sql2);

				if ($result2->num_rows > 0) {
					while($row2 = $result2->fetch_assoc()) {
						
						// Get month
						$dateStr=date_create($row2["ExpenseDateIncurred"]);
						$date = date_format($dateStr, "m");
						
						$index = intval($date);
						$monthExpenses[$index - 1] += $row2["ExpenseAmount"];
						
						$bTotalExpenses += $row2["ExpenseAmount"];
						$TotalExpenses += $row2["ExpenseAmount"];
					}
				}
				
				$bNetProfit = $bTotalSales - $bTotalExpenses;
				
				// Append Result
				$_POST["Result"] .= appendResult($row["BranchAddress"],$row["UserName"],$bNetProfit,$bTotalSales,$bTotalExpenses,$bTotalOrders);
				
			}
		}
		
		$NetProfit = $TotalSales - $TotalExpenses;
		for ($x = 0; $x < count($netIncomes); $x++) {
		  $netIncomes[$x] = $monthSales[$x] - $monthExpenses[$x];
		}
		
		$_POST["chart"] = json_encode($netIncomes);
		
		$color = "";
		if($NetProfit == 0){
			$color = "active";
		}
		else if($NetProfit > 0){
			$color = "success";
		}
		else{
			$color = "danger";
		}
		
		$_POST["Summary"] = appendHead($color,$NetProfit,$TotalSales,$TotalExpenses,$TotalOrders);
		
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
		
		$_POST["ChainInfo"] = "set";
		$conn->close();
		redirectPOSTdata("ChainInfo.php");
		exit();
	}
?> 