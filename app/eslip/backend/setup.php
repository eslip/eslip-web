<?php
include_once("../eslip.php");
session_start();

$isAuthenticated = ( isset($_SESSION['usuario']) && ! empty($_SESSION['usuario']) );

$runWizard = (bool)(string)$eslip->configuration->runWizard;

$_SESSION['referrer'] = currentPageUrl();

// si (no esta autenticado y run wizard es verdadero)
//		mostrar wizard original
//	si no (no esta autenticado y run wizard es false)
//		redirigir a login
//	si no (si esta autenticado y run wizard es false)
//		correr wizard nuevo con menos opciones
//  ! $isAuthenticated && $runWizard

// si no esta autenticado y run wizard es falso
if ( ! $isAuthenticated && ! $runWizard){
	header( 'Location: login.php' );
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>ESLIP Setup</title>

	<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css">
	<link type="text/css" rel="stylesheet" href="css/bootstrap-wizard.css">
	<link type="text/css" rel="stylesheet" href="css/onoff_switch.css">
	<link type="text/css" rel="stylesheet" href="css/main.css">
	<link type="text/css" rel="stylesheet" href="../frontend/eslip_plugin.css">

	<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="js/jquery.loadTemplate-1.3.2.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/bootstrap-wizard.min.js"></script>
	<script type="text/javascript" src="js/jquery.validate.js"></script>
	<script type="text/javascript" src="js/onoff_switch.js"></script>
	<script type="text/javascript" src="js/eslip_common.js"></script>
	<script type="text/javascript" src="js/eslip_setup.js"></script>
	<script type="text/javascript" src="../frontend/eslip_plugin.js" id="eslip_script" autoInit="false"></script>

</head>

<body>
	
	<div id="wizardContainer" class="row">
			
	</div>

	<!-- Modal -->
	<div class="modal fade" id="dialog-lang" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="dialog-lang-label" aria-hidden="true">
	    <div class="modal-dialog modal-sm">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title" id="dialog-lang-label"><?php echo SelectLangTitle; ?></h4>
	            </div>
	            <div class="modal-body">
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-primary btn-select">Seleccionar</span></button>
	            </div>
	        </div>
	    </div>
	</div>

</body>

</html>
