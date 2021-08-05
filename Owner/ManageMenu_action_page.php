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
			
			// Edit and Delete Check 
			foreach ($_POST as $a => $b) {
			
				$pattern = "/edit(\d)+/";
				$pattern2 = "/delete(\d)+/";
				if(preg_match($pattern, $a)){
					$outputString = preg_replace('/[^0-9]/', '', $a);
					$ItemID = (int)$outputString;
					
					
					// No Image
					if(!file_exists($_FILES['image'.$ItemID]['tmp_name']) || !is_uploaded_file($_FILES['image'.$ItemID]['tmp_name'])) {
						if(strlen($_POST["ItemPrice".$ItemID]) == 0){
							// Query all items and leave
							
							unset($_POST);
							$_POST["Result"] = "";
							$sql = "SELECT * from items where ItemIsDeleted = '0'";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								// output data of each row
								while($row = $result->fetch_assoc()) {
									$_POST["Result"] .= appendResult($row["ItemID"], $row["ItemName"], $row["ItemPrice"], $row["ItemImageLocation"]);
								}
							} else {
							  
							}
							
							$_POST["alert"] =
								'
									<div id = "alert" class="alert alert-danger fade in">
									<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
									<strong>No item price indicated!</strong>
									</div>
								';
							$_POST["ManageMenu"] = "set";
							
							$conn->close();
							redirectPOSTdata("ManageMenu.php");
							exit();
							
						}
						$sql = "UPDATE items SET ItemPrice='".$_POST["ItemPrice".$ItemID]."' WHERE ItemID = ".$ItemID;
						if ($conn->query($sql) === TRUE) {
						  
							// Query all items and leave
							
							unset($_POST);
							$_POST["Result"] = "";
							$sql = "SELECT * from items where ItemIsDeleted = '0'";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								// output data of each row
								while($row = $result->fetch_assoc()) {
									$_POST["Result"] .= appendResult($row["ItemID"], $row["ItemName"], $row["ItemPrice"], $row["ItemImageLocation"]);
								}
							} else {
							  
							}
							
							$_POST["alert"] =
								'
									<div id = "alert" class="alert alert-success fade in">
									<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
									<strong>Item Edited!</strong>
									</div>
								';
							$_POST["ManageMenu"] = "set";
							
							$conn->close();
							redirectPOSTdata("ManageMenu.php");
							exit();
						  
						} else {
						}
					}
					// Has Image
					else{
						if(strlen($_POST["ItemPrice".$ItemID]) == 0){
							// Query all items and leave
							
							unset($_POST);
							$_POST["Result"] = "";
							$sql = "SELECT * from items where ItemIsDeleted = '0'";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								// output data of each row
								while($row = $result->fetch_assoc()) {
									$_POST["Result"] .= appendResult($row["ItemID"], $row["ItemName"], $row["ItemPrice"], $row["ItemImageLocation"]);
								}
							} else {
							  
							}
							
							$_POST["alert"] =
								'
									<div id = "alert" class="alert alert-danger fade in">
									<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
									<strong>No item price indicated!</strong>
									</div>
								';
							$_POST["ManageMenu"] = "set";
							
							$conn->close();
							redirectPOSTdata("ManageMenu.php");
							exit();
							
						}
						else{
							// Upload Image
							$target_dir = "../img/Items/";
							$target_file = $target_dir . basename($_FILES["image".$ItemID]["name"]);
							$uploadOk = 1;
							$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
							$fileName = $target_dir . $ItemID;
							$destination = $target_dir . $ItemID .".". $imageFileType;
							

							// Check if image file is a actual image or fake image
							$check = getimagesize($_FILES["image".$ItemID]["tmp_name"]);
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
							if ($_FILES["image".$ItemID]["size"] > 1000000) {
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
								// Query all items and leave
								$_POST["Result"] = "";
								$sql = "SELECT * from items where ItemIsDeleted = '0'";
								$result = $conn->query($sql);

								if ($result->num_rows > 0) {
									// output data of each row
									while($row = $result->fetch_assoc()) {
										$_POST["Result"] .= appendResult($row["ItemID"], $row["ItemName"], $row["ItemPrice"], $row["ItemImageLocation"]);
									}
								} else {
								  
								}
								
								$_POST["ManageMenu"] = "set";								
								
								$conn->close();
								redirectPOSTdata("ManageMenu.php");
								exit();
								
							// if everything is ok, try to upload file
							} 
							else {
								if (move_uploaded_file($_FILES["image".$ItemID]["tmp_name"], $destination)) {
									
								} else {
									
								}
							}
							
							// Update item table
							
							if(file_exists($destination)){
								$sql = "UPDATE items SET ItemPrice = '".$_POST["ItemPrice".$ItemID]."', ItemImageLocation='".$destination."' WHERE ItemID = ".$ItemID;
								if ($conn->query($sql) === TRUE) {
								  // Query all items and leave
									unset($_POST);
									$_POST["Result"] = "";
									$sql = "SELECT * from items where ItemIsDeleted = '0'";
									$result = $conn->query($sql);

									if ($result->num_rows > 0) {
										// output data of each row
										while($row = $result->fetch_assoc()) {
											$_POST["Result"] .= appendResult($row["ItemID"], $row["ItemName"], $row["ItemPrice"], $row["ItemImageLocation"]);
										}
									} else {
									  
									}
									
									
									$_POST["alert"] =
										'
											<div id = "alert" class="alert alert-success fade in">
											<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
											<strong>Item Edited!</strong>
											</div>
										';
									$_POST["ManageMenu"] = "set";
									
									
									$conn->close();
									redirectPOSTdata("ManageMenu.php");
									exit();
								} else {
								}
							}
						}
					}
				}
				// Delete
				else if(preg_match($pattern2, $a)){
					$outputString = preg_replace('/[^0-9]/', '', $a);
					$ItemID = (int)$outputString;
					
					$sql = "UPDATE items SET ItemIsDeleted = '1' WHERE ItemID = ".$ItemID;
					if ($conn->query($sql) === TRUE) {
					  // Query all items and leave
						unset($_POST);
						$_POST["Result"] = "";
						$sql = "SELECT * from items where ItemIsDeleted = '0'";
						$result = $conn->query($sql);

						if ($result->num_rows > 0) {
							// output data of each row
							while($row = $result->fetch_assoc()) {
								$_POST["Result"] .= appendResult($row["ItemID"], $row["ItemName"], $row["ItemPrice"], $row["ItemImageLocation"]);
							}
						} else {
						  
						}
						
						
						$_POST["alert"] =
							'
								<div id = "alert" class="alert alert-success fade in">
								<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
								<strong>Item Deleted!</strong>
								</div>
							';
						$_POST["ManageMenu"] = "set";
						
						
						$conn->close();
						redirectPOSTdata("ManageMenu.php");
						exit();
					} else {
					}
				}
			}
			
			// Initial Page Load Check
			
			if(isset($_POST["Add_Item"])){
				if(strlen($_POST["ItemName"]) == 0 || strlen($_POST["ItemPrice"]) == 0){
					unset($_POST);
					$_POST["alert"] =
						'
							<div id = "alert" class="alert alert-danger fade in">			
								<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
								<strong>Required Fields Blank!</strong> Please fill out all required data.
							</div>
						';
					$_POST["ManageMenu"] = "set";
					
					$conn->close();
					redirectPOSTdata("ManageMenu.php");
					exit();
				}
				else {
				
					// Check Item Uniqueness
					$sql = "SELECT * from items where ItemName = '".$_POST["ItemName"]."' and ItemIsDeleted = '0'";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						unset($_POST);
						$_POST["alert"] = 
							'
								<div id = "alert" class="alert alert-danger fade in">
									<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
									<strong>Item Exists!</strong> Please enter a different item name.
								</div>
							';
							
						$_POST["ManageMenu"] = "set";
						
						$conn->close();
						redirectPOSTdata("ManageMenu.php");
						exit();
					}
					
					// Insert Item
					else{
						// No Image
						if(!file_exists($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
								
							$sql = "INSERT INTO items (ItemName, ItemPrice, ItemIsDeleted)
								VALUES ('".$_POST["ItemName"]."', '".$_POST["ItemPrice"]."', '0')";
							
							if ($conn->query($sql) === TRUE) {
								$last_id = $conn->insert_id;
								
								// Create ItemAvailability record for each branch
								
								$sql = "SELECT * from branches";
								$result = $conn->query($sql);

								if ($result->num_rows > 0) {
									
									while($row = $result->fetch_assoc()) {
										$sql2 = "INSERT INTO itemavailability (ItemID, BranchID, ItemIsAvailable)
										VALUES ('".$last_id."','".$row["BranchID"]."','1')";
										
										if ($conn->query($sql2) === TRUE) {
										
										}
										else{
											
										}
									}
								} 
								else {
								  
								}
								
								// Query all items and leave
								
								unset($_POST);
								$_POST["Result"] = "";
								$sql = "SELECT * from items where ItemIsDeleted = '0'";
								$result = $conn->query($sql);

								if ($result->num_rows > 0) {
									// output data of each row
									while($row = $result->fetch_assoc()) {
										$_POST["Result"] .= appendResult($row["ItemID"], $row["ItemName"], $row["ItemPrice"], $row["ItemImageLocation"]);
									}
								} else {
								  
								}
								
								
								$_POST["alert"] =
									'
										<div id = "alert" class="alert alert-success fade in">
											<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
											<strong>Item Inserted!</strong>
										</div>
									';
								$_POST["ManageMenu"] = "set";
								
								
								$conn->close();
								redirectPOSTdata("ManageMenu.php");
								exit();
								
							} 
							else {
							}
						}
						
						// Has Image
						else{
							// Insert Name and Price
							$sql = "INSERT INTO items (ItemName, ItemPrice, ItemIsDeleted)
								VALUES ('".$_POST["ItemName"]."', '".$_POST["ItemPrice"]."', '0')";
							
							$last_id = -1;
							
							if ($conn->query($sql) === TRUE) {
								$last_id = $conn->insert_id;
								
								// Create ItemAvailability record for each branch
								
								$sql = "SELECT * from branches";
								$result = $conn->query($sql);

								if ($result->num_rows > 0) {
									
									while($row = $result->fetch_assoc()) {
										$sql2 = "INSERT INTO itemavailability (ItemID, BranchID, ItemIsAvailable)
										VALUES ('".$last_id."','".$row["BranchID"]."','1')";
										
										if ($conn->query($sql2) === TRUE) {
										
										}
										else{
											
										}
									}
								} 
								else {
								  
								}
							} 
							else {
							}
							
							// Upload Image
							$target_dir = "../img/Items/";
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
								// Query all items and leave
								$_POST["Result"] = "";
								$sql = "SELECT * from items where ItemIsDeleted = '0'";
								$result = $conn->query($sql);

								if ($result->num_rows > 0) {
									// output data of each row
									while($row = $result->fetch_assoc()) {
										$_POST["Result"] .= appendResult($row["ItemID"], $row["ItemName"], $row["ItemPrice"], $row["ItemImageLocation"]);
									}
								} else {
								  
								}
								
								$_POST["ManageMenu"] = "set";								
								
								$conn->close();
								redirectPOSTdata("ManageMenu.php");
								exit();
								
							// if everything is ok, try to upload file
							} 
							else {
								if (move_uploaded_file($_FILES["image"]["tmp_name"], $destination)) {
									
								} else {
									
								}
							}
							
							// Update item table
							
							if(file_exists($destination)){
								$sql = "UPDATE items SET ItemImageLocation='".$destination."' WHERE ItemID = ".$last_id;
								if ($conn->query($sql) === TRUE) {
								  // Query all items and leave
									unset($_POST);
									$_POST["Result"] = "";
									$sql = "SELECT * from items where ItemIsDeleted = '0'";
									$result = $conn->query($sql);

									if ($result->num_rows > 0) {
										// output data of each row
										while($row = $result->fetch_assoc()) {
											$_POST["Result"] .= appendResult($row["ItemID"], $row["ItemName"], $row["ItemPrice"], $row["ItemImageLocation"]);
										}
									} else {
									  
									}
									
									
									$_POST["alert"] =
										'
											<div id = "alert" class="alert alert-success fade in">
											<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
											<strong>Item Inserted!</strong>
											</div>
										';
									$_POST["ManageMenu"] = "set";
									
									
									$conn->close();
									redirectPOSTdata("ManageMenu.php");
									exit();
								} else {
								}
							}
							
						}
					}
				}
			}
			
			else if(!isset($_POST["ManageMenu"])){
				unset($_POST);
				$_POST["ManageMenu"] = "set";
				$_POST["Result"] = "";
				// Load all menu items
				
				$sql = "SELECT * from items where ItemIsDeleted = '0'";
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
					// output data of each row
					while($row = $result->fetch_assoc()) {
						$_POST["Result"] .= appendResult($row["ItemID"], $row["ItemName"], $row["ItemPrice"], $row["ItemImageLocation"]);
					}
				} else {
				  
				}
				
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