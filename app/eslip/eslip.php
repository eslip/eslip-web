<?php

include_once("eslip_helper.php");

/**
* ESLIP
*
* Clase con funcionalidad generica para todo el plugin
*
* Se definen metodos generales del plugin para ser utilizados donde este disponible un objeto
* de esta clase. Además se incluye el archivo de lenguaje correspondiente al idioma configurado en el 
* administrador del plugin el cual define las constantes correspondientes para todos los textos 
* que se utilizan en el plugin.
* 
* @author Nicolás Burghi [nicoburghi@gmail.com]
* @author Martín Estigarribia [martinestiga@gmail.com]
* @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
* @package Eslip
*/

class Eslip {

	/**
    * Objeto que contiene una instancia de la API XML de ESLIP
    *
    * @var object 
    * @access public
    */

	public $xmlApi;

	/**
    * Configuraciones generales del plugin
    *
    * @var object 
    * @access public
    */

	public $configuration;

	/**
    * Idioma del plugin seleccionado en el administrador
    *
    * @var string 
    * @access public
    */

	public $selectedLang;

	/**
    * Metodo constructor de la clase. Se inicializa la variable de clase que contiene una instancia 
    * de la API XML de ESLIP, la variable de clase que contiene el idioma seleccionado 
    * en el administrador y la variable de clase que contiene las configuraciones generales del
    * plugin.
    * Aqui tambien se incluye el archivo de lenguaje correspondiente al idioma configurado en el 
    * administrador del plugin el cual define las constantes correspondientes para todos los textos 
    * que se utilizan en el plugin.
    *
    * @access public
    * @param object $xmlApi Instancia de la clase xmlApi
    */

	public function __construct($xmlApi)
    {
    	$this->xmlApi = $xmlApi;

        $this->configuration = $this->xmlApi->getElementValue("configuration");

        if (isset($_POST["lang"]))
		{
			$selectedLang = (empty($_POST["lang"])) ? getSystemLang() : $_POST["lang"];
		}
		else
		{
			$selectedLang = $this->xmlApi->getElementListByFieldValue("selected", "1", "language");
			$selectedLang = (empty($selectedLang) || empty($selectedLang[0]->code )) ? getSystemLang() : (String)$selectedLang[0]->code;	
		}

		$this->selectedLang = $selectedLang;

		include_once("i18n/".$selectedLang.".php");
    }
}

/**
* ESLIP XML API
*
* API para manipular el archivo XML de configuración. Implementa una interfaz para obtener
* y guardar facilmente información en el archivo XML de configuración.
* 
* @author Nicolás Burghi [nicoburghi@gmail.com]
* @author Martín Estigarribia [martinestiga@gmail.com]
* @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
* @package Eslip
*/

class EslipXMLApi {

	/**
    * Archivo de configuración del plugin
    *
    * @var string 
    * @access private
    */

	private $xmlFile = "";

	/**
    * Metodo constructor de la clase. Se inicializa la variable de clase que contiene la ubicación
    * del archivo XML de configuración.
    *
    * @access public
    */

	public function __construct()
    {
        $this->xmlFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . "config.xml";
    }

    /**
    * Convierte el archivo XML de configuración en un objeto y lo retorna.
    *
    * @access private
    * @return object Retorna un object de tipo SimpleXMLElement cuyas propiedades contienen los datos del documento XML, o FALSE en caso de error.
    */
	
	private function getXml()
	{
		$xml = simplexml_load_file($this->xmlFile);
		return $xml;
	}
	
	/**
    * Escribe en el archivo XML de configuración un string XML correcto basado en un 
    * elemento SimpleXML que se recibe como parámetro.
    *
    * @access private
    * @param SimpleXML $xml objeto de tipo SimpleXML
    */

	private function saveXml($xml)
	{
		$xml->asXml($this->xmlFile);
	}
	
	/**
    * Escribe en el archivo XML de configuración un string XML correcto basado en un 
    * elemento SimpleXML que se recibe como parámetro, respetando el formato de identación
	* de los archivos XML.
    *
    * @access private
    * @param SimpleXML $xml objeto de tipo SimpleXML
    */
	
	private function saveFormattedXml($xml)
	{
		$this->saveXml($xml);
		$dom = new DOMDocument("1.0");
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->load($this->xmlFile);
		$dom->save($this->xmlFile);
	}
	
	/**
    * Retorna una cadena que representa una consulta compatible con xpath 
    *
    * @access private
    * @param String $element cadena que representa al elemento del XML que se desea buscar
	* @param String $parent cadena que representa al padre del elemento que se desea busccar, en caso de que se conozca
	* @return String cadena que representa una consulta compatible con xpath para buscar el elemento
    */
	
