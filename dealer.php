<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["d_loggedin"]) && $_SESSION["d_loggedin"] === true){
  header("location: dealer_welcome.php");
  exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$name = "";
$name_err = "";

$did=0;
$dname='';
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["name"]))){
        $name_err = "Please enter username.";
    } else{
        $name = trim($_POST["name"]);
    }
    
    // Validate credentials
    if(empty($name_err)){
        // Prepare a select statement
        $sql = "SELECT DID, DName FROM dealers WHERE DName = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_name);
            
            // Set parameters
            $param_name = $name;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $did, $dname);
                    if(mysqli_stmt_fetch($stmt)){
                        
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["d_loggedin"] = true;
                            $_SESSION["d_id"] = $did;
                            $_SESSION["d_name"] = $dname;

                            
                            // Redirect user to welcome page
                            header("location: dealer_welcome.php");
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $name_err = "No dealer found with that name.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Dealer Interface</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">

<style type="text/css">
.wrapper{ width: 350px; padding: 20px;}
</style>
</head>
<body>

<center>
<div class="wrapper">
<form method="post">
            <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                <span class="help-block"><?php echo $name_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
</form>
</div>
</center>

</body>
</html>
