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
			if(!isset($_POST["ManageBranches"])){
				header("Location: ManageBranches_action_page.php");
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>RCMS | Manage Branches</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/myStyles.css">
		<link rel="icon" href="../img/favicon.ico">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	</head>
	<body>
		<form action = "ManageBranches_action_page.php" method = "post" enctype="multipart/form-data">
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
						<li class = "active"><a href="ManageBranches.php">Manage Branches</a></li>
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
			<div class = "row">
				<div class = "col-sm-12" style = "text-align: center; margin-bottom: 1em;">
					<span style = "font-weight: bold; font-size: 1.5em;">
						<?php
							if(!file_exists("../Restaurant_Name.txt")){
								echo 'Restaurant Chain';
							}
							else{
								echo file_get_contents("../Restaurant_Name.txt");
							}
						?>
					</span>
				</div>
			</div>
			
			<?php
				if(isset($_POST["alert"])){
					echo $_POST["alert"];
				}
			?>
			
			<div class="panel panel-default">
				<div class="panel-heading"><span style = "font-weight: bold; font-size: 1.25em;">Branch List</span></div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Address</th>
									<th>Branch Contact No.</th>
									<th>Manager</th>
									<th>Email</th>
									<th>Cellphone</th>
									<th>Branch Status</th>
									<th>Update</th>
								</tr>
							</thead>
							<tbody>
								<?php
									if(isset($_POST["Result"]) && strlen($_POST["Result"]) != 0){
										echo $_POST["Result"];
									}
									else{
										echo
											'
												<tr>
													<td colspan = "7" style = "text-align: center"><i>No results to show.</i></td>
												</tr>
											
											';
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
			<div class="panel panel-default">
				<div class="panel-heading"><span style = "font-weight: bold; font-size: 1.25em;">Add New Branch</span></div>
				<div class="panel-body form-horizontal">
						<div class = "form-group">
							<div class = "col-sm-offset-5 col-sm-7"><img id = "output" src = "../img/user.jpg" class="img-responsive img-circle" style = "max-height: 200px; max-width: 200px;"></div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="BranchAddress"><span style = "color: red;">*</span> Branch Address:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name = "BranchAddress" id="BranchAddress" placeholder="Enter branch name">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="BranchContactNo"><span style = "color: red;">*</span> Branch Contact No:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name = "BranchContactNo" id="BranchContactNo" placeholder="Enter branch contact no">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="UserName"><span style = "color: red;">*</span> Branch Manager:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name = "UserName" id="UserName" placeholder="Enter branch manager name">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="UserEmail"><span style = "color: red;">*</span> Email:</label>
							<div class="col-sm-10">
								<input type="email" class="form-control" name = "UserEmail" id="UserEmail" placeholder="Enter email">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="UserCellphone">Cellphone:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name = "UserCellphone" id="UserCellphone" placeholder="Enter cellphone">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="UserPassword"><span style = "color: red;">*</span> Password:</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" name = "UserPassword" id="UserPassword" placeholder="Enter password">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="cUserPassword"><span style = "color: red;">*</span> Confirm Password:</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" name = "cUserPassword" id="cUserPassword" placeholder="Enter password">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="pwd">Image:</label>
							<div class="col-sm-10">
								<input class = "form-control" type="file" accept="image/*" name="image" id="file"  onchange="loadFile(event)">
							</div>
						</div>
						<div class="form-group" style = "text-align: center;">
							<button type="submit" name = "Add_Branch" value = "Add_Branch" class="btn btn-default btn-lg">Add Branch</button>
						</div>
				</div>
			</div>
		</div>
		</form>
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