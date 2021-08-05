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
			
			if(strlen($_POST["UserEmail"]) == 0 || strlen($_POST["UserPassword"]) == 0 || strlen($_POST["cUserPassword"]) == 0){
				$_POST["alert"] = 
					'
						<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
						<strong>Required Fields Blank!</strong> Please fill out all required data.
					';
				$_POST["MyProfile"] = "set";
				$conn->close();
				redirectPOSTdata("MyProfile.php");
				exit();
			}
				
			else if(strcmp($_POST["UserPassword"],$_POST["cUserPassword"]) != 0){
				$_POST["alert"] = 
					'
						<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
						<strong>Password fields don\'t match!</strong> Please make sure your passwords are identical.
					';
				$_POST["MyProfile"] = "set";
				$conn->close();
				redirectPOSTdata("MyProfile.php");
				exit();
			}
			
			else{
				
				// Check Email Uniqueness
				$sql = "SELECT * from users where UserEmail = '".$_POST["UserEmail"]."' and UserID != '".$_SESSION["User"]["UserID"]."'";
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
					unset($_POST);
					$_POST["alert"] = 
						'
								<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
								<strong>Account Exists!</strong> Please enter a different email.
							
						';
						
					$_POST["MyProfile"] = "set";
					
					$conn->close();
					redirectPOSTdata("MyProfile.php");
					exit();
				}
				
				// Check if image upload is required
				if(!file_exists($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
					$sql = "UPDATE users
					SET UserEmail = '".$_POST["UserEmail"]."', UserCellphone = '".$_POST["UserCellphone"]."' , UserPassword = '".$_POST["UserPassword"]."'
					WHERE UserID = ".$_SESSION["User"]["UserID"]."
						";
					
					if ($conn->query($sql) === TRUE) {
						// Update Session Data
						$_SESSION["User"]["UserEmail"] = $_POST["UserEmail"];
						$_SESSION["User"]["UserCellphone"] = $_POST["UserCellphone"];
						$_SESSION["User"]["UserPassword"] = $_POST["UserPassword"];
						
						unset($_POST);
						
						// Show confirmatory message
						$_POST["isSuccess"] = "set";
						$_POST["alert"] = 
							'
								<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
								<strong>Profile Updated!</strong>
							';
						
						$_POST["MyProfile"] = "set";
						$_POST["UserEmail"] = $_SESSION["User"]["UserEmail"];
						$_POST["UserCellphone"] = $_SESSION["User"]["UserCellphone"];
						
						
						$conn->close();
						redirectPOSTdata("MyProfile.php");
						exit();
					} 
					else {
						echo "Error updating record: " . $conn->error;
					}
					
				}
				else{
					$target_dir = "../img/Users/";
					$target_file = $target_dir . basename($_FILES["image"]["name"]);
					$uploadOk = 1;
					$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
					$fileName = $target_dir . $_SESSION["User"]["UserID"];
					$destination = $target_dir . $_SESSION["User"]["UserID"] .".". $imageFileType;
					
					

					// Check if image file is a actual image or fake image
					$check = getimagesize($_FILES["image"]["tmp_name"]);
					if($check !== false) {
						$uploadOk = 1;
					} 
					else {
						$_POST["alert"] = 
							'
								<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
								<strong>Image Upload Error!</strong> The selected file is not an image.
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
								<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
								<strong>Image Upload Error!</strong> The file you selected is too large.
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
								<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
								<strong>Image Upload Error!</strong> Invalid file extension.
							';
						$uploadOk = 0;
					}

					// Check if $uploadOk is set to 0 by an error
					if ($uploadOk == 0) {
						$_POST["MyProfile"] = "set";
						$conn->close();
						redirectPOSTdata("MyProfile.php");
						exit();
					// if everything is ok, try to upload file
					} 
					else {
						if (move_uploaded_file($_FILES["image"]["tmp_name"], $destination)) {
							
						} else {
							
						}
					}
					// Update the Item with the proper image path
					if(isset($destination)){
						$sql = "UPDATE users
						SET UserEmail = '".$_POST["UserEmail"]."', UserCellphone = '".$_POST["UserCellphone"]."' , UserPassword = '".$_POST["UserPassword"]."' , UserImageLocation = '".$destination."'
						WHERE UserID = ".$_SESSION["User"]["UserID"]."
							";
						
						if ($conn->query($sql) === TRUE) {
							// Update Session Data
							$_SESSION["User"]["UserImageLocation"] = $destination;
							$_SESSION["User"]["UserEmail"] = $_POST["UserEmail"];
							$_SESSION["User"]["UserCellphone"] = $_POST["UserCellphone"];
							$_SESSION["User"]["UserPassword"] = $_POST["UserPassword"];
							
							unset($_POST);
							$_POST["image"] = $destination;
							
							
							// Show confirmatory message
							$_POST["isSuccess"] = "set";
							$_POST["alert"] = 
								'
									<a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a>
									<strong>Profile Updated!</strong>
								';
							
							$_POST["MyProfile"] = "set";
							$_POST["UserEmail"] = $_SESSION["User"]["UserEmail"];
							$_POST["UserCellphone"] = $_SESSION["User"]["UserCellphone"];
							
							$conn->close();
							redirectPOSTdata("MyProfile.php");
							exit();
						} 
						else {
							echo "Error updating record: " . $conn->error;
						}
						
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
?> 