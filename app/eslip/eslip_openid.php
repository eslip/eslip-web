<?php
include_once('eslip.php');
include_once('eslip_protocol.php');

/**
* Clase de excepción personalizada para OpenID
*
* Clase que amplia la clase Exception interna de PHP para una personalización de los errores
* en este caso para Open ID
* 
* @author Nicolás Burghi [nicoburghi@gmail.com]
* @author Martín Estigarribia [martinestiga@gmail.com]
* @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
* @package Eslip
*/

class OpenIDException extends Exception {}

/**
* ESLIP OpenID
*
* Esta clase implementa una interfaz para autenticar un usuario a traves del protocolo
* OpenID. Soporta tanto las versiones 1.0 como la 2.0 del protocolo.
*
* @author Nicolás Burghi [nicoburghi@gmail.com]
* @author Martín Estigarribia [martinestiga@gmail.com]
* @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
* @package Eslip
*/

class eslip_openid extends Eslip_protocol
{
    /**
    * Arreglo con atributos que se desean requerir al proveedor de identidad
    *
    * @var array
    * @access private
    */

    private $required = array();

    /**
    * Arreglo con atributos que se desean requerir al proveedor de identidad pero serán opcionales
    *
    * @var array
    * @access private
    */

    private $optional = array();

    /**
    * URL a donde debe retornar el proveedor de identidad. Debe ser la URL del script  que procesa la 
    * respuesta del proveedor de identidad
    *
    * @var string
    * @access private
    */
        
    private $returnUrl;

    /**
    * URL del sitio que esta implementando el plugin para ser mostrado en el proveedor de identidad a la 
    * hora de pedir autenticacion
    *
    * @var string
    * @access private
    */

    private $trustRoot;

    /**
    * URL de la ubicacion del formulario donde el usuario debe insertar su OpenID
    *
    * @var string
    * @access private
    */

    private $openid_form_url = '';

    /**
    * Datos enviados al plugin via GET o POST
    *
    * @var array
    * @access private
    */

    private $data;

    /**
    * Modo que devuelve en la resupesta el proveedor de identidad
    *
    * @var string
    * @access private
    */

    private $mode;

    /**
    * Identificador OpenID que el usuario ingresa en el formulario
    *
    * @var string
    * @access private
    */

    private $identity;

    /**
    * Es el identificador reclamado. Este identificador es una URL que el usuario final dice poseer 
    * pero aún no fue verificada.
    *
    * @var string
    * @access private
    */

    private $claimed_id;

    /**
    * Indica si el servidor OpenID implementa la extención AX (Attribute Exchange) para luego 
    * enviarle los parametros correspondientes para obtener los datos deseados del usuario
    *
    * @var boolean
    * @access private
    */

    private $ax = FALSE;

    /**
    * Indica si el servidor OpenID implementa la extención SREG (Simple Registration) para luego 
    * enviarle los parametros correspondientes para obtener los datos deseados del usuario
    *
    * @var boolean
    * @access private
    */

    private $sreg = FALSE;

    /**
    * Contiene la URL del servidor OpenID. Extremo del protocolo obtenido de realizar el descubrimiento.
    *
    * @var string
    * @access private
    */

    private $server;

    /**
    * Version del servidor OpenID identificado en el proceso de descubrimiento
    *
    * @var integer
    * @access private
    */

    private $version;

    /**
    * Especifica si el servidor OpenID soporta Identifier Select
    *
    * @var boolean
    * @access private
    */

    private $identifier_select = FALSE;

    /**
    * Determina si la autenticacion se va a realizar utilizando el modo inmediato
    *
    * @var boolean
    * @access private
    */

    private $immediate = FALSE;

    /**
    * Arreglo para mapear nombres de parametros de la extencion AX a nombres de la extension SREG o viceversa
    *
    * @var array
    * @access private
    */

    static private $ax_to_sreg = array(
        'namePerson/friendly'     => 'nickname',
        'contact/email'           => 'email',
        'namePerson'              => 'fullname',
        'birthDate'               => 'dob',
        'person/gender'           => 'gender',
        'contact/postalCode/home' => 'postcode',
        'contact/country/home'    => 'country',
        'pref/language'           => 'language',
        'pref/timezone'           => 'timezone'
        );

    /**
    * Metodo constructor de la clase. Se inicializan las variables de configuración del plugin
    *
    * @access public
    * @param string $eslip_data Cadena codificada en base64 con datos internos del plugin que se deben mantener entre llamadas
    * @param array $identity_provider Arreglo con los datos de configuración del plugin
    * @param string $site_url URL del sitio que esta implementando el plugin
    * @throws EslipException Si el constructor no recibe el parametro $eslip_data
    * @throws EslipException Si el constructor no recibe el parametro $identity_provider
    * @throws EslipException Si el constructor no recibe el parametro $site_url
    */

