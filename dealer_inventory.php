<?php

session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["d_loggedin"]) || $_SESSION["d_loggedin"] !== true){
    header("location: dealer.php");
    exit;
}
require_once "config.php";

$did=$_SESSION["d_id"];

$sql = "Select DID, inventories.VIN, BrandName, ModelName, Body, Color, MName, Price from inventories right join (SELECT VIN, BrandName, ModelName, Body, Color, MName FROM vehicles, brands, models, manufacturers
where models.BrandID=brands.BrandID and models.ModelID = vehicles.ModelID and manufacturers.MID=vehicles.MID) as a on inventories.VIN=a.VIN where DID=? and InvID NOT IN (select InvID from sales)";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_did);
            
            // Set parameters
            $param_did = $did;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                
                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt) != 0){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $did, $VIN, $brand, $model, $body, $color, $manufacturer, $price);
                 
                } else{
                    // Display an error message if username doesn't exist
                    echo 'Nothing Found.';
                }

            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
           }
?>

<!DOCTYPE html>
<html>
<head>
<title>Inventory</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
<style type="text/css">
td{ padding: 20px;}
</style>
</head>
<body>
	
<h1 align=center>Your inventory</h1>
<p> Dealer: <?php echo $_SESSION['d_name']?> </p>
<h2>Available vehicles</h2>
<table border=2>

	<tr style="font-weight:bold">
	<td>VIN</td>
	<td>Brand</td>
	<td>Model</td>
  <td>Body</td>
	<td>Color</td>
	<td>Manufacturer</td>
	<td>Price</td>
	</tr>

<?php

while (mysqli_stmt_fetch($stmt))
{ 
  echo '

  <tr>
  	<td>'.$VIN.'</td>
	<td>'.$brand.'</td>
	<td>'.$model.'</td>
    <td>'.$body.'</td>
	<td>'.$color.'</td>
	<td>'.$manufacturer.'</td>
	<td bgcolor="#5DADE2">'.$price.'</td>

  </tr>
';
}
?>
</table>

<h2>Already sold</h2>
<table border=2>

	<tr style="font-weight:bold">
	<td>Customer</td>
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
$dname=$_SESSION["d_name"];

$sql = "Select CName,  Price, Date, VIN, BrandName, ModelName, Body, Color, MName
from
(
Select sales.InvID, VIN, BrandName, ModelName, Body, Color, MName, DName, Price, sales.Date, sales.CID
from
	(Select d.InvID, VIN, BrandName, ModelName,Body,Color, MName, DName, d.Price from
		(Select InvID, Price, DName from dealers, inventories where inventories.DID=dealers.DID) as d right join
		(Select InvID, inventories.VIN, BrandName, ModelName, Body,Color, MName from 
			(select VIN, BrandName, ModelName, MName, Body,Color from models, vehicles, brands, manufacturers where
			vehicles.ModelID=models.ModelID and brands.BrandID=models.BrandID and manufacturers.MID=vehicles.MID) as a  right join inventories on inventories.VIN=a.VIN) 
		as m on d.InvID=m.InvID) 
	as x right join sales on sales.InvID=x.InvID
)
as z
left join customers on customers.CID=z.CID where DName=?";
        
if($stmt = mysqli_prepare($link, $sql)){
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "s", $param_dname);
    
    // Set parameters
    $param_dname = $dname;
    
    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)){
        
        mysqli_stmt_store_result($stmt);

        if(mysqli_stmt_num_rows($stmt) != 0){                    
            // Bind result variables
            mysqli_stmt_bind_result($stmt, $CName, $price, $date, $VIN, $brand, $model, $body, $color, $manufacturer);
         
        } else{
            // Display an error message if username doesn't exist
            echo 'Nothing Found.';
        }

    } else{
        echo "Oops! Something went wrong. Please try again later.";
    }
   }

while (mysqli_stmt_fetch($stmt))
{ 
  echo '

  <tr>

  	<td bgcolor="#5DADE2">'.$CName.'</td>
	<td bgcolor="#5DADE2">'.$price.'</td>
	<td bgcolor="#5DADE2">'.$date.'</td>
  	<td>'.$VIN.'</td>
	<td>'.$brand.'</td>
	<td>'.$model.'</td>
  <td>'.$body.'</td>
	<td>'.$color.'</td>
	<td>'.$manufacturer.'</td>


  </tr>
';
}

?>
</table>
<a href="dealer_welcome.php">Go back to your homepage</a>

</body>