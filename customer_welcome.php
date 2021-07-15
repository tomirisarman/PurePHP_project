<?php

// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: customer.php");
    exit;
}
require_once "config.php";

$result=mysqli_query($link,
	'Select d.InvID, VIN, BrandName, ModelName, Body, Color, MName, DName, d.Price from
(Select InvID, Price, DName from dealers, inventories where inventories.DID=dealers.DID) as d
right join
(Select InvID, inventories.VIN, BrandName, ModelName, Body, Color, MName from  
	(select VIN, BrandName, ModelName, Body, Color, MName from models, vehicles, brands, manufacturers where vehicles.ModelID=models.ModelID and brands.BrandID=models.BrandID and manufacturers.MID=vehicles.MID) as a right join inventories on inventories.VIN=a.VIN) as m
on d.InvID=m.InvID where d.InvID not in (select InvID from sales);');

$item="";
$rad_err="";

if( isset( $_POST['add'] ) )
{
	$item=$_POST['add'];
	$sql='Insert into sales (InvID, CID) values (?, ?)';

    	if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ii", $param_InvID, $param_CID);

            $param_InvID=$item;
            $param_CID=$_SESSION["id"]; 

            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                mysqli_stmt_store_result($stmt);
                header("location: customer_purchases.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }        
    	}

    $updating=mysqli_query($link, "Update inventories set Status='purchased' where InvID=".$item."; ");

    mysqli_close($link);
}



?>

<!DOCTYPE html>
<html>
<head>
	<title>Customer Interface</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
</head>
<style type="text/css">
td{ padding: 20px;}
</style>
<body>
<br><br>
<a href="customer_logout.php">  Log Out.</a>
<center>
<h1 style="margin-top: 100px;">Welcome</h1>
<table border=2 >
	<tr class="text-primary">
		<td>Name</td>
		<td>Address</td>
		<td>Phone</td>
		<td>Gender</td>
	</tr>
	<div class="primary">
	<tr >
		<td><?php echo htmlspecialchars($_SESSION["name"]); ?></td>
		<td><?php echo htmlspecialchars($_SESSION["address"]); ?></td>
		<td><?php echo htmlspecialchars($_SESSION["phone"]); ?></td>
		<td><?php echo htmlspecialchars($_SESSION["gender"]); ?></td>
	</tr>
</div>
</table>
<br><br>
<a href="customer_purchases.php">Your purchases.</a>
<hr>
<h4><a href="customer_search.php">Search by brand and model.</a></h4>
<h4><a href="customer_search_price.php">Search by price.</a></h4>
</center>

<br><br>
<div class="row">

<div class="col-sm-6 col-sm-offset-1" style="text-align: center;">
<h1>The list of available vehicles</h1>
<h4>Choose the vehicle you want to buy</h4>
<form method="post">
<table border=2>

<tr>
<td>Choose</td>
<td>VIN</td>
<td>Brand</td>
<td>Model</td>
<td>Body</td>
<td>Color</td>
<td>Manufacturer</td>
<td>Dealer</td>
<td>Price</td>
</tr>

<?php
// выполнение SQL запроса и получение всех записей (строк) из таблицы `table_name`
while ($row=mysqli_fetch_array($result))
{ // вывод данных
  echo '

  <tr>
  	<td><input type="radio" name="add" value= '.$row['InvID'].' ></td>
  	<td>'.$row['VIN'].'</td>
	<td>'.$row['BrandName'].'</td>
	<td>'.$row['ModelName'].'</td>
	<td>'.$row['Body'].'</td>
	<td>'.$row['Color'].'</td>
	<td>'.$row['MName'].'</td>
	<td>'.$row['DName'].'</td>
	<td>'.$row['Price'].'</td>
  </tr>
';
}// /while
?>

</table>
<span class="help-block"><?php echo $rad_err; ?></span>
<input type="submit" class="btn btn-primary" value="Submit">
</form>
</div>



</body>
</html>