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
			
			// Edit User
			foreach ($_POST as $a => $b) {
			
				$pattern = "/edit(\d)+/";
				$pattern2 = "/delete(\d)+/";
				if(preg_match($pattern, $a)){
					$outputString = preg_replace('/[^0-9]/', '', $a);
					$ID = (int)$outputString;
					
					$open = 1;
					
					if(isset($_POST["UserIsActive".$ID]) && $_POST["UserIsActive".$ID] == "Active"){
						$open = 1;
					}
					else if(isset($_POST["UserIsActive".$ID]) && $_POST["UserIsActive".$ID] == "Inactive"){
						$open = 0;
					}
					
					$sql = "UPDATE users SET UserIsActive='".$open."' WHERE UserID = ".$ID;
					
					if ($conn->query($sql) === TRUE) {
						unset($_POST);
						$_POST["Result"] = "";
						
						// Query all users and leave
						$sql = "SELECT * from users where UserType = 3 and BranchID = ".$_SESSION["User"]["BranchID"]."
						";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								while($row = $result->fetch_assoc()) {
									$_POST["Result"] .= appendResult($row["UserImageLocation"],$row["UserName"],$row["UserEmail"],$row["UserCellphone"],$row["UserID"],$row["UserIsActive"]);
								}
							}
						
						$_POST["alert"] =
							'
								<div id = "alert" class="alert alert-success fade in">			
									<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
									<strong>User Edited!</strong>
								</div>
							';
						$_POST["ManageEmployees"] = "set";
						
						$conn->close();
						redirectPOSTdata("ManageEmployees.php");
						exit();
						
					}
					else{
						echo "Error: " . $sql . "<br>" . $conn->error;
					}
				}
			}
			
			// Add Employee
			
			if(isset($_POST["Add_User"])){
				if(	strlen($_POST["UserName"]) == 0 || 
					strlen($_POST["UserEmail"]) == 0 ||
					strlen($_POST["UserPassword"]) == 0 ||
					strlen($_POST["cUserPassword"]) == 0					
				
				){
					unset($_POST);
						$_POST["Result"] = "";
						
						// Query all users and leave
						$sql = "SELECT * from users where UserType = 3 and BranchID = ".$_SESSION["User"]["BranchID"]."
						";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								while($row = $result->fetch_assoc()) {
									$_POST["Result"] .= appendResult($row["UserImageLocation"],$row["UserName"],$row["UserEmail"],$row["UserCellphone"],$row["UserID"],$row["UserIsActive"]);
								}
							}
						
						$_POST["alert"] =
							'
								<div id = "alert" class="alert alert-danger fade in">			
									<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
									<strong>Required Fields Blank!</strong> Please fill out all required data.
								</div>
							';
						$_POST["ManageEmployees"] = "set";
						
						$conn->close();
						redirectPOSTdata("ManageEmployees.php");
						exit();
				}
				else {
					
					// Check Account Uniqueness
					$sql = "SELECT * from users where UserEmail = '".$_POST["UserEmail"]."'";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						unset($_POST);
						$_POST["Result"] = "";
						
						// Query all users and leave
						$sql = "SELECT * from users where UserType = 3 and BranchID = ".$_SESSION["User"]["BranchID"]."
						";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								while($row = $result->fetch_assoc()) {
									$_POST["Result"] .= appendResult($row["UserImageLocation"],$row["UserName"],$row["UserEmail"],$row["UserCellphone"],$row["UserID"],$row["UserIsActive"]);
								}
							}
						
						$_POST["alert"] =
							'
								<div id = "alert" class="alert alert-danger fade in">			
									<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
									<strong>Account Exists!</strong> Please enter a different email.
								</div>
							';
						$_POST["ManageEmployees"] = "set";
						
						$conn->close();
						redirectPOSTdata("ManageEmployees.php");
						exit();
					}
					
					// Check Password Fields Match
					if(strcmp($_POST["UserPassword"],$_POST["cUserPassword"]) != 0){
						unset($_POST);
						$_POST["Result"] = "";
						
						// Query all users and leave
						$sql = "SELECT * from users where UserType = 3 and BranchID = ".$_SESSION["User"]["BranchID"]."
						";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								while($row = $result->fetch_assoc()) {
									$_POST["Result"] .= appendResult($row["UserImageLocation"],$row["UserName"],$row["UserEmail"],$row["UserCellphone"],$row["UserID"],$row["UserIsActive"]);
								}
							}
						
						$_POST["alert"] =
							'
								<div id = "alert" class="alert alert-danger fade in">			
									<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
									<strong>Password fields don\'t match!</strong>
								</div>
							';
						$_POST["ManageEmployees"] = "set";
						
						$conn->close();
						redirectPOSTdata("ManageEmployees.php");
						exit();
					}
					
					// Insert User
					else{
						
						// No Image
						if(!file_exists($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
							
							$sql = "INSERT INTO users (UserName,UserEmail,UserPassword,UserCellphone,UserIsActive,UserType,BranchID)
								VALUES ('".$_POST["UserName"]."','".$_POST["UserEmail"]."','".$_POST["UserPassword"]."','".$_POST["UserCellphone"]."','1','3','".$_SESSION["User"]["BranchID"]."')";
						
							
							if ($conn->query($sql) === TRUE) {
								$last_id = $conn->insert_id;
								unset($_POST);
								$_POST["Result"] = "";
								
								// Query all users and leave
								$sql = "SELECT * from users where UserType = 3 and BranchID = ".$_SESSION["User"]["BranchID"]."
								";
									$result = $conn->query($sql);

									if ($result->num_rows > 0) {
										while($row = $result->fetch_assoc()) {
											$_POST["Result"] .= appendResult($row["UserImageLocation"],$row["UserName"],$row["UserEmail"],$row["UserCellphone"],$row["UserID"],$row["UserIsActive"]);
										}
									}
								
								$_POST["alert"] =
									'
										<div id = "alert" class="alert alert-success fade in">			
											<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
											<strong>User Account Created!</strong>
										</div>
									';
								$_POST["ManageEmployees"] = "set";
								
								$conn->close();
								redirectPOSTdata("ManageEmployees.php");
								exit();
							} 
							else {
								echo "Error: " . $sql . "<br>" . $conn->error;
							}
						}
						
						// Has Image
						else{
							// Insert User
							$sql = "INSERT INTO users (UserName,UserEmail,UserPassword,UserCellphone,UserIsActive,UserType,BranchID)
								VALUES ('".$_POST["UserName"]."','".$_POST["UserEmail"]."','".$_POST["UserPassword"]."','".$_POST["UserCellphone"]."','1','3','".$_SESSION["User"]["BranchID"]."')";
							
							if ($conn->query($sql) === TRUE) {
								$last_id = $conn->insert_id;
								
								// Insert Image
								// Upload Image
								$target_dir = "../img/Users/";
								$target_file = $target_dir . basename($_FILES["image"]["name"]);
								$uploadOk = 1;
								$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
								$fileName = $target_dir . $last_id;
								$destination = $target_dir . $last_id .".". $imageFileType;
								

								// Check if image file is a actual image or fake image
								$check = getimagesize($_FILES["image"]["tmp_name"]);
								if($check !== false) {
									$uploadOk = 1;
								} 
								else {
									$_POST["alert"] = 
										'
											<div id = "alert" class="alert alert-danger fade in">
											<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
											<strong>Image Upload Error!</strong> The selected file is not an image.
											</div>
										';
									$uploadOk = 0;
								}
								
								// Check if file exists
								$img_extensions = array("tiff","pjp","jfif","gif","svg","bmp","png","jpeg","svgz","jpg","webp","ico","xbm","dib","tif","pjpeg","avif");
								
								foreach ($img_extensions as $extension) {
									if(file_exists($fileName.".".$extension)){
										if (!unlink($fileName.".".$extension)) {
										}
										else {
										}
									}
								}					

								// Check file size
								if ($_FILES["image"]["size"] > 1000000) {
								  $_POST["alert"] = 
										'
											<div id = "alert" class="alert alert-danger fade in">
											<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
											<strong>Image Upload Error!</strong> The file you selected is too large.
											</div>
										';
								  $uploadOk = 0;
								}

								// Allow certain file formats
								$isValidExtension = false;
								foreach ($img_extensions as $extension) {
									if($imageFileType == $extension){
										$isValidExtension = true;
										break;
									}
								}
								
								if(!$isValidExtension){
									$_POST["alert"] = 
										'
											<div id = "alert" class="alert alert-danger fade in">
											<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
											<strong>Image Upload Error!</strong> Invalid file extension.
											</div>
										';
									$uploadOk = 0;
								}

								// Check if $uploadOk is set to 0 by an error
								if ($uploadOk == 0) {
									$_POST["Result"] = "";
									
									// Query all users and leave
									$sql = "SELECT * from users where UserType = 3 and BranchID = ".$_SESSION["User"]["BranchID"]."
									";
										$result = $conn->query($sql);

										if ($result->num_rows > 0) {
											while($row = $result->fetch_assoc()) {
												$_POST["Result"] .= appendResult($row["UserImageLocation"],$row["UserName"],$row["UserEmail"],$row["UserCellphone"],$row["UserID"],$row["UserIsActive"]);
											}
										}
										
									$_POST["ManageEmployees"] = "set";
									
									$conn->close();
									redirectPOSTdata("ManageEmployees.php");
									exit();
									
								// if everything is ok, try to upload file
								} 
								else {
									if (move_uploaded_file($_FILES["image"]["tmp_name"], $destination)) {
										
									} else {
										
									}
								}
								
								// Update user table
								
								if(file_exists($destination)){
									
									$sql = "UPDATE users SET UserImageLocation='".$destination."' WHERE UserID = ".$last_id;
									if ($conn->query($sql) === TRUE) {
									  
										unset($_POST);
										$_POST["Result"] = "";
										
										// Query all users and leave
										$sql = "SELECT * from users where UserType = 3 and BranchID = ".$_SESSION["User"]["BranchID"]."
										";
											$result = $conn->query($sql);

											if ($result->num_rows > 0) {
												while($row = $result->fetch_assoc()) {
													$_POST["Result"] .= appendResult($row["UserImageLocation"],$row["UserName"],$row["UserEmail"],$row["UserCellphone"],$row["UserID"],$row["UserIsActive"]);
												}
											}
										
										$_POST["alert"] =
											'
												<div id = "alert" class="alert alert-success fade in">			
													<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
													<strong>User Account Created!</strong>
												</div>
											';
										$_POST["ManageEmployees"] = "set";
										
										$conn->close();
										redirectPOSTdata("ManageEmployees.php");
										exit();
										
									}
									else{
										echo "Error: " . $sql . "<br>" . $conn->error;
									}
								}
							}
						}
					}
				}
			}
			
			else if(!isset($_POST["ManageEmployees"])){
				unset($_POST);
				$_POST["Result"] = "";
				
				// Query all users and leave
				$sql = "SELECT * from users where UserType = 3 and BranchID = ".$_SESSION["User"]["BranchID"]."
				";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							$_POST["Result"] .= appendResult($row["UserImageLocation"],$row["UserName"],$row["UserEmail"],$row["UserCellphone"],$row["UserID"],$row["UserIsActive"]);
						}
					}
				$_POST["ManageEmployees"] = "set";
				
				$conn->close();
				redirectPOSTdata("ManageEmployees.php");
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
	
	function appendResult($UserImageLocation,$UserName,$UserEmail,$UserCellphone,$UserID,$UserIsActive){
		$opencheck = "";
		$closedcheck = "";
		if($UserIsActive){
			$opencheck = "checked";
		}
		else{
			$closedcheck = "checked";
		}
		
		if(!file_exists($UserImageLocation)){
			$UserImageLocation = "https://via.placeholder.com/32";
		}
		$str = 
			'
			<tr>
				<td><img src = "'.$UserImageLocation.'" class="img-circle small-image"></td>
				<td>'.$UserName.'</td>
				<td>'.$UserEmail.'</td>
				<td>'.$UserCellphone.'</td>
				<td>
					<div class="radio">
						<label><input type="radio" name="UserIsActive'.$UserID.'" value = "Active" '.$opencheck.'>Active</label>
					</div>
					<div class="radio">
						<label><input type="radio" name="UserIsActive'.$UserID.'" value = "Inactive" '.$closedcheck.'>Inactive</label>
					</div>
				</td>
				<td><button type="submit" name = "edit'.$UserID.'" value = "edit'.$UserID.'" class="btn btn-default"><span class = "glyphicon glyphicon-edit"></span></button></td>
			</tr>
			';
		return $str;
	}
?> 