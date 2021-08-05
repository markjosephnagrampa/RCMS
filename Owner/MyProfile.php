<?php
	session_start();
	date_default_timezone_set('Etc/GMT-8'); // Set time zone to Philippine time
    // 1. Check if the user has access to this page
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
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>RCMS | My Profile</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/myStyles.css">
		<link rel="icon" href="../img/favicon.ico">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	</head>
	<body>
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>                        
					</button>
					<a class="navbar-brand" href="#" style = "font-size: 1.5em; font-weight: bold;">Restaurant Chain Management System</a>
				</div>
				<div class="collapse navbar-collapse" id="myNavbar">
					<ul class="nav navbar-nav" style = "text-align: center;">
						<li><a href="ChainInfo.php">Chain Info</a></li>
						<li><a href="ManageMenu.php">Manage Menu</a></li>
						<li><a href="ManageBranches.php">Manage Branches</a></li>
					</ul>
					<ul class="nav navbar-nav navbar-right" style = "text-align: center;">
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
								<?php 
									echo $_SESSION["User"]["UserName"]; 
								?> 
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu" style = "text-align: center;">
								<li><a href="MyProfile.php"><span class = "glyphicon glyphicon-user"></span> My Profile</a></li>
								<li><a href="../SignOut.php"><span class = "glyphicon glyphicon-off"></span> Sign Out</a></li>
							</ul>
						<li>
							<img 
								<?php 
									if(file_exists($_SESSION["User"]["UserImageLocation"])){ 
										echo 'src = "'.$_SESSION["User"]["UserImageLocation"].'"';
									}
									else{ 
										echo 'src = "https://via.placeholder.com/32"'; 
									}
								?>
							class="img-circle user-avatar"></li>
						</li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="container">
			<div id = "alert" class="alert
				<?php
					if(isset($_POST["isSuccess"])){
						echo 'alert-success';
					}
					else{
						echo 'alert-danger';
					}
				?>
				fade in" 
				<?php
					if(isset($_POST["alert"])){
						echo 'style = "display: block;"';
					}	
					else{
						echo 'style = "display: none;"';
					}
				?>>
				<?php
					if(isset($_POST["alert"]))
						echo $_POST["alert"];
				?>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading"><span style = "font-weight: bold; font-size: 1.25em;">My Profile </span></div>
				<div class="panel-body">
					<form action = "MyProfile_action_page.php" method="post" enctype="multipart/form-data" class="form-horizontal">
						<div class = "form-group">
							<div class = "col-sm-offset-4 col-sm-8">
								<img id = "output" 
									<?php
										if(file_exists($_SESSION["User"]["UserImageLocation"])){ 
											echo 'src = "'.$_SESSION["User"]["UserImageLocation"].'"';
										}
										else{ 
											echo 'src = "https://via.placeholder.com/400"'; 
										}
									?>
								class="img-responsive img-circle" style = "max-height: 400px; max-width: 400px;">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="email">Position:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="email" placeholder="Chain Owner" disabled>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="email">Name:</label>
							<div class="col-sm-10">
								<input type="text" name = "UserName" id="UserName" class="form-control" 
									<?php
										echo 'placeholder = "'.$_SESSION["User"]["UserName"].'"';
									?>
								disabled>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="email"><span style = "color: red;">*</span> Email:</label>
							<div class="col-sm-10">
								<input type="email" name = "UserEmail" id="UserEmail" class="form-control"  placeholder="Enter email" 
									<?php
										echo 'value = "'.$_SESSION["User"]["UserEmail"].'"';
									?>
								>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="UserCellphone">Cellphone:</label>
							<div class="col-sm-10">
								<input type="text" name = "UserCellphone" id="UserCellphone" class="form-control"  
									<?php
										echo 'value = "'.$_SESSION["User"]["UserCellphone"].'"';
									?>
								>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="pwd"><span style = "color: red;">*</span> Password:</label>
							<div class="col-sm-10">
								<input type="password" name = "UserPassword" id = "UserPassword" class="form-control" placeholder="Enter password">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="pwd"><span style = "color: red;">*</span> Confirm Password:</label>
							<div class="col-sm-10">
								<input type="password" name = "cUserPassword" id = "cUserPassword" class="form-control" placeholder="Enter password">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="pwd">Image:</label>
							<div class="col-sm-10">
								<input class = "form-control" type="file" accept="image/*" name="image" id="file"  onchange="loadFile(event)">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12" style = "text-align: center;">
								<button type="submit" name = "Update" value = "Update" class="btn btn-success" style = "font-size: 1.25em;">Update</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
	<script>
		var loadFile = function(event) {
			var image = document.getElementById('output');
			image.src = URL.createObjectURL(event.target.files[0]);
		};
		function hideAlert(){
			$("#alert").fadeOut();
		}
	</script>
</html>