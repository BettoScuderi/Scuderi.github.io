<?php

	define('DB_SERVER', 'localhost');
	define('DB_USERNAME', 'root');
	define('DB_PASSWORD', '');
	define('DB_NAME', 'Bettosanderi');
	 
	$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
	 
	if($link === false){
	    die("ERROR: Could not connect. " . mysqli_connect_error());
	}

	$username = $password = $confirm_password = "";
	$username_err = $password_err = $confirm_password_err = "";
 
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            $param_username = trim($_POST["username"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "Questo username è stato già preso";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Qualcosa è andato storto. Riprova!";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
	    if(empty(trim($_POST["password"]))){
	        $password_err = "Per favore inserisci la password";     
	    } elseif(strlen(trim($_POST["password"])) < 6){
	        $password_err = "La password deve contenere minimo 6 caratteri.";
	    } else{
	        $password = trim($_POST["password"]);
	    }
	    
	    if(empty(trim($_POST["confirm_password"]))){
	        $confirm_password_err = "Please confirm password.";     
	    } else{
	        $confirm_password = trim($_POST["confirm_password"]);
	        if(empty($password_err) && ($password != $confirm_password)){
	            $confirm_password_err = "Password did not match.";
	        }
	    }
	    
	    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
	        
	        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
	         
	        if($stmt = mysqli_prepare($link, $sql)){
	            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
	            
	            $param_username = $username;
	            $param_password = password_hash($password, PASSWORD_DEFAULT); 
	            
	            if(mysqli_stmt_execute($stmt)){
	                header("location: login.php");
	            } else{
	                echo "Something went wrong. Please try again later.";
	            }

	            mysqli_stmt_close($stmt);
	        }
	    }
	    
	    mysqli_close($link);
	}
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Registrazione</title>	
    <style type="text/css">
        body{ 
            font: 14px sans-serif; 
            text-align: center; 
            background-color: coral;
        }
        .wrapper{ width: 350px; padding: 20px; }
        .shadowbox {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 30em;
            border: 1px solid #333;
            box-shadow: 8px 8px 5px #444;
            padding: 8px 12px;
            background-image: linear-gradient(180deg, #fff, #ddd 40%, #ccc);
        }
    </style>

</head>
<body>
    <div class="shadowbox">
        <div class="wrapper">
            <h2>Registrati!</h2>
            <p>Riempi i form per registrarti</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                    <span class="help-block"><?php echo $username_err; ?></span>
                </div>    
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                    <span class="help-block"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                    <label>Conferma Password</label>
                    <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                    <span class="help-block"><?php echo $confirm_password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Invia">
                </div>
                <p>Hai già un account? <a href="login.php">Fai il login qui</a>.</p>
            </form>
        </div>    
    </div>
</body>
</html>