    public function __construct($eslip, $referer)
    {
        parent::__construct();

        $this->eslip = $eslip;

        $this->referer = $referer;

        $eslip_data = base64url_encode(json_encode(array('referer' => $this->referer )));

        $this->returnUrl = str_replace('{ESLIP_DATA}', $eslip_data, (string)$this->eslip->configuration->pluginUrl."eslip_openid.php?eslip_data={ESLIP_DATA}" );

        $identity_provider = $this->eslip->xmlApi->getElementById("identityProvider",'openid');

        if (is_array($identity_provider))
        {
            $identity_provider = $this->Array2Object($identity_provider);
        }

        $this->trustRoot = $this->prep_trust_url($this->eslip->configuration->siteUrl);

        $this->openid_form_url = $this->my_http_build_query(array('return_url' => $this->returnUrl), '&', $identity_provider->formUrl, '?');

        $this->immediate = ((string)$identity_provider->immediate == '1') ? TRUE : FALSE ;

        $this->data = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : $_GET;

        $this->mode = $this->GetRequestMode();

        $this->identity = $this->ProcessIdentity();

        $this->claimed_id = $this->identity;

        $this->required = explode(',', $identity_provider->scopeRequired);

        $this->optional = explode(',', $identity_provider->scopeOptional);

        $this->process();
    }

    /**
    * Prepara la URL del sitio que implementa el plugin para que pueda ser utilizada correctamente
    * como lo indica el protocolo OpenID 
    *
    * @access private
    * @param string $site_url URL que se quiere preparar
    * @return string URL preparada
    */

    private function prep_trust_url($site_url)
    {
        $parse_url = parse_url($site_url);

        if (! isset($parse_url['host']))
        {
            $url = explode('/', $parse_url['path']);
			$url = $url[0];
        }
        else
        {
            $url = $parse_url['host'];
        }
        if (! isset($parse_url['scheme']))
        {
            $url = 'http://'.$url;
        }
        else
        {
            $url = $parse_url['scheme'].'://'.$url;
        }

        return trim($url);
    }

    /**
    * Procesa y prepara el identificador OpenID que el usuario ingresa en el formulario o que ya se encuentra
    * en la sesion para que pueda ser utilizada correctamente como lo indica el protocolo OpenID 
    *
    * @access private
    * @return string identificador del usuario
    */

    private function ProcessIdentity()
    {
        if (IsSet($_POST['openid_identifier']))
        {
            $value = trim(strtolower((String)$_POST['openid_identifier']));
            
            $domain = get_domain($value);
            
            if (strpos($value, 'xri://') !== FALSE)
            {
                //Si es un i-name de la manera xri:// se le quita el xri:// y se deja solo el i-name
                $value = substr($value, strlen('xri://'));
            }
            elseif (checkdnsrr($domain)) // Primero se chequea si existe el dominio que fue ingresado
            {
                // Antepone http:// al principio si no esta presenta y / al final si es un dominio y no están presente
                if ((strpos($value, 'http://') === FALSE) && (strpos($value, 'https://') === FALSE)) 
                {
                    // Si no hay / en la URL, entonces debe ser un dominio, por lo tanto se agrega / al final
                    if (strpos($value, '/') === FALSE) 
                    {
                        $value = $value . '/';
                    }
                    $value = 'http://'.$value;
                } 
                else
                {
                    // Sabemos que empieza ya con http o https. Ahora bien, si no hay ningúna / tras el http (s) :// entonces añadimos una, porque entonces es probable que sea un dominio
                    if ((strpos($value, '/', strlen('http://')) === FALSE) || (strpos($value, '/', strlen('https://')) === FALSE)) 
                    {
                        $value = $value . '/';
                    } 
                }
            }
            else
            {
                throw new OpenIDException(URIErrorInOpenIDDiscover);
            }
            
            // Se almacena en la session para armar la setup url en el caso de que immediate falle y no devuelva setup url
            $_SESSION['OPENID']['IDENTITY'] = $value;
        }
        else
        {
            if(IsSet($_SESSION['OPENID']['IDENTITY']) && !empty($this->mode)) 
            {
                // Se utiliza la identidad almacenada en la session solo cuando es la respuesta del OP
                $value = $_SESSION['OPENID']['IDENTITY'];
            }
            else
            {
                $value = FALSE;    
            }
        }

        return $value;
    }

