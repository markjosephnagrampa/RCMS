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
		if($_SESSION["User"]["UserType"] != 2){
			header("Location: ../InvalidAccess.html");
		}

		else{
			if(!isset($_POST["ManageExpenses"])){
				header("Location: ManageExpenses_action_page.php");
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>RCMS | Manage Expenses</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/myStyles.css">
		<link rel="icon" href="../img/favicon.ico">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	</head>
	<body>
		<form action = "ManageExpenses_action_page.php" method = "post" enctype="multipart/form-data">
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
						<li><a href="BranchInfo.php">Branch Info</a></li>
						<li><a href="ManageEmployees.php">Manage Employees</a></li>
						<li><a href="ManageOrders.php">Manage Orders</a></li>
						<li class = "active"><a href="ManageExpenses.php">Manage Expenses</a></li>
						<li><a href="ManageMenu.php">Manage Menu</a></li>
						<li><a href="ManageTables.php">Manage Tables</a></li>
					</ul>
					<ul class="nav navbar-nav navbar-right" style = "text-align: center;">
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
								<?php 
									echo $_SESSION["User"]["UserName"]; 
								?> 
							<span class="caret"></span></a>
							<ul class="dropdown-menu" style = "text-align: center;">
								<li><a href="MyProfile.php"><span class = "glyphicon glyphicon-user"></span> My Profile</a></li>
								<li><a href="../SignOut.php"><span class = "glyphicon glyphicon-off"></span> Sign Out</a></li>
							</ul>
						<li><img <?php 
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
							if(isset($_SESSION["User"]["BranchAddress"])){
								echo $_SESSION["User"]["BranchAddress"];
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
				<div class="panel-heading"><span style = "font-weight: bold; font-size: 1.25em;">Expense List</span></div>
				<div class="panel-body">
					<div class = "table-responsive">
						<table class="table">
							<thead>
								<tr>
									<td>
										<div class="form-group">
											<label for="from">From:</label>
										</div>
									</td>
									<td>
										<div class="form-group">
											<input type="date" class="form-control" name = "from" id="from">
										</div>
									</td>
									<td>
										<div class="form-group">
											<label for="to">To:</label>
										</div>
									</td>
									<td>
										<div class="form-group">
											<input type="date" class="form-control" id="to" name = "to">
										</div>
									</td>
									<td>
										<div class="form-group">
											<button type="submit" name = "Search" value = "Search" class="btn btn-lg btn-default"><span class = "glyphicon glyphicon-search"></span></button>
										</div>
									</td>
								</tr>
							</thead>
						</table>
					</div>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Expense Name</th>
									<th>Date Incurred</th>
									<th>Amount</th>
									<th>Delete</th>
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
													<td colspan = "4" style = "text-align: center"><i>No results to show.</i></td>
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
				<div class="panel-heading"><span style = "font-weight: bold; font-size: 1.25em;">Add Expense</span></div>
				<div class="panel-body form-horizontal">
						<div class="form-group">
							<label class="control-label col-sm-2" for="ExpenseName"><span style = "color: red;">*</span> Expense Name:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="ExpenseName" name = "ExpenseName" placeholder="Enter expense">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="ExpenseDateIncurred"><span style = "color: red;">*</span> Date Incurred:</label>
							<div class="col-sm-10">
								<input type="date" class="form-control" id="ExpenseDateIncurred" name = "ExpenseDateIncurred" placeholder="Enter email">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="ExpenseAmount"><span style = "color: red;">*</span> Amount:</label>
							<div class="col-sm-10">
								<input type="number" step="any" class="form-control" id="ExpenseAmount" name = "ExpenseAmount" placeholder="Enter amount">
							</div>
						</div>
						<div class="form-group" style = "text-align: center;">
							<button type="submit" name = "Add_Expense" value = "Add_Expense" class="btn btn-danger btn-lg">Add Expense</button>
						</div>
				</div>
			</div>
		</div>
		</form>
	</body>
	<script type = "text/javascript">
		var loadFile = function(event) {
			var image = document.getElementById('output');
			image.src = URL.createObjectURL(event.target.files[0]);
		};
		function hideAlert(){
			$("#alert").fadeOut();
		}
		
	</script>
</html>