	private function getQuery($element, $parent=null)
	{
		$query = "/";
		if ($parent != null && $parent != "")
		{
			$query .= "/".$parent;
		}
		$query .= "/".$element;
		return $query;
	}

	/**
    * Retorna el valor de un determinado elemento del XML de configuración
    *
    * @access public
    * @param String $element cadena que representa al elemento del XML del cual se desea buscar su valor
	* @param String $parent cadena que representa al padre del elemento en cuestión, en caso de que se conozca
	* @return Object valor del elemento
    */
	
	public function getElementValue($element, $parent=null)
	{
		$xml = $this->getXml();
		$query = $this->getQuery($element, $parent);
		$result = $xml->xpath($query);
		return (!empty($result)) ? $result[0] : false ;
	}

	/**
    * Asigna un valor a un determinado elemento del XML de configuración
    *
    * @access public
    * @param String $element cadena que representa al elemento del XML al cual se desea asignarle un valor
	* @param Object $value valor que será asignado al elemento
	* @param String $parent cadena que representa al padre del elemento en cuestión, en caso de que se conozca
    */
	
	public function setElementValue($element, $value, $parent=null)
	{
		$xml = $this->getXml();
		$query = $this->getQuery($element, $parent);
		$result = $xml->xpath($query);
		$result[0][0] = $value;
		$this->saveXml($xml);
	}

	/**
    * Retorna una lista de elementos SimpleXMLElement
    *
    * @access public
    * @param String $element cadena que representa al elemento del XML que se desea buscar
	* @param String $parent cadena que representa al padre del elemento en cuestión, en caso de que se conozca
	* @return Array lista de elementos SimpleXMLElement encontrados
    */
	
	public function getElementList($element, $parent=null)
	{
		$xml = $this->getXml();
		$query = $this->getQuery($element, $parent);
		$result = $xml->xpath($query);
		return $result;
	}

	/**
    * Retorna un elemento SimpleXMLElement determinado por el valor de su atributo id 
    *
    * @access public
    * @param String $element cadena que representa al elemento del XML que se desea buscar
	* @param String $elementId id del elemento en cuestión
	* @return SimpleXMLElement elemento encontrado
    */
	
	public function getElementById($element, $elementId)
	{
		$element = $element."[@id='".$elementId."']";
		return $this->getElementValue($element);
	}

	/**
    * Actualiza los valores de los campos especificados de un elemento del XML determinado por el valor de su atributo id 
    *
    * @access public
	* @param Array $fields lista de los campos que serán actualizados
	* @param Array $values lista con los valores que serán asignados a los campos
    * @param String $element cadena que representa al elemento del XML que se desea actualizar
	* @param String $elementId id del elemento que se desea actualizar
    */
	
	public function updateElementById($fields, $values, $element, $elementId)
	{
		$parent = $element."[@id='".$elementId."']";
		for ($i=0; $i < count($fields); $i++)
		{
			$this->setElementValue($fields[$i], $values[$i], $parent);
		}
	}
	
	/**
    * Actualiza los valores de los campos especificados de los elementos del XML 
    *
    * @access public
	* @param Array $fields lista de los campos que serán actualizados
	* @param Array $values lista con los valores que serán asignados a los campos
    * @param String $element cadena que representa a los elemento del XML que se desea actualizar
    */
	
	public function updateElement($fields, $values, $element)
	{
		$xml = $this->getXml();
		$query = $this->getQuery($element);
		$result = $xml->xpath($query);
		foreach( $result as $node )
		{
			for ($i=0; $i < count($fields); $i++)
			{
				$n = $node->$fields[$i];
				$n[0][0] = $values[$i];
			}
		}
		$this->saveXml($xml);
	}

	/**
    * Agrega un elemento al XML
    *
    * @access public
	* @param String $id atributo id que tendrá el elemento
	* @param Array $fields lista de los campos que tendrá el elemento
	* @param Array $values lista con los valores de los campos del elemento
    * @param String $element cadena que representa al elemento a agregar
	* @param String $parent cadena que representa al quién será el padre del elemento a agregar
    */
	
	public function addElement($id, $fields, $values, $element, $parent)
	{
		$xml = $this->getXml();
		$query = $this->getQuery($parent, null);
		$result = $xml->xpath($query);
		$newElem = $result[0]->addChild($element);
		if (!empty($id))
		{
			$newElem->addAttribute("id", $id);
		}
		for ($i=0; $i < count($fields); $i++)
		{
			$newElem->addChild($fields[$i], $values[$i]);
		}
		$this->saveFormattedXml($xml);
	}
	
	/**
    * Elimina un elemento del XML determinado por el valor de su atributo id
    *
    * @access public
    * @param String $element cadena que representa al elemento del XML que se desea eliminar
	* @param String $elementId id del elemento que se desea eliminar
    */
	
