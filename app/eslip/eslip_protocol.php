<?php

/**
* Clase de excepción personalizada
*
* Clase que amplia la clase Exception interna de PHP para una personalización de los errores
* 
* @author Nicolás Burghi [nicoburghi@gmail.com]
* @author Martín Estigarribia [martinestiga@gmail.com]
* @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
* @package Eslip
*/

class EslipException extends Exception {}

/**
* SuperClase de los protocolos de autenticación OAuth y OpenID
*
* Se define funcionalidad comun a las clases en donde se implementan ambos protocolos.
* 
* @author Nicolás Burghi [nicoburghi@gmail.com]
* @author Martín Estigarribia [martinestiga@gmail.com]
*
* @package Eslip
*/

class eslip_protocol {

    /**
    * Instancia de la Api del plugin
    *
    * @var object
    * @access protected
    */

    protected $eslip;

    /**
    * URL de la cual proviene el llamado al plugin para luego ser enviado de vuelta al sitio para que
    * el desarrollador pueda redirigir
    *
    * @var string
    * @access protected
    */

    protected $referer;

    /**
    * Determina si se debe abandonar la ejecución del script
    *
    * @var boolean 
    * @access protected
    */

    protected $exit;

    /**
    * Metodo constructor de la clase
    *
    * @access public
    * @throws EslipException Si el servidor no tiene instalado el modulo PHP cURL
    * @throws EslipException Si no se puede iniciar una sesion PHP
    */

    public function __construct()
    {
        if ( ! function_exists("curl_init"))
        {
            throw new EslipException(CurlError);
        }

        if(!isset($_SESSION))
        {
            if( ! session_start())
            {
                throw new EslipException(SessionError);
            }
        }
    }

    /**
    * Retorna el valor de la variable exit
    *
    * @access public
    * @return boolean exit
    */

    public function ExitProgram()
    {
        return $this->exit;
    }

    /**
    * Almacena en la sesion los datos obtenidos del proveedor de identidad
    *
    * Los datos obtenidos del proveedor de identidad que el usuario eligio 
    * para autenticarse son almacenados en la sesion para que luego el desarrollador
    * los pueda utlizar cuando quiera.
    *
    * @param array $user_data Arreglo con los datos del usuario
    * @param string $type que puede tener los valores 'openid' o 'oauth' para identificar que protocolo se utilizó
    * @access public
    */

    public function StoreUserDataInSession($user_data, $type)
    {
        if ($type == 'openid')
        {   
            unset($_SESSION['OAUTH']);
        }
        else
        {
            unset($_SESSION['OPENID']);
        }

        $_SESSION['ESLIP_USER_DATA'] = $user_data;
    }

    /**
    * Convierte un arreglo a objeto
    *
    * @param array $array Arreglo de entrada 
    * @return object Se devuele el arreglo que se envio como parametro transformado a tipo objeto
    * @access protected
    */   

    protected function Array2Object($array)
    {
        $obj = new StdClass();
        foreach ($array as $key => $val)
        {
            $obj->$key = $val;
        }
        return $obj;
    }

    /**
    * Codifica una URI a RFC 3986
    *
    * @param string $uri URI a codificar
    * @return string codificado a RFC 3986
    * @access protected
    */
  
    protected function rfc3986_encode($uri)
    {
        $result = rawurlencode($uri);  
        $result = str_replace('%7E', '~', $result);  
        $result = str_replace('=', '%3D', $result);  
        $result = str_replace('+', '%2B', $result);  
  
        return $result;  
    }

    /**
    * Genera una cadena de consulta codificada estilo URL a partir del array asociativo dado
    *
    * @param array $parameters Array asociativo que contenga las llaves y los valores que deben utilizar para formar la cadena
    * @param string $separator Simbolo usado para separar argumentos. Si este parámetro no es especificado, el simbolo '&' será usado
    * @param string $url URL a la que se le debe concatenar la cadena generada. Si este parámetro no es especificado, solo se devuelve la cadena
    * @param string $first Simbolo usado como primer serparador. Util cuando se proporciona una URL a la cual debe ser concatenada.
    * @param boolean $quote Indica si en la cadena a generar, los valores de los parametros se deben encerrar entre comillas.
    * @return string Cadena codificada en forma de URL
    * @access protected
    */