    /**
    * Devuelve el parametro mode que se utiliza para distinguir que mensaje está siendo enviado o recibido. El mismo 
    * esta presente en todos los mensajes de OpenID
    *
    * @access private
    * @return string parametro mode
    */

    private function GetRequestMode()
    {
        return ( empty($this->data['openid_mode']) ) ? FALSE : $this->data['openid_mode'];
    }

    /**
    * Realiza una peticion utlizando la libreria cURL a una URL con opciones y parametros especificos.
    * La URL con la que se desea comunicar, y los parametros que se utilizan son recibidos por este metodo
    * por parametro.
    *
    * @access private
    * @param string $url URL con la que nos queremos comunicar
    * @param string $method Metodo HTTP que se va a utilizar para comunicarse con el destino
    * @param array $params Parametros que se van a enviar en la petición
    * @param boolean $update_claimed_id Determina si se debe actualizar la propiedad de la clase claimed_id
    * @param boolean $includeHeader Determina si el servidor con el que nos comunicamos debe incluir la cabezera HTTP en la respuesta
    * @return array Arreglo con los datos devueltos por la URL con la que nos comunicamos
    */

    private function request($url, $method='GET', $params=array(), $update_claimed_id = FALSE, $includeHeader = FALSE)
    {
        $params = http_build_query($params, '', '&');

        $config['url'] = $url . ($method == 'GET' && $params ? '?' . $params : '');

        $config['method'] = strtoupper($method);
        
        $config['curl_followlocation'] = TRUE;

        $config['curl_ssl_verifypeer'] = FALSE;

        $config['curl_header'] = $includeHeader;

        $config['curl_returntransfer'] = TRUE;
        
        $config['curl_httpheader'][] = 'Accept: application/xrds+xml, */*';

        $config['parameters'] = $params;

        $curl_response = $this->request_curl($config);

        $code = $curl_response['code'];

        if($method == 'HEAD' && $code == 405) 
        {   
            //El servidor no tiene HEAD entonces pido GET con la opcion de que devuelva las cabeceras
            $config['method'] = 'GET';
            $curl_response = $this->request_curl($config);
        }

        $response = $curl_response['response'];
        $headers = array();

        // Pongo el header en un array
        if($method == 'HEAD' || $method == 'GET') 
        {
            $header_response = $response;

            $header_response = substr($response, 0, strpos($response, "\r\n\r\n"));

            foreach(explode("\n", $header_response) as $header)
            {
                $pos = strpos($header,':');
                if ($pos !== false)
                {
                    $name = strtolower(trim(substr($header, 0, $pos)));
                    $headers[$name] = trim(substr($header, $pos+1));
                }
            }

            if($update_claimed_id) 
            {
                //Actualizamos el claimed_id en caso de redirecciones
                $effective_url = $curl_response['effective_url'];
                if($effective_url != $url)
                {
                    $this->identity = $this->claimed_id = $effective_url;
                }
            }
        }

        return array("content" => $response, "headers" => $headers);
    }

    /**
    * Función de ayuda usada para analizar las etiquetas HTML <meta> o <link> extraer información de ellas
    *
    * @access private
    * @param string $content Contenido HTML en el que se va a realizar la busqueda
    * @param string $tag Elemento HTML en el que se encuentra el Atributo que queremos saber su valor
    * @param string $attrName Atributo selector por el que vamos a buscar el elemento
    * @param string $attrValue Valor que debe tener el atributo selector
    * @param string $valueName Atributo del cual queremos saber su valor
    * @return string con el valor buscado si existe o FALSE si no se encuentra nada
    */

    private function getValueOfHtmlTag($content, $tag, $attrName, $attrValue, $valueName)
    {
        preg_match_all("#<{$tag}[^>]*$attrName=['\"].*?$attrValue.*?['\"][^>]*$valueName=['\"](.+?)['\"][^>]*/?>#i", $content, $matches1);
        preg_match_all("#<{$tag}[^>]*$valueName=['\"](.+?)['\"][^>]*$attrName=['\"].*?$attrValue.*?['\"][^>]*/?>#i", $content, $matches2);

        $result = array_merge($matches1[1], $matches2[1]);

        return empty($result) ? FALSE : $result[0];
    }

    /**
    * Realiza el descubrimiento Yadis 
    *
    * @access private
    * @param $url URL de identificacion OpenID que el usuario ingresa en el formulario
    * @return string Extremo del protocolo. URL del servidor OpenID
    * @throws EslipException Si no se proporciona el parametro $url
    * @throws EslipException  Si no se puede descubrir el servidor OpenID
    */

