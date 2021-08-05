<!DOCTYPE html>
<html lang="en">
	<head>
		<title>RCMS | Login</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/myStyles.css">
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Redressed&display=swap" rel="stylesheet">
		<link rel="icon" href="img/favicon.ico">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	</head>
	<body>
		<div class="container">
			<span style = "font-size: 2em;">&nbsp;</span>
			
			<div id = "alert" class="alert alert-danger fade in" style = "display: none;">
			
			</div>
			
			<span style = "font-size: 1em;">&nbsp;</span>
			
			<div class="panel panel-default">
				<div class="panel-heading" style = "text-align: center;"><span style = "font-weight: bold; font-size: 2em;">Restaurant Chain Management System</span></div>
				<div class="panel-body">
					<form onsubmit="login();return false" id="LoginForm" class="form-horizontal">
						<div class = "form-group">
							<div class = "col-sm-12"><img id = "output" src = "img/login3.gif" class="img-responsive img-circle" style = "margin: auto; max-height: 400px; max-width: 400px;"></div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="email">Email:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="UserEmail" name = "UserEmail" placeholder="test@gmail.com">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="pwd">Password:</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" id="UserPassword" name = "UserPassword" placeholder="Enter password">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12" style = "text-align: center;">
								<button type="submit" class="btn btn-default" style = "font-size: 1.5em;">Log In</button>
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
	
	function login() {
	  var xhttp = new XMLHttpRequest();
	  xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			
			if(this.responseText == 1){
				window.location.href = "Owner/MyProfile.php";
			}
			
			else if(this.responseText == 2){
				window.location.href = "BranchManager/MyProfile.php";
			}
			
			else if(this.responseText == 3){
				window.location.href = "Waiter/MyProfile.php";
			}
			
			else{
			$("#alert").fadeIn();
		  document.getElementById("alert").innerHTML = this.responseText;
			}
		}
	  };
	  xhttp.open("POST", "LogIn_action_page.php", true);
	  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	   var un = document.getElementById("UserEmail").value;
    var pw = document.getElementById("UserPassword").value;
		xhttp.send("UserEmail="+un+"&UserPassword="+pw);
	}
	
	function hideAlert(){
		$("#alert").fadeOut();
	}
	
	</script>
</html>