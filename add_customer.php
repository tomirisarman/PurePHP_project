<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$name = $address = $phone = $gender = "";
$name_err = $address_err = $phone_err = $gender_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["name"]))){
        $name_err = "Please enter a name.";
    } 
    else{
    	$name = trim($_POST["name"]);
    }

    if(empty(trim($_POST["address"]))){
        $address_err = "Please enter a address.";
    } 
    else{
    	$address = trim($_POST["address"]);
    }

    if(empty(trim($_POST["phone"]))){
        $phone_err = "Please enter a phone.";
    } 
    else{
    	$phone = trim($_POST["phone"]);
    }

    if(empty(trim($_POST["gender"]))){
        $gender_err = "Please enter a gender.";
    } 
    else{
    	$gender = trim($_POST["gender"]);
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($address_err) && empty($phone_err) && empty($gender_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO customers (CName, Address, Phone, Gender) VALUES (?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssis", $param_name, $param_address, $param_phone, $param_gender);
            
            // Set parameters
            $param_name = $name;
	        $param_address = $address;
	        $param_phone = $phone;
	        $param_gender = $gender;

            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: customer.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>
<html>
<head>
<title>Customer Interface</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">

<style type="text/css">
.wrapper{ width: 350px; padding: 20px;}
</style>
</head>
<body>

<center>
<div class="wrapper">
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                <span class="help-block"><?php echo $name_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($address_err)) ? 'has-error' : ''; ?>">
                <label>Address</label>
                <input type="text" name="address" class="form-control" value="<?php echo $address; ?>">
                <span class="help-block"><?php echo $address_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($phone_err)) ? 'has-error' : ''; ?>">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="<?php echo $phone; ?>">
                <span class="help-block"><?php echo $phone_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($gender_err)) ? 'has-error' : ''; ?>">
                <label>Gender</label>
                <input type="text" name="gender" class="form-control" value="<?php echo $gender; ?>">
                <span class="help-block"><?php echo $gender_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
</form>
</div>
</center>

</body>
</html>