    private function discover($url)
    {
        if (!$url)
        {
            throw new EslipException(ParametersErrorIdentityInOpenIDDiscover);
        }

        // Uso del proxy xri.net para resolver identidades i-name
        if (!preg_match('#^https?:#', $url)) 
        {
            $url = "https://xri.net/$url";
        }
		
        for ($i=0; $i < 5 ; $i++)
        { 
    		$response = $this->request($url, 'GET', array(), TRUE, TRUE);

            $content = $response['content'];

    		$headers = $response['headers'];

    		$location = $this->getValueOfHtmlTag($content, 'meta', 'http-equiv', 'X-XRDS-Location', 'content');
    		
            // Comprobamos si hubo redirección (Sólo una vez, no preocuparse por redirecciones sin fin)
    		if (isset($headers['x-xrds-location']))
            {
    			$url = trim($headers['x-xrds-location']);
    			continue;
    		}
            else if ($location)
            {
    			$url = $location;
    			continue;
            }

            break;
        }

		// Descubrimiento YADIS
		if (isset($headers['content-type']) && (strpos($headers['content-type'], 'application/xrds+xml') !== FALSE || strpos($headers['content-type'], 'text/xml') !== FALSE) )
		{
            // Al parecer, algunos proveedores devuelven documentos XRDS como text/html.
            // A pesar de que está en contra de la especificación, permitiendo esto aquí no debe romper la compatibilidad con nada
            
            // Se encontró un documento XRDS, ahora vamos a buscar el servidor y, opcionalmente delegar

			preg_match_all('#<Service.*?>(.*?)</Service>#s', $content, $m);

			foreach($m[1] as $content) 
            {
                // Se añade el espacio, por lo que strpos no devolverá 0.
				$content = ' ' . $content;

				// OpenID 2
				$ns = preg_quote('http://specs.openid.net/auth/2.0/', '#'); //Escapar caracteres con #
				if(preg_match('#<Type>\s*'.$ns.'(server|signon)\s*</Type>#s', $content, $type))
                {
					if ($type[1] == 'server')
                    {
                        $this->identifier_select = TRUE;   
                    }

                    // Server
					preg_match('#<URI.*?>(.*)</URI>#', $content, $server);
					if (empty($server))
                    {
						throw new EslipException(URIErrorInOpenIDDiscover);
					}
                    $server = $server[1];

                    // Delegate
                    preg_match('#<(Local|Canonical)ID>(.*)</\1ID>#', $content, $delegate);
                    if (isset($delegate[2]))
                    {
                        $this->identity = trim($delegate[2]);
                    }

                    // ¿El servidor anuncia el soporte de AX o SREG?
					$this->ax   = (bool) strpos($content, '<Type>http://openid.net/srv/ax/1.0</Type>');
					$this->sreg = strpos($content, '<Type>http://openid.net/sreg/1.0</Type>') 
                                || strpos($content, '<Type>http://openid.net/extensions/sreg/1.1</Type>');

					$this->version = 2;

					$this->server = $server;

					return $server;
				}

				// OpenID 1.1
				$ns = preg_quote('http://openid.net/signon/1.1', '#');
				if (preg_match('#<Type>\s*'.$ns.'\s*</Type>#s', $content)) {

                    // Server
					preg_match('#<URI.*?>(.*)</URI>#', $content, $server);
					if (empty($server))
                    {
						throw new EslipException(URIErrorInOpenIDDiscover);
					}
                    $server = $server[1];

                    // Delegate
                    preg_match('#<.*?Delegate>(.*)</.*?Delegate>#', $content, $delegate);
                    if (isset($delegate[1]))
                    {
                        $this->identity = $delegate[1];
                    }

                    // AX se puede utilizar sólo con OpenID 2.0, así que comprobamos solo SREG
					$this->sreg = strpos($content, '<Type>http://openid.net/sreg/1.0</Type>')
							   || strpos($content, '<Type>http://openid.net/extensions/sreg/1.1</Type>');

					$this->version = 1;

					$this->server = $server;

					return $server;
				}
			}
		}

        // En este punto, el descubrimiento YADIS ha fallado, así que vamos a cambiar al descubrimiento HTML

        // OpenID 2
		$server   = $this->getValueOfHtmlTag($content, 'link', 'rel', 'openid2.provider', 'href');
		$delegate = $this->getValueOfHtmlTag($content, 'link', 'rel', 'openid2.local_id', 'href');
		$this->version = 2;

		if (!$server) {
			// Lo mismo con OpenID 1.1
			$server   = $this->getValueOfHtmlTag($content, 'link', 'rel', 'openid.server', 'href');
			$delegate = $this->getValueOfHtmlTag($content, 'link', 'rel', 'openid.delegate', 'href');
			$this->version = 1;
		}

		if ($server) 
        {
			// Encontramos un OpenID 2 OP Endpoint
			if ($delegate) 
            {
				// También hemos encontrado un OP-Local ID.
				$this->identity = $delegate;
			}
			$this->server = $server;
			return $server;
		}
		
		throw new EslipException(URIErrorInOpenIDDiscover);
    }

