<?php

// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["d_loggedin"]) || $_SESSION["d_loggedin"] !== true){
    header("location: dealer.php");
    exit;
}
require_once "config.php";

$result=mysqli_query($link,
	'SELECT VIN, BrandName, ModelName, Body, Color, MName FROM vehicles, brands, models, manufacturers
where models.BrandID=brands.BrandID and models.ModelID = vehicles.ModelID and manufacturers.MID=vehicles.MID and vehicles.VIN NOT IN (select VIN from inventories);');

$vin=$price='';
$sel_err=$pr_err='';

if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(!isset($_POST["vin"])){
        $sel_err = "Please choose a vehicle.";
    }
    else{
        $vin=$_POST["vin"];
    }

    if(empty($_POST["price"])){
        $pr_err = "Please assign a price.";
    }
    else{
        $price=$_POST["price"];
    }

    if( empty($sel_err) && empty($pr_err) )
    {
    	//$vin=$_POST['vin'];
        //$price=$_POST['price'];

    	$sql='Insert into inventories (DID, VIN, Price) values (?, ?, ?);';

        	if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "iii", $param_DID, $param_VIN, $param_price);

                $param_DID=$_SESSION["d_id"];
                $param_VIN=$vin;
                $param_price=$price ;

                if(mysqli_stmt_execute($stmt)){
                    // Redirect to login page
                    mysqli_stmt_store_result($stmt);
                    header("location: dealer_inventory.php");
                } else{
                    echo "Something went wrong. Please try again later.";
                }        
        	}
            mysqli_stmt_close($stmt);
    }

   // mysqli_close($link);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dealer Interface</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
</head>
<style type="text/css">
td{ padding: 20px;}
</style>
<body>
<br><br>
<a href="customer_logout.php">  Log Out.</a>
<center>
<h1 style="margin-top: 100px;">Welcome, <?php echo htmlspecialchars($_SESSION["d_name"]); ?></h1>

<br><br>
<a href="dealer_inventory.php">Your inventory.</a>
<hr>
<h4><a href="dealer_search.php">Smart search by brand and model.</a></h4>
</center>

<br><br>
<div class="row">

<div class="col-sm-6 col-sm-offset-1" style="text-align: center;">
<h1>The list of available vehicles</h1>
<h4>Choose the vehicle you want to add to the inventory.</h4>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
<div class="form-group <?php echo (!empty($sel_err)) ? 'has-error' : ''; ?>">
<table border=2>

<tr>
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
    <td><input type="radio" name="vin" value=" '.$row['VIN'].'" >'.$row['VIN'].'</td>
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
<span class="help-block"><?php echo $sel_err; ?></span>
</div>
<h4>Assign a price.</h4>
    <div class="form-group <?php echo (!empty($pr_err)) ? 'has-error' : ''; ?>">
        <input type="number" name="price" class="form-control" value="<?php echo $price; ?>">
        <span class="help-block"><?php echo $pr_err; ?></span>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
    </div>

</form>
</div>
</div>
</body>
</html>