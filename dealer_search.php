<?php

// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["d_loggedin"]) || $_SESSION["d_loggedin"] !== true){
    header("location: dealer.php");
    exit;
}

require_once "config.php";
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

<style type="text/css">
body{margin-left: 50px;}
td{ padding: 20px;}
.wrapper{ width: 350px; padding: 20px;}
</style>
</head>
<body>

<a href="dealer_welcome.php">Go back to your homepage.</a>
<!--
<div class="row">
<h4>Smart search:</h4></center>
<span>By brand  <input type="radio" name="brand" value="'.$row['BrandName'].'"></span>
<span>By model  <input type="radio" name="brand" value="'.$row['BrandName'].'"></span>
<span>By price  <input type="radio" name="brand" value="'.$row['BrandName'].'"></span>
<span>By brand and price  <input type="radio" name="brand" value="'.$row['BrandName'].'"></span>
<span>By model and price  <input type="radio" name="brand" value="'.$row['BrandName'].'"></span>
</div>
-->
<div class="row">
        <form method="post">
            <div class="form-group" style="display: inline;">
                <h4>Brand</h4>

<?php

$result=mysqli_query($link, 'Select BrandName from brands');
while ($row=mysqli_fetch_array($result))
{

    //echo '<option value="'.$row[BrandName].'">'.$row[BrandName].'</option>';
    echo '<span>   '.$row['BrandName'].'  <input type="radio" name="brand" value="'.$row['BrandName'].'"></span>';
}
?>      
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
        </form>
    </div>

</div>
<div class="row">


<?php
    global $brand ;
    if( isset( $_POST['brand'] ) ){
        $brand=$_POST['brand'];
        $res=mysqli_query($link, 'Select ModelName from models right join brands on models.BrandID=brands.BrandID where BrandName="'.$brand.'";');
        echo '<form method="post"><div class="form-group"><h4>Model</h4>';
        while ($row=mysqli_fetch_array($res))
        {
            echo '<span>'.$row['ModelName'].'  <input type="radio" name="model" value="'.$row['ModelName']. '"></span>';
        }

        echo'<div class="form-group"><input type="submit" class="btn btn-primary" value="Submit"></div></form></div>';

        $res=mysqli_query($link, 
        'SELECT VIN, BrandName, ModelName, Body, Color, MName FROM vehicles, brands, models, manufacturers
where models.BrandID=brands.BrandID and models.ModelID = vehicles.ModelID and manufacturers.MID=vehicles.MID and vehicles.VIN NOT IN (select VIN from inventories) and BrandName="'.$brand.'" ;');
    }

    if( isset( $_POST['model'])  ){
        $model=$_POST['model'];
        $res=mysqli_query($link, 
        'SELECT VIN, BrandName, ModelName, Body, Color, MName FROM vehicles, brands, models, manufacturers
where models.BrandID=brands.BrandID and models.ModelID = vehicles.ModelID and manufacturers.MID=vehicles.MID and vehicles.VIN NOT IN (select VIN from inventories) and ModelName="'.$model.'";');
    }


        if (!empty($res)){
            echo '<form method="post"><div class="form-group"><table border=1 ">

            <tr>
                <td>VIN</td>
                <td>Brand</td>
                <td>Model</td>
                <td>Body</td>
                <td>Color</td>
                <td>Manufacturer</td>
            </tr>

            ';
            while ($row=mysqli_fetch_array($res))
            {
                echo'

                   <tr>
                    <td><input type="radio" name="vin" value=" '.$row['VIN'].'" >'.$row['VIN'].'</td>
                    <td>'.$row['BrandName'].'</td>
                    <td>'.$row['ModelName'].'</td>
                    <td>'.$row['Body'].'</td>
                    <td>'.$row['Color'].'</td>
                    <td>'.$row['MName'].'</td>
                  </tr>
                ';
            }
            echo'</table><span class="help-block"><?php echo $sel_err; ?></span></div>';
        }
        else{
            echo '<p>nothing found</p>';
        }
?>
</div>
<div class="row">
<div class="col-sm-3 col-sm-offset-9">
    <h3>Assign a price</h3>
    <span class="help-block"><?php echo $pr_err; ?></span>
    <div class="form-group">
        <input type="number" name="price">
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
    </div>
            
</div>

</div>


</body>
</html>