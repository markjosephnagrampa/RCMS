<?php
//include library
include('../library/tcpdf.php');

// reference variables
	$RestaurantName = "";
	if(!file_exists("../Restaurant_Name.txt")){
		$RestaurantName = 'Restaurant Chain';
	}
	else{
		$RestaurantName = file_get_contents("../Restaurant_Name.txt");
	}
	
	$title = $_POST["BranchAddress"]. " - Order Invoice";

//make TCPDF object
$pdf = new TCPDF('P','mm','A4');

//remove default header and footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

//add page
$pdf->AddPage();

//add content (student list)
//title
$pdf->SetFont('Helvetica','B',14);
$pdf->Cell(190,10,$RestaurantName,0,1,'C');

$pdf->Ln(2);

$pdf->SetFont('Helvetica','B',12);
$pdf->Cell(190,5,$title,0,1,'C');

$pdf->SetFont('Helvetica','',10);
$pdf->Ln();
$pdf->Ln(2);

//make the table
$html = "
	<b>Order Summary</b>
	<br><br>
	<table>
		<tr>
			<th>Order ID</th>
			<th>Date</th>
			<th>Time</th>
			<th>Addressee</th>
			<th>No. of Items</th>
			<th>Order Total</th>
		</tr>
		";
//load the json data
$data = json_decode($_POST["Summary"]);

//loop the data
foreach($data as $summary){	
	$html .= "
			<tr>
				<td>". $summary[0] ."</td>
				<td>". $summary[1] ."</td>
				<td>". $summary[2] ."</td>
				<td>". $summary[3] ."</td>
				<td>". $summary[4] ."</td>
				<td>". $summary[5] ."</td>
			</tr>
		
			";
}		

$html .= "
	</table>
	<br><br>
	<b>Order Details</b>
	<br><br>
	<table>
		<tr>
			<th>Item Name</th>
			<th>Qty</th>
			<th>Subtotal</th>
		</tr>
		";

//load the json data
$data = json_decode($_POST["Result"]);

//loop the data
foreach($data as $summary){	
	$html .= "
			<tr>
				<td>". $summary[0] ."</td>
				<td>". $summary[1] ."</td>
				<td>". $summary[2] ."</td>
			</tr>
		
			";
}		

$html .= "
	</table>
	<style>
	table {
		border-collapse:collapse;
	}
	th,td {
		border:1px solid #888;
	}
	table tr th {
		background-color:#888;
		color:#fff;
		font-weight:bold;
	}
	</style>
";
//WriteHTMLCell
$pdf->WriteHTMLCell(192,0,9,'',$html,0);	


//output
$pdf->Output();










