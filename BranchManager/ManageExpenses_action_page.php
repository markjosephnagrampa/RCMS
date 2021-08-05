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
			
			// Delete Expense
			foreach ($_POST as $a => $b) {
			
				$pattern = "/edit(\d)+/";
				$pattern2 = "/delete(\d)+/";
				if(preg_match($pattern2, $a)){
					$outputString = preg_replace('/[^0-9]/', '', $a);
					$ID = (int)$outputString;
					
					$sql = "UPDATE expenses SET ExpenseIsDeleted='1' WHERE ExpenseID = ".$ID;
					
					if ($conn->query($sql) === TRUE) {
						unset($_POST);
						$_POST["Result"] = "";
						
						// Query all expenses and leave
						$sql = "SELECT * from expenses where ExpenseIsDeleted = '0' and BranchID = ".$_SESSION["User"]["BranchID"]."
						";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								while($row = $result->fetch_assoc()) {
									$_POST["Result"] .= appendResult($row["ExpenseName"],$row["ExpenseDateIncurred"],$row["ExpenseAmount"],$row["ExpenseID"]);
								}
							}
						
						$_POST["alert"] =
							'
								<div id = "alert" class="alert alert-success fade in">			
									<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
									<strong>Expense Deleted!</strong>
								</div>
							';
						$_POST["ManageExpenses"] = "set";
						
						$conn->close();
						redirectPOSTdata("ManageExpenses.php");
						exit();
						
					}
					else{
						echo "Error: " . $sql . "<br>" . $conn->error;
					}
				}
			}
			
			// Add Expense
			
			if(isset($_POST["Add_Expense"])){
				if(	strlen($_POST["ExpenseName"]) == 0 || 
					strlen($_POST["ExpenseDateIncurred"]) == 0 ||
					strlen($_POST["ExpenseAmount"]) == 0
				
				){
					// Query all expenses and leave
					unset($_POST);
					$_POST["Result"] = "";
					
					$sql = "SELECT * from expenses where ExpenseIsDeleted = '0' and BranchID = ".$_SESSION["User"]["BranchID"]."
					";
						$result = $conn->query($sql);

						if ($result->num_rows > 0) {
							while($row = $result->fetch_assoc()) {
								$_POST["Result"] .= appendResult($row["ExpenseName"],$row["ExpenseDateIncurred"],$row["ExpenseAmount"],$row["ExpenseID"]);
							}
						}
					
					$_POST["alert"] =
						'
							<div id = "alert" class="alert alert-danger fade in">			
								<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
								<strong>Required Fields Blank!</strong> Please fill out all required data.
							</div>
						';
					$_POST["ManageExpenses"] = "set";
					
					$conn->close();
					redirectPOSTdata("ManageExpenses.php");
					exit();
						
						
				}
				else {
						
						$sql = "INSERT INTO expenses (ExpenseName,ExpenseDateIncurred,ExpenseIsDeleted,BranchID,ExpenseAmount)
							VALUES ('".$_POST["ExpenseName"]."','".$_POST["ExpenseDateIncurred"]."','0','".$_SESSION["User"]["BranchID"]."','".$_POST["ExpenseAmount"]."')";
					
						
						if ($conn->query($sql) === TRUE) {
							unset($_POST);
							$_POST["Result"] = "";
							
							// Query all expenses and leave
							unset($_POST);
							$_POST["Result"] = "";
							
							$sql = "SELECT * from expenses where ExpenseIsDeleted = '0' and BranchID = ".$_SESSION["User"]["BranchID"]."
							";
								$result = $conn->query($sql);

								if ($result->num_rows > 0) {
									while($row = $result->fetch_assoc()) {
										$_POST["Result"] .= appendResult($row["ExpenseName"],$row["ExpenseDateIncurred"],$row["ExpenseAmount"],$row["ExpenseID"]);
									}
								}
							
							$_POST["alert"] =
								'
									<div id = "alert" class="alert alert-success fade in">			
										<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
										<strong>Expense Inserted!</strong>
									</div>
								';
							$_POST["ManageExpenses"] = "set";
							
							$conn->close();
							redirectPOSTdata("ManageExpenses.php");
							exit();
						} 
						else {
							echo "Error: " . $sql . "<br>" . $conn->error;
						}
							
						
					}
			}
			
			else if(isset($_POST["Search"])){
				
				// Blank Date Check
				if(strlen($_POST["from"]) == 0 ||
					strlen($_POST["to"]) == 0
				){
					// Query all expenses and leave
					unset($_POST);
					$_POST["Result"] = "";
					
					$sql = "SELECT * from expenses where ExpenseIsDeleted = '0' and BranchID = ".$_SESSION["User"]["BranchID"]."
					";
						$result = $conn->query($sql);

						if ($result->num_rows > 0) {
							while($row = $result->fetch_assoc()) {
								$_POST["Result"] .= appendResult($row["ExpenseName"],$row["ExpenseDateIncurred"],$row["ExpenseAmount"],$row["ExpenseID"]);
							}
						}
					
					$_POST["alert"] =
						'
							<div id = "alert" class="alert alert-danger fade in">			
								<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
								<strong>Required Fields Blank!</strong> Please fill out all required data.
							</div>
						';
					$_POST["ManageExpenses"] = "set";
					
					$conn->close();
					redirectPOSTdata("ManageExpenses.php");
					exit();
				}
				
				else{
					// Date Range Check
					$from = date_create($_POST["from"]);
					$to = date_create($_POST["to"]);
					
					if($to < $from){
						// Query all expenses and leave
						unset($_POST);
						$_POST["Result"] = "";
						
						$sql = "SELECT * from expenses where ExpenseIsDeleted = '0' and BranchID = ".$_SESSION["User"]["BranchID"]."
						";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								while($row = $result->fetch_assoc()) {
									$_POST["Result"] .= appendResult($row["ExpenseName"],$row["ExpenseDateIncurred"],$row["ExpenseAmount"],$row["ExpenseID"]);
								}
							}
						
						$_POST["alert"] =
							'
								<div id = "alert" class="alert alert-danger fade in">			
									<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
									<strong>Invalid Date Range!</strong>
								</div>
							';
						$_POST["ManageExpenses"] = "set";
						
						$conn->close();
						redirectPOSTdata("ManageExpenses.php");
						exit();
					}
					
					// Filter by Search Criteria
					else{
						// Query all expenses and leave
						$_POST["Result"] = "";
						
						$sql = "SELECT * from expenses where ExpenseIsDeleted = '0' and BranchID = ".$_SESSION["User"]["BranchID"]." 
							and
								ExpenseDateIncurred >= '".$_POST["from"]."' and ExpenseDateIncurred <= '".$_POST["to"]."'
						";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								while($row = $result->fetch_assoc()) {
									$_POST["Result"] .= appendResult($row["ExpenseName"],$row["ExpenseDateIncurred"],$row["ExpenseAmount"],$row["ExpenseID"]);
								}
							}
							else{
								echo "Error: " . $sql . "<br>" . $conn->error;
							}
						
						$_POST["ManageExpenses"] = "set";
						
						$conn->close();
						redirectPOSTdata("ManageExpenses.php");
						exit();
					}
				}
				
			}
			
			else if(!isset($_POST["ManageExpenses"])){
				unset($_POST);
				$_POST["Result"] = "";
				
				// Query all expenses and leave
				unset($_POST);
				$_POST["Result"] = "";
				
				$sql = "SELECT * from expenses where ExpenseIsDeleted = '0' and BranchID = ".$_SESSION["User"]["BranchID"]."
				";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							$_POST["Result"] .= appendResult($row["ExpenseName"],$row["ExpenseDateIncurred"],$row["ExpenseAmount"],$row["ExpenseID"]);
							
						}
					}
				
				$_POST["ManageExpenses"] = "set";
				
				$conn->close();
				redirectPOSTdata("ManageExpenses.php");
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
	
	function appendResult($ExpenseName,$ExpenseDateIncurred,$ExpenseAmount,$ExpenseID){
		$ExpenseDateIncurred=date_create($ExpenseDateIncurred);
		$ExpenseDateIncurred = date_format($ExpenseDateIncurred,"M d, Y");
		$str = 
			'
			<tr>
				<td>'.$ExpenseName.'</td>
				<td>'.$ExpenseDateIncurred.'</td>
				<td>Php '.$ExpenseAmount.'</td>
				<td><button type="submit" name = "delete'.$ExpenseID.'" value = "delete'.$ExpenseID.'" class="btn btn-default"><span class = "glyphicon glyphicon-remove"></span></button></td>
			</tr>
			';
		return $str;
	}
?> 