<?php

// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: customer.php");
    exit;
}
require_once "config.php";

$cname=$_SESSION["name"];
$result=mysqli_query($link, "Select InvID, CName,  DName, Price, Date, VIN, BrandName, ModelName, Body, Color, MName
from
(
Select sales.InvID, VIN, BrandName, ModelName, Body, Color, MName, DName, Price, sales.Date, sales.CID
from
	(Select d.InvID, VIN, BrandName, ModelName, Body, Color, MName, DName, d.Price from
		(Select InvID, Price, DName from dealers, inventories where inventories.DID=dealers.DID) as d right join
		(Select InvID, inventories.VIN, BrandName, ModelName, Body, Color, MName from 
			(select VIN, BrandName, ModelName, MName, Body, Color from models, vehicles, brands, manufacturers where
			vehicles.ModelID=models.ModelID and brands.BrandID=models.BrandID and manufacturers.MID=vehicles.MID) as a  right join inventories on inventories.VIN=a.VIN) 
		as m on d.InvID=m.InvID) 
	as x right join sales on sales.InvID=x.InvID
)
as z
left join customers on customers.CID=z.CID where CName='".$cname."';");

?>

<!DOCTYPE html>
<html>
<head>
<title>Purchases</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
<style type="text/css">
td{ padding: 20px;}
</style>
</head>
<body>
	
<h1>Your purchases</h1>
<p> Customer: <?php echo $_SESSION['name']?> </p>
<table border=2>

	<tr style="font-weight:bold">
	<td>Dealer</td>
	<td>Price</td>
	<td>Date</td>
	<td>VIN</td>
	<td>Brand</td>
	<td>Model</td>
	<td>Body</td>
	<td>Color</td>
	<td>Manufacturer</td>
	</tr>

<?php
// выполнение SQL запроса и получение всех записей (строк) из таблицы `table_name`
while ($row=mysqli_fetch_array($result))
{ // вывод данных
  echo '

  <tr>

  	<td bgcolor="#5DADE2">'.$row['DName'].'</td>
	<td bgcolor="#5DADE2">'.$row['Price'].'</td>
	<td bgcolor="#5DADE2">'.$row['Date'].'</td>
  	<td>'.$row['VIN'].'</td>
	<td>'.$row['BrandName'].'</td>
	<td>'.$row['ModelName'].'</td>
	<td>'.$row['Body'].'</td>
	<td>'.$row['Color'].'</td>
	<td>'.$row['MName'].'</td>

  </tr>
';
}// /while
?>
</table>
<a href="customer.php">Go back to your homepage</a>

</body>