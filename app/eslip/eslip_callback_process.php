<?php 

/**
* Formulario utilizado para redirigir al usuario a la URL configurada en el plugin y postear
* los datos obtenidos del proveedor de identidad, los cuales serán utilizados por el desarrollador.
* 
* @author Nicolás Burghi [nicoburghi@gmail.com]
* @author Martín Estigarribia [martinestiga@gmail.com]
* @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
* @package Eslip
*/

include_once('eslip_helper.php');

/**
* Datos que llegan por parametro y son posteados
*
*		server 					Servidor que uso para loguearse
*		referer					URL de la pagina en la questaba en ese momento
*		client_callback_url		URL donde maneja el login a la que tenemos que redirigir ahora
*		state 					Estado del proceso
*
*		user 					Datos del usuario si el estado es 'success'
*		user_identification		Dato que identifica al usuario
*
*		error 					Error que decuelve el sistema si el estado es 'error'
*/

if (IsSet($_GET['data']))
{
	$data = json_decode(base64url_decode($_GET['data']));
?>
	<!DOCTYPE html>
	<html>
	<head>
	</head>
		<body onload="document.getElementById('form_to_post').submit();">
		<form id="form_to_post" action="<?php echo $data->client_callback_url; ?>" method="POST">
			<input type="hidden" name="server" value="<?php echo $data->server; ?>" >
			<input type="hidden" name="referer" value="<?php echo $data->referer; ?>" >
			<input type="hidden" name="state" value="<?php echo $data->state; ?>" >
			<?php if($data->state == 'success'){ ?>
			<input type="hidden" name="user" value='<?php echo json_encode($data->user); ?>' >
			<input type="hidden" name="user_identification" value='<?php echo ($data->user_identification); ?>' >
			<?php }else{ ?>
			<input type="hidden" name="error" value='<?php echo $data->error; ?>' >
			<?php } ?>
		</form>
	</body>
	</html>
<?php
}
?>