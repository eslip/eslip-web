<?php

/**
* ESLIP Services
*
* SuperClase de las APIs de servicios de ESLIP.
*
* Se define la funcionalidad comun a las APIs de servicios.
* 
* @author Nicolás Burghi [nicoburghi@gmail.com]
* @author Martín Estigarribia [martinestiga@gmail.com]
* @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
* @package Eslip
*/

class EslipServices {

	/**
    * Objeto que contiene una instancia de la API XML de ESLIP
    *
    * @var object 
    * @access protected
    */

	protected $xmlApi;

	/**
    * Metodo constructor de la clase. Se inicializa la variable de clase que contiene una instancia 
    * de la API XML de ESLIP y se llama al metodo que realiza la llamada al metodo solicitado.
    *
    * @access public
    * @param object $xmlApi Instancia de la API XML de ESLIP
    */

	public function __construct($xmlApi){
		$this->xmlApi = $xmlApi;
		$this->callService();
	}
	
	/**
	* Este metodo llama dinámicamente a la función de la API de Servicios correspondiente dependiendo del 
	* valor del parámetro rquest.
	*
	* @access public
	*/

	public function callService()
	{
		$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
		if((int)method_exists($this,$func) > 0)
		{
			$this->$func();
		}
	}

	/**
	* Metodo utilizado por los servicios para retornar en formato JSON la información solicitada
	*
	* @access protected
	* @param Array $data Informacion para ser devuelta por el servicio
	*/

	protected function response($data)
	{
		echo json_encode($data);
		exit;
	}

	/**
	* Retorna un arreglo con los proveedores de identidad activos en el adminstrador.
	*
	* @access protected
	* @return array Arreglo con los proveedores de identidad
	*/

	protected function getActiveIdentityProviders()
	{
		$idps = $this->xmlApi->getElementListByFieldValue("active", "1", "identityProvider");
		$configuration = $this->xmlApi->getElementValue("configuration");
		$activeIdps = array();
		foreach ($idps as $ip)
		{
			$_tmp_ip = SimpleXMLElementToObject($ip);
			$styles = $this->xmlApi->getElementById("buttonStyle", $_tmp_ip->id);
			$_tmp_ip->styles = SimpleXMLElementToObject($styles);
			if (isset($_tmp_ip->styles->logo))
			{
				$_tmp_ip->styles->logo_url = $configuration->pluginUrl . 'frontend/img/icons/' . $_tmp_ip->styles->logo;
			}
			$activeIdps[] = $_tmp_ip;
		}
		return $activeIdps;
	}
}