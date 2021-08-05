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
			if(!isset($_POST["BranchInfo"])){
				header("Location: BranchInfo_action_page.php");
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>RCMS | Branch Info</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/myStyles.css">
		<link rel="icon" href="../img/favicon.ico">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	</head>
	<body>
		<form action = "BranchInfo_action_page.php" method = "post" enctype="multipart/form-data">
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
						<li class = "active"><a href="BranchInfo.php">Branch Info</a></li>
						<li><a href="ManageEmployees.php">Manage Employees</a></li>
						<li><a href="ManageOrders.php">Manage Orders</a></li>
						<li><a href="ManageExpenses.php">Manage Expenses</a></li>
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
				<div class="panel-heading"><span style = "font-weight: bold; font-size: 1.25em;">Net Income Summary</span></div>
				<div class="panel-body">
					<div id="piechart"></div>
				</div>
			</div>
			
			<div class="panel panel-default">
				<div class="panel-heading"><span style = "font-weight: bold; font-size: 1.25em;">Period Info</span></div>
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
											<input type="date" class="form-control" name = "from" id="from" 
												<?php
													if(isset($_POST["from"])){
														echo 'value = "'.$_POST["from"].'"';
													}
												?>
											>
										</div>
									</td>
									<td>
										<div class="form-group">
											<label for="to">To:</label>
										</div>
									</td>
									<td>
										<div class="form-group">
											<input type="date" class="form-control" name = "to" id="to"
												<?php
													if(isset($_POST["to"])){
														echo 'value = "'.$_POST["to"].'"';
													}
												?>
											>
										</div>
									</td>
									<td>
										<div class="form-group">
											<button type="submit" name = "search" value = "search" class="btn btn-lg btn-default"><span class = "glyphicon glyphicon-search"></span></button>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan = "6" style = "text-align: center; border-top: 0px;">
										<button type="submit" name = "printSummary" value = "printSummary" formtarget="_blank" class="btn btn-success"><span class = "glyphicon glyphicon-print"></span> Print Summary</button>
									</td>
								</tr>
							</thead>
						</table>
					</div>
					<div class = "table-responsive">
						<table class="table">
							<thead>
								<tr style = "font-size: 1.5em;">
									<th>Net Profit</th>
									<th>Total Sales</th>
									<th>Total Expenses</th>
									<th>Total Orders</th>
								</tr>
							</thead>
							<tbody style = "font-size: 1.25em;">
								<?php
									if(isset($_POST["Summary"]) && strlen($_POST["Summary"]) != 0){
										echo $_POST["Summary"];
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
				<div class="panel-heading"><span style = "font-weight: bold; font-size: 1.25em;">Orders</span></div>
				<div class = "panel-body">
					<div class = "table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Date</th>
									<th>Time</th>
									<th>Addressee</th>
									<th>No. of Items</th>
									<th>Order Total</th>
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
													<td colspan = "5" style = "text-align: center"><i>No results to show.</i></td>
												</tr>
											
											';
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-heading"><span style = "font-weight: bold; font-size: 1.25em;">Expenses</span></div>
				<div class = "panel-body">
					<div class = "table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Date</th>
									<th>Expense Name</th>
									<th>Amount</th>
								</tr>
							</thead>
							<tbody>
								<?php
									if(isset($_POST["Expenses"]) && strlen($_POST["Expenses"]) != 0){
										echo $_POST["Expenses"];
									}
									else{
										echo
											'
												<tr>
													<td colspan = "3" style = "text-align: center"><i>No results to show.</i></td>
												</tr>
											
											';
									}
								?>
							</tbody>
						</table>
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
		
		// Load google charts
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);
		
		// Draw the chart and set the chart values
		function drawChart() {
		 var data = google.visualization.arrayToDataTable([
		 ['Month', 'Net Income'],
		 <?php
			if(isset($_POST["chart"])){
				$data = json_decode($_POST["chart"]);
				
				echo 
					"
						['Jan', ".$data['0']."],
						['Feb', ".$data['1']."],
						['Mar', ".$data['2']."],
						['Apr', ".$data['3']."],
						['May', ".$data['4']."],
						['Jun', ".$data['5']."],
						['Jul', ".$data['6']."],
						['Aug', ".$data['7']."],
						['Sep', ".$data['8']."],
						['Oct', ".$data['9']."],
						['Nov', ".$data['10']."],
						['Dec', ".$data['11']."],
					"
				;
				/*
			 
			 */
			}
		 ?>
		]);
		
		 // Optional; add a title and set the width and height of the chart
		 var options = {'title':'Monthly Income', 'width':'80%', 'height':400};
		
		 // Display the chart inside the <div> element with id="piechart"
		 var chart = new google.visualization.LineChart(document.getElementById('piechart'));
		 chart.draw(data, options);
		}
		
		$(window).resize(function(){
		       drawChart();
		});
		
	</script>
</html>