	public function removeElementById($element, $elementId)
	{
		$element = $element."[@id='".$elementId."']";
		$xml = $this->getXml();
		$query = $this->getQuery($element);
		$result = $xml->xpath($query);
		if (!empty($result))
		{
			unset($result[0][0]);
			$this->saveFormattedXml($xml);
		}
	}
	
	/**
    * Retorna una lista de elementos SimpleXMLElement determinada por el valor de un determinado campo
    *
    * @access public
	* @param String $field cadena que representa al campo por el cual se desea buscar
	* @param String $value cadena que representa al valor del campo por el cual se desea buscar
    * @param String $element cadena que representa al elemento del XML que se desea buscar
	* @param String $parent cadena que representa al padre del elemento en cuestión, en caso de que se conozca
	* @return Array lista de elementos SimpleXMLElement encontrados
    */
	
	public function getElementListByFieldValue($field, $value, $element, $parent=null)
	{
		$xml = $this->getXml();
		$query = $this->getQuery($element, $parent)."[".$field."='".$value."']";
		$result = $xml->xpath($query);
		return $result;
	}
	
	/**
    * Asigna el valor a un campo de un elemento del XML determinado por el valor de un determinado campo
    *
    * @access public
	* @param String $field cadena que representa al campo por el cual se desea buscar al elemento a actualizar
	* @param String $value cadena que representa al valor del campo por el cual se desea buscar al elemento a actualizar
    * @param String $element cadena que representa al elemento del XML que se desea actualizar
	* @param String $parent cadena que representa al padre del elemento en cuestión, en caso de que se conozca
	* @param String $fieldToSet cadena que representa al campo al cual se desea actualizar su valor
	* @param String $valueToSet cadena que representa al valor que será asignado al campo
    */
	
	public function setElementListByFieldValue($field, $value, $element, $parent=null,$fieldToSet,$valueToSet)
	{
		$xml = $this->getXml();
		$query = $this->getQuery($element, $parent)."[".$field."='".$value."']";
		$result = $xml->xpath($query);
		$result = $result[0]->$fieldToSet;
		$result[0][0] = $valueToSet;
		$this->saveXml($xml);
	}

	/**
    * Retorna una lista de atributos de un elemento del XML
    *
    * @access public
    * @param String $element cadena que representa al elemento del XML del cual se quieren obtener los atributos
	* @param String $elementId id del elemento del XML del cual se quieren obtener los atributos
	* @return Array arreglo asociativo de atributos y valores de esos atributos
    */
	
	public function getElementAttributesListById($element, $elementId)
	{
		$element = $element."[@id='".$elementId."']";
		return $this->getElementValue($element)->attributes();
	}

	/**
    * Retorna el valor de un atributo determinado de un elemento del XML
    *
    * @access public
	* @param String $attribute cadena que representa al atributo del elemento del XML del cual se quiere obtener el valor
    * @param String $element cadena que representa al elemento del XML del cual se quiere obtener el valor del atributo
	* @param String $elementId id del elemento del XML del cual se quiere obtener el valor del atributo
	* @return Object valor del atributo
    */
	
	public function getElementAttributeById($attribute, $element, $elementId)
	{
		$result = $this->getElementAttributesListById($element, $elementId);
		return $result->$attribute;
	}

	/**
    * Asgina el valor a un atributo determinado de un elemento del XML
    *
    * @access public
	* @param String $attribute cadena que representa al atributo del elemento del XML al cual se desea asginar el valor
    * @param String $value cadena que representa al valor que será asignado al atributo
	* @param String $element cadena que representa al elemento del XML el cual contiene el atributo a atualizar
	* @param String $elementId id del elemento del XML que contiene el atributo a atualizar
    */
	
	public function setElementAttributeById($attribute, $value, $element, $elementId)
	{
		$element = $element."[@id='".$elementId."']";
		$xml = $this->getXml();
		$query = $this->getQuery($element);
		$result = $xml->xpath($query);
		$attributes = $result[0]->attributes()->$attribute = $value;
		$this->saveXml($xml);
	}

	/**
    * Asgina valores a una lista de atributo determinada de un elemento del XML
    *
    * @access public
	* @param Array $attributes lista de los atributos del elemento del XML a los cuales se desea asginar valores
    * @param Array $values lsita con los valores que serán asignados a los atributos
	* @param String $element cadena que representa al elemento del XML el cual contiene los atributos a atualizar
	* @param String $elementId id del elemento del XML que contiene los atributos a atualizar
    */
    
	public function setElementAttributesById($attributes, $values, $element, $elementId)
	{
		for ($i=0; $i < count($attributes); $i++)
		{
			$this->setElementAttributeById($attributes[$i], $values[$i], $element, $elementId);
		}
	}
}

$xmlApi = new EslipXMLApi();
$eslip = new Eslip($xmlApi);