    /**
    * Arma el arreglo con los parametros necesarios que entiende la extension SREG para 
    * solicitar al proveedor de identidad los atributos requeridos u opcionales que fueron 
    * configurados previamente.
    *
    * @access private
    * @return array Parametros que entiende la extension SREG para solicitar atributos
    */

    private function sregParams()
    {
        $params = array();

        if ( !empty($this->required) || !empty($this->optional)) 
        {
            // Siempre usamos SREG 1.1, incluso si el servidor está anunciando que solo soporta 1.0. 
            // Esto es porque SREG 1.1 es totalmente compatibile con 1.0, y algunos proveedores 
            // anuncian 1.0, incluso si aceptan sólo 1.1. Un proveedor por ejemplo es myopenid.com

            $params['openid.ns.sreg'] = 'http://openid.net/extensions/sreg/1.1';

            if ( !empty($this->required))
            {
                $params['openid.sreg.required'] = array();
                foreach ($this->required as $required)
                {
                    if (isset(self::$ax_to_sreg[$required]))
                    {
                        $params['openid.sreg.required'][] = self::$ax_to_sreg[$required];
                    }
                }
                $params['openid.sreg.required'] = implode(',', $params['openid.sreg.required']);
            }

            if ( !empty($this->optional))
            {
                $params['openid.sreg.optional'] = array();
                foreach ($this->optional as $optional)
                {
                    if (isset(self::$ax_to_sreg[$optional]))
                    {
                        $params['openid.sreg.optional'][] = self::$ax_to_sreg[$optional];
                    }
                }
                $params['openid.sreg.optional'] = implode(',', $params['openid.sreg.optional']);
            }
        }

        return $params;
    }

    /**
    * Arma el arreglo con los parametros necesarios que entiende la extension AX para 
    * solicitar al proveedor de identidad los atributos requeridos u opcionales que fueron 
    * configurados previamente.
    *
    * @access private
    * @return array Parametros que entiende la extension AX para solicitar atributos
    */

    private function axParams()
    {
        $params = array();

        if ( !empty($this->required) || !empty($this->optional)) 
        {
            $params['openid.ns.ax'] = 'http://openid.net/srv/ax/1.0';
            $params['openid.ax.mode'] = 'fetch_request';

            $aliases  = array();
            $counts   = array();

            if ( !empty($this->required))
            {
                $required = array();
                foreach ($this->required as $alias => $field) 
                {
                    if (is_int($alias))
                    {
                        $alias = strtr($field, '/', '_'); //reemplaza la / por el _
                    }
                    $aliases[$alias] = 'http://axschema.org/' . $field;
                    if (empty($counts[$alias]))
                    {
                        $counts[$alias] = 0;
                    }
                    $counts[$alias] += 1;
                    $required[] = $alias;
                }

                $params['openid.ax.required'] = implode(',', $required);

            }
            
            if ( !empty($this->optional))
            {
                $optional = array();
                foreach ($this->optional as $alias => $field) 
                {
                    if (is_int($alias))
                    {
                        $alias = strtr($field, '/', '_'); //reemplaza la / por el _
                    }
                    $aliases[$alias] = 'http://axschema.org/' . $field;
                    if (empty($counts[$alias]))
                    {
                        $counts[$alias] = 0;
                    }
                    $counts[$alias] += 1;
                    $optional[] = $alias;
                }

                $params['openid.ax.if_available'] = implode(',', $optional);
            }

            foreach ($aliases as $alias => $ns) 
            {
                $params['openid.ax.type.' . $alias] = $ns;
            }

            foreach ($counts as $alias => $count) 
            {
                if ($count != 1)
                {
                    $params['openid.ax.count.' . $alias] = $count;
                }
            }

        }

        return $params;
    }

