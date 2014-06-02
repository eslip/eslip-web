<?php
include_once('../eslip.php');
session_start();

$isAuthenticated = ( isset($_SESSION['usuario']) && ! empty($_SESSION['usuario']) );

$runWizard = (bool)(string)$eslip->configuration->runWizard;

/* (Assuming session already started) */
if(isset($_SESSION['referrer'])){
    // Get existing referrer
    $redirect = $_SESSION['referrer'];

} elseif(isset($_SERVER['HTTP_REFERER'])){
    // Use given referrer
    $redirect = $_SERVER['HTTP_REFERER'];

} else {
    $redirect = "admin.php";
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="loginPage"><head>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>ESLIP Login</title>

	<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css">
	<link type="text/css" rel="stylesheet" href="css/main.css">
	
	<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	
	<script type="text/javascript" src="js/jquery.validate.js"></script>
	<script type="text/javascript" src="js/eslip_common.js"></script>
	<script type="text/javascript" src="js/eslip_admin.js"></script>

</head>

<body class="loginPage">

	<?php if( ! $isAuthenticated ){ ?>
		<div class="loginContainer borderBox">

		    <div class="row">

		        <div class="col-sm-12">
		            <img src="images/eslip-logo.png" class="col-sm-12" alt="<?php echo Login; ?>">
		        </div>

		    </div>

		    <div class="row">

		        <div class="col-sm-12">
		            <h3 class="title"><?php echo Login; ?></h3>
		        </div>

		    </div>

		    <div class="row">

		        <div class="col-sm-12">
		            <form id="loginForm" name="loginForm" role="form">

						<div class="form-group">
							<label for="adminUser"><?php echo AdminUser; ?>:</label>
							<input type="text" class="form-control" id="adminUser" name="adminUser">
						</div>

						<div class="form-group">
							<label for="adminPass"><?php echo AdminPass; ?>:</label>
							<input type="password" class="form-control" id="adminPass" name="adminPass">
						</div>

						<input type="button" class="btn btn-primary" id="login" value="<?php echo btnLogin; ?>"/>

		            </form>
		        </div>

		    </div>
		    
		    <p></p>

		    <div class="row">

		        <div class="col-sm-12">

		            <div class="errorMessage alert alert-danger fade in" style="display:none;">
						<?php echo messageLoginError; ?>
					</div>

					<?php if ($runWizard){ ?>
					<p></p>
		            <div class="infoMessage alert alert-info fade in">
		                <?php echo messageLoginInfo; ?><strong><a href="setup.php" id="wizard"><?php echo btnWizard; ?></a></strong>
		            </div>
		            <?php } ?>
		            
		        </div>

		    </div>


		</div>

	<?php }else{ ?>
					
		<?php header( 'Location: '.$redirect ); ?>
	
	<?php } ?>
	
</body>

</html>