    protected function my_http_build_query($parameters, $separator = '&', $url = '', $first = '', $quote = false)
    {
        if ($first != '')
        {
            $f = (strpos($url, $first) === false);
        }
        else
        {
            $f = TRUE;
        }
        foreach($parameters as $parameter => $value)
        {
            $url .= ($f ? $first : $separator).$this->rfc3986_encode($parameter).'='.($quote ? '"' : '').$this->rfc3986_encode($value).($quote ? '"' : '');
            $f = false;
        }
        return $url;
    }

    /**
    * Realiza una conexion con un servidor utlizando la libreria PHP cURL con los parametros proporcionados
    *
    * @param array $options Array asociativo con los parametros correspondientes necesarios para realizar la conexión
    * @return array Arreglo con información sobre el resultado de conexión. 
    * @throws EslipException Si se produce un error en la operación cURL dettallando cual es el error.
    * @access protected
    */

    protected function request_curl($options = array())
    {
        $defaults = array(  'curl_connecttimeout' => 10, 
                            'curl_timeout' => 30, 
                            'curl_user_agent' => 'ESLIP-API',
                            'curl_followlocation' => FALSE,
                            'curl_ssl_verifypeer' => FALSE,
                            'curl_ssl_verifyhost' => FALSE,
                            'curl_cainfo' => FALSE,
                            'curl_capath' => FALSE,
                            'curl_header' => FALSE,
                            'curl_returntransfer' => FALSE,
                            'curl_httpheader' => FALSE,
                            'url' => FALSE,
                            'method' => FALSE,
                            'parameters' => FALSE
                        );

        $config = array_merge($defaults, $options);

        $curl = curl_init();

        if ($config['curl_followlocation'] && ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) 
        {
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, $config['curl_followlocation']);
        }
        else
        {
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, $config['curl_followlocation']);
        }
        curl_setopt($curl, CURLOPT_USERAGENT, $config['curl_user_agent']);    
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $config['curl_connecttimeout']);    
        curl_setopt($curl, CURLOPT_TIMEOUT, $config['curl_timeout']);    

        // SSL y Certificado
        if($config['curl_ssl_verifypeer'])
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $config['curl_ssl_verifyhost']);
            curl_setopt($curl, CURLOPT_CAINFO, $config['curl_cainfo']);
            curl_setopt($curl, CURLOPT_CAPATH, $config['curl_capath']);
        }
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $config['curl_ssl_verifypeer']);
                
        //OUTPUT
        curl_setopt($curl, CURLOPT_HEADER, $config['curl_header']);                 // Para incluir o no el header en el output
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, $config['curl_returntransfer']); // Devolver el resultado de la transferencia como string del valor de curl_exec() o mostrarlo directamente. 
        
        //HEADER
        curl_setopt($curl, CURLOPT_HTTPHEADER, $config['curl_httpheader']);  
        
        //URL
        curl_setopt($curl, CURLOPT_URL, $config['url']);
        
        if($config['method'] == 'POST')
        {
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $config['parameters']);
        }
        elseif($config['method'] == 'HEAD')
        {
            curl_setopt($curl, CURLOPT_HEADER, TRUE);
            curl_setopt($curl, CURLOPT_NOBODY, TRUE);
        }
        
        $return['response'] = curl_exec($curl);  
        $return['code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $return['content_type'] = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        $return['effective_url'] = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        
        if (curl_errno($curl))
        {
            throw new EslipException('ESLIP ERROR: CURL error: ' . curl_error($curl));
        }

        curl_close($curl);

        return $return;
    }
}