    /**
    * Forma la URL para servidores que implementen la version 1 de OpenID a la cual se va a 
    * redirigir el navegador para que el proveedor de identidad confirme la identidad reclamada 
    *
    * @access private
    * @return string URL del servidor OpenID con todos los parametros necesarios
    */

    private function authUrl_v1()
    {
        $returnUrl = $this->returnUrl;

        // Si tenemos un openid.delegate que es diferente de nuestra identificación reclamada, tenemos 
        // que preservar de alguna manera la identificación reclamada entre las peticiones. La forma más 
        // sencilla es simplemente envirla como parametro dentro de la URL de retorno

        if($this->identity != $this->claimed_id)
        {
            $returnUrl .= $this->my_http_build_query(array('openid.claimed_id' => $this->claimed_id), '&', $returnUrl, '?');
        }

        $params = array(
            'openid.return_to'  => $returnUrl,
            'openid.mode'       => ($this->immediate) ? 'checkid_immediate' : 'checkid_setup',
            'openid.identity'   => $this->identity,
            'openid.trust_root' => $this->trustRoot,
        );

        $params += $this->sregParams();

        return($this->my_http_build_query($params, '&', $this->server, '?'));
    }

    /**
    * Forma la URL para servidores que implementen la version 2 de OpenID a la cual se va a 
    * redirigir el navegador para que el proveedor de identidad confirme la identidad reclamada 
    *
    * @access private
    * @return string URL del servidor OpenID con todos los parametros necesarios
    */

    private function authUrl_v2()
    {
        $params = array(
            'openid.ns'          => 'http://specs.openid.net/auth/2.0',
            'openid.mode'        => ($this->immediate) ? 'checkid_immediate' : 'checkid_setup',
            'openid.return_to'   => $this->returnUrl,
            'openid.realm'       => $this->trustRoot
        );

        if ($this->ax) 
        {
            $params += $this->axParams();
        }
        if ($this->sreg) 
        {
            $params += $this->sregParams();
        }
        if (!$this->ax && !$this->sreg) 
        {   
            // Si el proveedor de identidad no soporta SREG ni AX, igual vamos a enviar ambos. En el 
            // peor de los casos no recibimos nada en la respuesta

            $params += $this->axParams() + $this->sregParams();
        }

        if ($this->identifier_select)
        {
            $params['openid.identity']   = 'http://specs.openid.net/auth/2.0/identifier_select';
            $params['openid.claimed_id'] = 'http://specs.openid.net/auth/2.0/identifier_select';
        }
        else
        {
            $params['openid.identity']   = $this->identity;
            $params['openid.claimed_id'] = $this->claimed_id;
        }

        return($this->my_http_build_query($params, '&', $this->server, '?'));
    }

    /**
    * Realiza el llamado de todos los metodos necesarios por ESLIP para realizar la identificacion  
    * del usuario. Si todo el proceso se realiza satisfactoriamente se redirige a un script el 
    * cual postea los datos obtenidos del proveedor de identidad a la URL configurada.
    *
    * @access public
    */

    public function Process()
    {
        $this->Authenticate();
        
        if($this->ExitProgram())
        {
            exit;
        }
        
        $user = $this->getAttributes();
        
        $this->StoreUserDataInSession($user, 'openid');

        $return = array('user' => $user,
                        'user_identification' => $user['id'],
                        'server' => 'openid', 
                        'referer' => $this->referer,
                        'state' => 'success',
                        'client_callback_url' => (string)$this->eslip->configuration->callbackUrl);

        $return = base64url_encode(json_encode($return));

        $callback_url_preocess = str_replace('{DATA}', $return, (string)$this->eslip->configuration->pluginUrl."eslip_callback_process.php?data={DATA}");

        close_and_redirect_parent_window($callback_url_preocess);
    }

    /**
    * Realiza el procesamiento de la interaccion entre el plugin y el servidor OpenID, de acuerdo a 
    * la especificacion del protocolo OpenID
    *
    * @access public
    * @throws EslipException Si el proveedor de identidad devuelve el parametro modo con el valor 'cancel'
    * @throws EslipException Si el proveedor de identidad devuelve el parametro modo con un valor distinto a 'id_res'
    * @return boolean TRUE si se termina la ejecucion del metodo
    */

