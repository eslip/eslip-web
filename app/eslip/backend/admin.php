<?php
include_once('../eslip.php');
session_start();

$isAuthenticated = ( isset($_SESSION['usuario']) && ! empty($_SESSION['usuario']) );

$_SESSION['referrer'] = currentPageUrl();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>ESLIP Admin</title>

	<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css">
	<link type="text/css" rel="stylesheet" href="css/sidebar-bootstrap.css">
	<link type="text/css" rel="stylesheet" href="css/dataTables.bootstrap.css">
	<link type="text/css" rel="stylesheet" href="css/onoff_switch.css">
	<link type="text/css" rel="stylesheet" href="css/jquery.minicolors.css">
	<link type="text/css" rel="stylesheet" href="css/main.css">
	<link type="text/css" rel="stylesheet" href="../frontend/eslip_plugin.css">
	
	<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="js/jquery.loadTemplate-1.3.2.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/jquery.validate.js"></script>
	<script type="text/javascript" src="js/onoff_switch.js"></script>
	<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="js/dataTables.bootstrap.js"></script>
	<script type="text/javascript" src="js/jquery.minicolors.min.js"></script>
	<script type="text/javascript" src="js/eslip_common.js"></script>
	<script type="text/javascript" src="js/eslip_admin.js"></script>
	<script type="text/javascript" src="../frontend/eslip_plugin.js" id="eslip_script" autoInit="false"></script>

</head>

<body>
	<?php if( ! $isAuthenticated ){ ?>
		<?php header( 'Location: login.php' ); ?>
	<?php }else{ ?>
	<div id="wrapper">

		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	  		<div class="container-fluid">
		    	<!-- Brand and toggle get grouped for better mobile display -->
		    	<div class="navbar-header">
					<a class="navbar-brand" href="#"><?php echo adminTitle; ?></a>
				</div>

		    	<!-- Collect the nav links, forms, and other content for toggling -->
		    	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		     		
		     		<ul class="nav navbar-nav side-nav">
				        <li class="active"><a href="javascript:void(0);" id="generalConfig"><?php echo GeneralConfigs; ?></a></li>
				        <li><a href="javascript:void(0);" id="idProviders"><?php echo IdProviders; ?></a></li>
				        <li><a href="javascript:void(0);" id="configUser"><?php echo ConfigUser; ?></a></li>
				        <li><a href="javascript:void(0);" id="languagesConfig"><?php echo LanguagesConfig; ?></a></li>
				        <li><a href="javascript:void(0);" id="loginWidget"><?php echo LoginWidget; ?></a></li>
				        <li><a href="javascript:void(0);" id="idProvidersButtons"><?php echo IdProvidersButtons; ?></a></li>
				    </ul>

					<ul class="nav navbar-nav navbar-right">
						<li><a href="#" id="logout"><span class="glyphicon glyphicon-off"></span>&nbsp;<?php echo btnLogout; ?></a></li>
					</ul>
		    	</div><!-- /.navbar-collapse -->
			</div><!-- /.container-fluid -->
		</nav>

		<div id="page-wrapper">

		</div>

	</div>

	<!-- Modal -->
	<div class="modal fade" id="dialog-general-message" tabindex="-1" role="dialog" aria-labelledby="dialog-general-message-label" aria-hidden="true">
	    <div class="modal-dialog modal-sm">
	        <div class="modal-content">
	            <div class="modal-header">
	            	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	                <h4 class="modal-title" id="dialog-general-message-label">Info</h4>
	            </div>
	            <div class="modal-body">
	            </div>
	        </div>
	    </div>
	</div>

	<?php } ?>

</body>

</html>
