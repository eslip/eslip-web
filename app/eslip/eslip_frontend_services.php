<?php
	
include_once("eslip.php");
include_once("eslip_services.php");

/**
* ESLIP Frontend Services
*
* API de servicios de ESLIP utilizada por el frontend.
*
* Se definen servicios utilizados por el plugin en el frontend
* 
* @author Nicolás Burghi [nicoburghi@gmail.com]
* @author Martín Estigarribia [martinestiga@gmail.com]
* @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
* @package Eslip
*/

class FrontendServiceApi extends EslipServices{
	
	/**
    * Metodo constructor de la clase. Se realiza un llamado al constructor de la clase padre de la que se
    * hereda. 
    *
    * @access public
    * @param object $xmlApi Instancia de la API XML de ESLIP
    */

	public function __construct($xmlApi){
		parent::__construct($xmlApi);
	}
	
	/**
	* Servicio que retorna los datos necesarios para renderizar el widget de Social Login.
	*
	* @access protected
	*/

	protected function getWidgetData(){

		$identityProviders = $this->getActiveIdentityProviders();

		$identityProvidersFixed = array();

		foreach( $identityProviders as $idProvider )
		{
			$idProviderFixed = array(
				"id" => (string)$idProvider->id,
				"label" => (string)$idProvider->label,
				"styles" => $idProvider->styles
			);

			array_push($identityProvidersFixed, $idProviderFixed);
		}

		$eslipSettings = $this->xmlApi->getElementValue("configuration");

		$loginWidget = $eslipSettings->loginWidget->children();
		$loginWidgetFixed = array();

		foreach ( $loginWidget as $key => $value)
		{
			$loginWidgetFixed[$key] = (string)$value;
		}

		$data = array(
			"identityProviders" => $identityProvidersFixed,
			"loginWidget" => $loginWidgetFixed
		);

		$this->response($data);
	}
}

// Inicializar la API
new FrontendServiceApi($xmlApi);