    public function Authenticate()
    {
        if( empty($this->mode) )
        {
            if( $this->identity )
            {   
                $this->discover($this->identity);

                header( 'Location: ' . $this->authUrl() );
                $this->exit = TRUE;
                return(TRUE);
            }
            else
            {
                header( 'Location: ' . $this->openid_form_url );
                $this->exit = TRUE;
                return(TRUE);
            }
        }
        else
        {
            if ($this->mode == 'cancel')
            {
                throw new EslipException(CancelInfo);
            }

            if(isset($this->data['openid_user_setup_url']) || $this->mode == 'setup_needed')
            {

                $this->immediate = FALSE;

                if (isset($this->data['openid_user_setup_url']))
                {
                    $setup_url = $this->data['openid_user_setup_url'];
                }
                else
                {
                    $this->discover($this->identity);
                    
                    $setup_url = $this->authUrl();
                }

                header( "Refresh:2; Url=".$setup_url ); 
                echo ImmediateRedirecting;

                // header( 'Location: ' . $setup_url );

                $this->exit = TRUE;
                return(TRUE);
            }

            if($this->mode != 'id_res') 
            {
                throw new EslipException(NoIdResError);
            }

            $this->validate();
        }
    }

    /**
    * Devoluelve la URL de autenticación a la cual se va a redirigir al usuario para que el 
    * proveedor de identidad confirme la identidad reclamada
    *
    * @access private
    * @return string URL del servidor OpenID con todos los parametros necesarios de acuerdo a la version del servidor
    */

    private function authUrl()
    {
        if ($this->version == 2)
        {
            return $this->authUrl_v2();
        }
        return $this->authUrl_v1();
    }

    /**
    * Realiza la verificación de OpenID con el proveedor de identidad. Valida la autenticacion realizada
    * en el proveedor de identidad.
    *
    * @access private
    * @return boolean Si la verificación se ha realizado correctamente.
    * @throws EslipException Si el parametro 'return_to' devuelto luego de la autencacion por el proveedor de identidad coincide que el configurado previamente
    */

    private function validate()
    {
        $this->claimed_id = isset($this->data['openid_claimed_id']) ? $this->data['openid_claimed_id'] : $this->data['openid_identity'];

        $params = array(
            'openid.assoc_handle' => $this->data['openid_assoc_handle'],
            'openid.signed'       => $this->data['openid_signed'],
            'openid.sig'          => $this->data['openid_sig']
        );

        if (isset($this->data['openid_ns'])) 
        {
            // Estamos tratando con un servidor de OpenID 2.0, así que vamos a fijar un ns. 
            // A pesar de que debemos saber la ubicación del punto final, todavía tenemos que 
            // verificarlo mediante el descubrimiento, por lo que $server no será configurado aquí

            $params['openid.ns'] = 'http://specs.openid.net/auth/2.0';
        }
        elseif (isset($this->data['openid_claimed_id']) && $this->data['openid_claimed_id'] != $this->data['openid_identity'])
        {
            // Si se trata de un proveedor de OpenID 1, y tenemos claimed_id, tenemos que añadirlo a 
            // la URL de retorno, como authUrl_v1 hace.

            $this->returnUrl .= $this->my_http_build_query(array('openid.claimed_id' => $this->claimed_id), '&', $this->returnUrl, '?');
        }

        // La URL de retorno debe coincidir con la url de la petición actual. 
        if ($this->data['openid_return_to'] != $this->returnUrl) 
        {
            throw new EslipException(DifferentsReturnURLError);
        }

        $server = $this->discover($this->claimed_id);

        foreach (explode(',', $this->data['openid_signed']) as $item)
        {
            // Comprobar si magic_quotes_gpc está activada, ya que la función puede fallar si lo esta. 
            // Uno de los recursos solicitados podría contener un apóstrofe, que se escapó. En tal caso, 
            // la validación fallará, ya que enviaríamos diferentes datos de los que el Proveedor de
            // Identidad quiere verificar. stripslashes() deben resolver este problema, pero no podemos 
            // usarlo cuando magic_quotes está apagado.

            $value = $this->data['openid_' . str_replace('.','_',$item)];
            $params['openid.' . $item] = function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() ? stripslashes($value) : $value;
        }

        $params['openid.mode'] = 'check_authentication';

        $response = $this->request($server, 'POST', $params);

        return preg_match('/is_valid\s*:\s*true/i', $response['content']);
    }

    /**
    * Analiza los parametros devueltos por el proveedor de identidad correspondientes 
    * a recursos solicitados. Analiza los parametros referentes a la extension AX 
    *
    * @access private
    * @return array Recursos devueltos por el proveedor de identidad referentes a la extension AX
    */

