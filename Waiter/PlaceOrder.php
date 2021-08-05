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
		<title>RCMS | Place Order</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/myStyles.css">
		<link rel="icon" href="../img/favicon.ico">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	</head>
	<body>
		<form onsubmit = "return false" action = "PlaceOrder_action_page.php" method = "post" enctype="multipart/form-data">
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
						<li class = "active"><a href="PlaceOrder.php">Place Order</a></li>
						<li><a href="SeatCustomers.php">Seat Customers</a></li>
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
		<div class="container-fluid">
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
			<div class="row">
				<div class = "col-sm-1">
					&nbsp;
				</div>
				<div id = "alertResult" class = "col-sm-10">
				</div>
				<div class = "col-sm-1">
					&nbsp;
				</div>
			</div>
			
			<span style = "font-size: 1.5em";>&nbsp;</span>
				<div class="row">
					<div class = "col-sm-1">
						&nbsp;
					</div>
					<div class="col-sm-2 panel panel-default">
						<div class="panel-heading"><span style = "font-weight: bold; font-size: 1.5em;">Add Item</span></div>
						<div class="panel-body">
							<div class="form-group">
								<label for="ItemName">Item Name:</label>
								<select class="form-control" name = "ItemName" id="ItemName">
									<option selected disabled>Select Item</option>
								</select>
							</div>
							<div class="form-group">
								<label for="Quantity">Quantity:</label>
								<input type = "number" name = "Quantity" min = "1" class ="form-control" id = "Quantity">
							</div>
							<div class="form-group" style = "text-align: center;">
								<button type="submit" name = "Add_to_Cart" id = "Add_to_Cart" value = "Add_to_Cart" class="btn btn-success btn-lg">Add</button>
							</div>
						</div>
					</div>
					<div class = "col-sm-1">
						&nbsp;
					</div>
					<div class="col-sm-7 panel panel-default">
						<div class="panel-heading"><span style = "font-weight: bold; font-size: 1.5em;">Order Info</span></div>
						<div class="panel-body">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Item Name</th>
										<th>Image</th>
										<th>Qty</th>
										<th>Subtotal</th>
										<th>Add</th>
										<th>Lessen</th>
										<th>Remove</th>
									</tr>
								</thead>
								<tbody id = "Order_Info">
								</tbody>
								<tfoot>
									<tr class = "active">
										<td colspan = "2"><span id = "cartTotal" style = "font-weight: bold; font-size: 1.5em;">Total: Php 0.00</span></td>
										<td colspan = "2">
											<select class="form-control" name = "Select_Table" id="Select_Table">
												<option selected disabled>Select Table</option>
											</select>
										</td>
										<td colspan = "2">
											<input type="text" class="form-control" id="OrderAddressee" name = "OrderAddressee" placeholder="Enter Addressee Name" disabled>
										</td>
										<td colspan = "1" style = "text-align: center;"><button type="submit" id = "Place_Order" name = "Place_Order" value = "Place_Order" class="btn btn-danger btn-lg">Place Order</button></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class = "col-sm-1">
					&nbsp;
				</div>
		</div>
		</form>
	</body>
	<script>
		var itemInfo = [];
		var cart = [];
		var cartTotal = 0;
		
		var loadFile = function(event) {
			var image = document.getElementById('output');
			image.src = URL.createObjectURL(event.target.files[0]);
		};
		function hideAlert(){
			$("#alert").fadeOut();
		}
		
		// Enable Addressee Textbox
		$("#Select_Table").change(function(){
		  if($("#Select_Table").val() == "TakeOut"){
			  $("#OrderAddressee").prop("disabled", false );
		  }
		  else{
			  $("#OrderAddressee").prop("disabled", true );
		  }
		});
		
		// Add to Cart
		$('#Add_to_Cart').click(function()
		{
			// Null Check
			if($('#Quantity').val().length == 0 || $('#ItemName').val() == null || $('#Quantity').val() <= 0){
				$('#alertResult').html('<div id = "alert" class="alert alert-danger fade in"><a href="#" class="close" onClick="hideAlert();" aria-label="close">&times;</a><strong>Required Fields Blank!</strong> Please fill out all required data.</div>');
			}
			
			else{
										
				// Get itemInfo index of selected
				var i = 0;
				for (len = itemInfo.length; i < len; i++) {
				  if(itemInfo[i][0] == $("#ItemName").val()){
					  break;
				  }
				}
				
				// Add that itemInfo with Quantity and subtotal to cart
				cart.push(itemInfo[i].concat([$("#Quantity").val(),$("#Quantity").val() * itemInfo[i][3]]));
				cartTotal += parseFloat($("#Quantity").val() * itemInfo[i][3]);
				
				// Add new cart item to display
				$("#Order_Info").append('<tr id = "cartItem'+itemInfo[i][0]+'"><td>'+itemInfo[i][1]+'</td><td><img src = "'+itemInfo[i][2]+'" class="img-circle small-image"></td><td id = "qty'+itemInfo[i][0]+'">'+$("#Quantity").val()+'</td><td id = "subtotal'+itemInfo[i][0]+'">Php '+($("#Quantity").val() * itemInfo[i][3]).toFixed(2)+'</td><td><button onclick="plus('+itemInfo[i][0]+')" id = "plus'+itemInfo[i][0]+'" class = "btn btn-default"><span class = "glyphicon glyphicon-plus"></span></button></td><td><button onclick="minus('+itemInfo[i][0]+')" id = "minus'+itemInfo[i][0]+'" class = "btn btn-default"><span class = "glyphicon glyphicon-minus"></span></button></td><td><button onclick="remove('+itemInfo[i][0]+')" id = "remove'+itemInfo[i][0]+'" class = "btn btn-default"><span class = "glyphicon glyphicon-remove"></span></button></td></tr>');
				$("#cartTotal").html("Total: Php " + cartTotal.toFixed(2));
			}
		});
		
		// Place Order
		$('#Place_Order').click(function()
		{
			var formData = new FormData();
			formData.append("Place_Order", "set");
			formData.append("cart",JSON.stringify(cart));
			formData.append("Select_Table", $('#Select_Table').val());
			formData.append("OrderAddressee", $('#OrderAddressee').val());
			formData.append("OrderTotal", cartTotal);
			
			$.ajax({
				type: "POST",
				url: "PlaceOrder_action_page.php",
				data: formData,             
				cache: false,
				contentType: false,
				processData: false,
				success: function(data)
				{
					console.log(data);
					var obj = JSON.parse(data);
					
					if(obj["isSuccess"] == "true"){
						
						$("#Quantity").html("");
						$("#Order_Info").html("");
						$("#cartTotal").html("Total: Php 0.00");
						$("#OrderAddressee").val("");
						$("#Quantity").val("");
						
						cartTotal = 0;
						cart = [];
						
						$("#alertResult").html(obj["alert"]);
						
					}
					else{
						$("#alertResult").html(obj["alert"]);
					}
					
				}
			});
		});
		
		function plus(id){
			// Get cart index of selected
			var i = 0;
			for (len = cart.length; i < len; i++) {
			  if(cart[i][0] == id){
				  break;
			  }
			}
			
			// Update cart variable
			cart[i][4]++;
			cart[i][5]+= parseFloat(cart[i][3]);
			cartTotal += parseFloat(cart[i][3]);
			
			// Update display
			$("#qty"+id).html(cart[i][4]);
			$("#subtotal"+id).html("Php "+cart[i][5].toFixed(2));
			$("#cartTotal").html("Total: Php " + cartTotal.toFixed(2));
		}
		
		function minus(id){
			// Get cart index of selected
			var i = 0;
			for (len = cart.length; i < len; i++) {
			  if(cart[i][0] == id){
				  break;
			  }
			}
			
			if(cart[i][4] == 1){
				remove(id);
			}
			else{
				// Update cart variable
				cart[i][4]--;
				cart[i][5]-= parseFloat(cart[i][3]);
				cartTotal-= parseFloat(cart[i][3]);
				
				// Update display
				$("#qty"+id).html(cart[i][4]);
				$("#subtotal"+id).html("Php "+cart[i][5].toFixed(2));
				$("#cartTotal").html("Total: Php " + cartTotal.toFixed(2));
			}
		}
		function remove(id){
			// Get cart index of selected
			var i = 0;
			for (len = cart.length; i < len; i++) {
			  if(cart[i][0] == id){
				  break;
			  }
			}
			// Remove display
			$("#cartItem"+id).remove();
			
			// Update cart
			cartTotal -= cart[i][5];
			$("#cartTotal").html("Total: Php " + cartTotal.toFixed(2));
			cart.splice(i, 1);
			
			// Recheck empty cart
			if(cart.length == 0){
				cartTotal = 0;
				$("#cartTotal").html("Total: Php " + cartTotal.toFixed(2));
			}
		}
		function addOption(item, index) {
		  $("#ItemName").append("<option value = "+item[0]+">"+item[1]+"</option>");
		}
		function addOption2(item, index) {
		  $("#Select_Table").append("<option value = "+item+">"+item+"</option>");
		}
		
		$(document).ready(function() {
			// Query all available menu items
			var formData = new FormData();
			formData.append("QueryItems", "set");
			
			$.ajax({
				type: "POST",
				url: "PlaceOrder_action_page.php",
				data: formData,             
				cache: false,
				contentType: false,
				processData: false,
				success: function(data)
				{
					console.log(data);
					var obj = JSON.parse(data);
					
					// Load all menu items in select
					
					$("#ItemName").html("<option selected disabled>Select Item</option>");
					obj["items"].forEach(addOption);
					
					// Load all menu item info
					itemInfo = obj["itemInfo"];
					
					// Load all occupied tables
					$("#Select_Table").html("<option selected disabled>Select Table</option>");
					obj["tables"].forEach(addOption2);
					$("#Select_Table").append("<option value = 'TakeOut'>Take Out</option>");
				}
			});
		});
		
		
		
	</script>
</html>