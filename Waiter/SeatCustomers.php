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
		if($_SESSION["User"]["UserType"] != 3){
			header("Location: ../InvalidAccess.html");
		}

		else{
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>RCMS | Seat Customers</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/myStyles.css">
		<link rel="icon" href="../img/favicon.ico">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	</head>
	<body>
		<form onsubmit = "return false" action = "SeatCustomers_action_page.php" method = "post" enctype="multipart/form-data">
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
						<li><a href="TableStatus.php">Table Status</a></li>
						<li><a href="PlaceOrder.php">Place Order</a></li>
						<li class = "active"><a href="SeatCustomers.php">Seat Customers</a></li>
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
						<li><img
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
							if(isset($_SESSION["User"]["BranchAddress"])){
								echo $_SESSION["User"]["BranchAddress"];
							}
						?>
					</span>
				</div>
			</div>
			
			<div id = "alertResult">
			</div>
			
			<span style = "font-size: 2em";>&nbsp;</span>
			<div class = "row">
				<div class="col-sm-4">
					&nbsp;
				</div>
				<div class="col-sm-4 panel panel-default">
					<div class="panel-heading"><span style = "font-weight: bold; font-size: 1.25em;"> New Customers </span></div>
					<div class="panel-body">
							<div class="form-group">
								<label for="NumberOfCustomers">No of customers:</label>
								<input type="number" class="form-control" id="NumberOfCustomers" name = "NumberOfCustomers" min = "1">
							</div>
							<div class="form-group">
								<label for="table">Select Table:</label>
								<select class="form-control" id="TableID" name = "TableID">
									<option selected disabled>Select Table</option>
								</select>
							</div>
							<div class="form-group">
								<label for="TableAddressee">Addressee:</label>
								<input type="text" class="form-control" id="TableAddressee" name = "TableAddressee">
							</div>
							<div class="form-group" style = "text-align: center;">
								<button type="submit" name = "Seat_Customers" id = "Seat_Customers" value = "Seat_Customers" class="btn btn-success btn-lg">Seat Customers</button>
							</div>
					</div>
				</div>
				<div class="col-sm-4">
					&nbsp;
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
		$('#NumberOfCustomers').change(function()
		{
			var formData = new FormData();
			formData.append("NumberOfCustomers", $('#NumberOfCustomers').val());
			
			$.ajax({
				type: "POST",
				url: "SeatCustomers_action_page.php",
				data: formData,             
				cache: false,
				contentType: false,
				processData: false,
				success: function(data)
				{
					var obj = JSON.parse(data);
					if(obj["isFilterAction"] == "true"){
						$("#TableID").html("<option selected disabled>Select Table</option>");
						obj["seats"].forEach(addOption);
					}
				}
			});
		});
		
		$('#Seat_Customers').click(function()
		{
			var formData = new FormData();
			formData.append("TableAddressee", $('#TableAddressee').val());
			formData.append("TableID", $('#TableID').val());
			$.ajax({
				type: "POST",
				url: "SeatCustomers_action_page.php",
				data: formData,             
				cache: false,
				contentType: false,
				processData: false,
				success: function(data)
				{
					var obj = JSON.parse(data);
					
					if(obj["isSeatedAction"] == "true"){
						if(obj["isSuccess"] == "true"){
							$("#TableAddressee").val("");
							$("#NumberOfCustomers").val("");
							$("#TableID").html("<option selected disabled>Select Table</option>");
						}
						$("#alertResult").html(obj["alert"]);
					}
				}
			});
		});
		

		function addOption(item, index) {
		  $("#TableID").append("<option value = "+item+">"+item+"</option>");
		}
	</script>
</html>