    private function getAxAttributes()
    {
        $alias = NULL;

        // Es el caso más probable, así que vamos a comprobarlo antes
        if (isset($this->data['openid_ns_ax']) && $this->data['openid_ns_ax'] != 'http://openid.net/srv/ax/1.0')
        {
            $alias = 'ax';
        }
        else
        {
            // El prefijo 'AX' no esta definido, o apunta a otra extensión, así que se busca otro prefijo
            foreach ($this->data as $key => $val)
            {
                if (substr($key, 0, strlen('openid_ns_')) == 'openid_ns_' && $val == 'http://openid.net/srv/ax/1.0')
                {
                    $alias = substr($key, strlen('openid_ns_'));
                    break;
                }
            }
        }

        $attributes = array();

        if ($alias)
        {
            foreach (explode(',', $this->data['openid_signed']) as $key)
            {
                $keyMatch = $alias . '.value.';           

                if (substr($key, 0, strlen($keyMatch)) == $keyMatch)
                {
                    $key = substr($key, strlen($keyMatch));

                    if (isset($this->data['openid_' . $alias . '_value_' . $key]))
                    {
                        $value = $this->data['openid_' . $alias . '_value_' . $key];
                        $attributes[$key] = $value;
                    }
                }
            }    
        }
        
        return $attributes;
    }

    /**
    * Analiza los parametros devueltos por el proveedor de identidad correspondientes 
    * a recursos solicitados. Analiza los parametros referentes a la extension SREG 
    *
    * @access private
    * @return array Recursos devueltos por el proveedor de identidad referentes a la extension SREG 
    */

    private function getSregAttributes()
    {
        $attributes = array();
        $sreg_to_ax = array_flip(self::$ax_to_sreg); // array_flip — Intercambia todas las keys con sus valores asociados en un array
        foreach (explode(',', $this->data['openid_signed']) as $key)
        {
            $keyMatch = 'sreg.';

            if (substr($key, 0, strlen($keyMatch)) == $keyMatch) 
            {
                $key = substr($key, strlen($keyMatch));

                if (isset($sreg_to_ax[$key])) 
                {
                    $attributes[$sreg_to_ax[$key]] = $this->data['openid_sreg_' . $key];    
                }

            }           
        }

        return $attributes;
    }

    /**
    * Obtiene los atributos tanto de la extension AX como de SREG devueltas por el proveedor de identidad. 
    * Debe utilizarse sólo después de que se haya validado con éxito la autenticacion. Hay que tener en 
    * cuenta que esto no garantiza que cualquiera de los parámetros necesarios u opcionales van a estar
    * presentes, o que no habrá otros atributos además de los especificados. En otras palabras, el 
    * proveedor de identidad puede proporcionar toda la información que quiera.
    *
    * @access public
    * @return array Recursos devueltos por el proveedor de identidad
    */

    public function getAttributes()
    {
        $attributes = array();

        $attributes['id'] = $this->data['openid_claimed_id'];

        if (isset($this->data['openid_ns']) && $this->data['openid_ns'] == 'http://specs.openid.net/auth/2.0')
        { 
            // OpenID 2.0
            // Buscamos tanto atributos AX y SREG, con AX como prioridad.
            $attributes += $this->getAxAttributes() + $this->getSregAttributes();
        }
        else
        {
            // OpenID 1.0
            $attributes += $this->getSregAttributes();
        }

        return $attributes; 
    }
}

if (IsSet($_GET['eslip_data']))
{
    $aux = json_decode(base64url_decode($_GET['eslip_data']));
    $referer = $aux->referer;
}
else
{
    $referer = (IsSet($_GET['referer'])) ? $_GET['referer'] : '';
}

try
{
    new eslip_openid($eslip, $referer);
}
catch (EslipException $e)
{
    $return = array('error' => $e->getMessage(), 
                            'server' => 'openid', 
                            'referer' => $referer,
                            'state' => 'error',
                            'client_callback_url' => (string)$eslip->configuration->callbackUrl);

    $return = base64url_encode(json_encode($return));

    $callback_url_preocess = str_replace('{DATA}', $return, $eslip->configuration->pluginUrl."eslip_callback_process.php?data={DATA}");

    close_and_redirect_parent_window($callback_url_preocess);
}
catch (OpenIDException $e)
{
    $eslip_data = base64url_encode(json_encode(array('referer' => $referer )));

    $returnUrl = str_replace('{ESLIP_DATA}', $eslip_data, (string)$eslip->configuration->pluginUrl."eslip_openid.php?eslip_data={ESLIP_DATA}" );

    $openid_form_url = $eslip->configuration->pluginUrl."frontend/openid_form.php?return_url=".$returnUrl."&error=".$e->getMessage();

    header( 'Location: ' . $openid_form_url );
}