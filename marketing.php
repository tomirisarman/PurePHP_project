<?php


require_once "config.php";

$result=mysqli_query($link,
	'Select InvID, CName,  DName, Price, Date, VIN, BrandName, ModelName, Body, Color, MName
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
left join customers on customers.CID=z.CID;');

?>

<!DOCTYPE html>
<html>
<head>
	<title>Marketing Interface</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
</head>
<style type="text/css">
td{ padding: 20px;}
</style>
<body>

<div class="row" style="margin-top: 100px; text-align: center">
	<h1>"General Motors" marketing department</h1>
</div>


<div class="row">

<div class="col-sm-offset-1" style="text-align: center;">
<h3>Sales</h3>
<table border=2>

<tr class="bg-primary">
<td>VIN</td>
<td>Brand</td>
<td>Model</td>
<td>Body</td>
<td>Color</td>
<td>Manufacturer</td>
<td>Dealer</td>
<td>Customer</td>
<td>Price</td>
<td>Date</td>
</tr>

<?php
// выполнение SQL запроса и получение всех записей (строк) из таблицы `table_name`
while ($row=mysqli_fetch_array($result))
{ // вывод данных
  echo '

  <tr>
  	<td>'.$row['VIN'].'</td>
	<td>'.$row['BrandName'].'</td>
	<td>'.$row['ModelName'].'</td>
	<td>'.$row['Body'].'</td>
	<td>'.$row['Color'].'</td>
	<td>'.$row['MName'].'</td>
	<td>'.$row['DName'].'</td>
	<td>'.$row['CName'].'</td>
	<td>'.$row['Price'].'</td>
	<td>'.$row['Date'].'</td>
  </tr>
';
}// /while
?>

</table>
</div>
</div>

<br><br>
<div class="row">
<div class="col-sm-3 col-sm-offset-2">

<table border=2>
	<h4>The most popular brand</h4>	
	<tr class="bg-primary">
		<td>Brand</td>
		<td>Number of purchases</td>
	</tr>

<?php

$result=mysqli_query($link, 'SELECT BrandName, count(*)
FROM (SELECT BrandName FROM sales, inventories, vehicles, models, brands where
sales.InvID=inventories.InvID and inventories.VIN=vehicles.VIN and vehicles.ModelID=models.ModelID and models.BrandID=brands.BrandID) a
GROUP BY BrandName
HAVING count(*) > 1
ORDER BY count(*) DESC LIMIT 1;');

while ($row=mysqli_fetch_array($result))
{ // вывод данных
  echo '

  <tr>
	<td><b>'.$row['BrandName'].'<b></td>
	<td>'.$row['count(*)'].'</td>
  </tr>
';
}// /while
?>

</table>

</div>

<div class="col-sm-3 col-sm-offset-2">

<table border=2>
	<h4>TOP-3 the most profitable brands</h4>	
	<tr class="bg-primary">
		<td>Brand</td>
		<td>Total profit</td>
	</tr>

<?php

$result=mysqli_query($link, 'SELECT BrandName, sum(Price)
FROM (SELECT BrandName, Price FROM sales, inventories, vehicles, models, brands where
sales.InvID=inventories.InvID and inventories.VIN=vehicles.VIN and vehicles.ModelID=models.ModelID and models.BrandID=brands.BrandID) a
GROUP BY BrandName Order by sum(Price) desc limit 3;');

while ($row=mysqli_fetch_array($result))
{ // вывод данных
  echo '

  <tr>
	<td><b>'.$row['BrandName'].'</b></td>
	<td>'.$row['sum(Price)'].'</td>
  </tr>
';
}// /while
?>

</table>
</div>
</div>

</body>
</html>