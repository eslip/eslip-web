<?php
include_once('eslip.php');
include_once('eslip_protocol.php');

/**
* ESLIP OAuth
*
* Esta clase implementa el protocolo OAuth para autenticar a un usuario 
* intercambiando mensajes con las API's de los Proveedores de Identidad.
* Esta clase soporta las versiones 1.0,  1.0a y 2.0 de OAuth. 
*
* @author Nicolás Burghi [nicoburghi@gmail.com]
* @author Martín Estigarribia [martinestiga@gmail.com]
* @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
* @package Eslip
*/

class eslip_oauth extends Eslip_protocol
{
    /**
    * Identificador del proveedor de identidad elegido por el usuario
    *
    * @var string
    * @access private
    */

    private $server;

    /**
    * URL del servidor OAuth para solicitar el token inicial cuando trabajamos con servidores OAuth 1.0 y 1.0a.
    * Es inicializada por el constructor de la clase con la URL que ha sido establecida en la configuracion 
    * para el proveedor de identidad correspondiente
    *
    * @var string 
    * @access private
    */

	private $request_token_url = '';

    /**
    * URL del formulario de login del proveedor de identidad. Se redirigirá al propietario del recurso 
	* a dicha URL para que inicie sesión en el proveedor de identidad correspondiente y autorice o no a 
	* la aplicación cliente
    *
    * @var string 
    * @access private
    */

	private $dialog_url = '';

	/**
    * URL del proveedor de identidad que retornará el access token
    *
    * @var string 
    * @access private
    */

	private $access_token_url = '';

	/**
    * Versión del protocolo que soporta el servidor OAuth del proveedor de identidad con la que 
    * se está interactuando
    *
    * @var string 
    * @access private
    */

	private $oauth_version = '2.0';

	/**
    * Determina si la API del proveedor de identidad con la que se está interactuando requiere a
    * la hora de realizar la peticion del token inicial que se le envien los parametros en la URL. 
    * Esta variable solo se utiliza para servidores OAuth 1.0 ya que 2.O siempre los pasa por la URL
    *
    * @var boolean 
    * @access private
    */

	private $url_parameters = FALSE;

	/**
    * Determina si la API del proveedor de identidad con la que se está interactuando requiere a
    * la hora de realizar la peticion del token inicial que se le envien los parametros en la cabecera
    * de la petición HTTP
    *
    * @var boolean 
    * @access private
    */	

	private $authorization_header = TRUE;

	/**
    * URL a la que retornará la API OAuth con la que estamos interactuando. Debe ser la URL del script 
    * que procesa la respuesta de la API
    *
    * @var string 
    * @access private
    */

	private $redirect_uri = '';

	/**
    * Identificador provisto por la API OAuth de la aplicación creada
    *
    * @var string 
    * @access private
    */

	private $client_id = '';

	/**
    * Clave secreta provista por la API OAuth para la aplicación creada
    *
    * @var string 
    * @access private
    */

	private $client_secret = '';

	/**
    * Recursos que se desean obtener y que su propietario quien se autentica a traves del proveedor 
    * de identidad debe autorizar para que sean concedidos
    *
    * @var string 
    * @access private
    */

	private $scope = '';

	/**
    * Access token obtenido de la API OAuth con la que se está intercactuando
    *
    * @var string 
    * @access private
    */

	private $access_token = '';

	/**
    * Access token secret obtenido de la API OAuth con la que se está intercactuando
    *
    * @var string 
    * @access private
    */

	private $access_token_secret = '';

	/**
    * Fecha y hora en la que el access token expira
    *
    * @var string 
    * @access private
    */

	private $access_token_expiry = '';

	/**
    * Señala si la API OAuth del proveedor de identidad con el que se está interactuando devuelve un 
    * parametro extra en la respuesta a la peticion de access token necesario para la autenticacion
    *
    * @var boolean 
    * @access private
    */

	private $has_access_token_extra_parameter = FALSE;

	/**
    * Nombre de la llave dentro de la respuesta a la petición de access token para acceder al parametro extra
    * en caso de que el proveedor de identidad con el que se está interactuando devuelva uno
    *
    * @var string 
    * @access private
    */

	private $access_token_extra_parameter_name = '';

	/**
    * Contenido del parametro extra
    *
    * @var string 
    * @access private
    */

	private $access_token_extra_parameter = '';

	/**
    * URL que proporciona la API OAuth del proveedor de identidad con el que se está interactuando 
    * a la cual se le realiza la petición para obtener los recursos requeridos
    *
    * @var string 
    * @access private
    */

	private $user_data_url = '';

	/**
	* Nombre de la llave dentro de la respuesta a la petición de recursos para acceder al identificador unico
    * del propietario de los recursos
    *
    * @var string 
    * @access private
    */

	private $user_data_id_key = '';

    /**
    * Cadena con los parametros que se le envian a la API del proveedor de identidad, entre los cuales debe estar
    * el access token
    *
    * @var string 
    * @access private
    */

    private $api_call_parameters = '';

	/**
    * Metodo constructor de la clase. Se inicializan las variables de configuración del proveedor de
    * identidad con el que se va a interactuar
    *
    * @access public
    * @param object $eslip Instancia de la clase Eslip
    * @param string $server Identificador del proveedor de identidad elegido por el usuario
    * @param string $referer URL de la cual proviene el llamado al plugin para luego ser enviado de vuelta 
    * al sitio para que el desarrollador pueda redirigir
    */

    public function __construct($eslip, $server, $referer)
    {
    	parent::__construct();

    	$this->server = $server;
		
		$this->referer = $referer;

		$this->eslip = $eslip;

		$eslip_data = base64url_encode(json_encode(array('server' => $this->server, 'referer' => $this->referer )));

		$this->redirect_uri = str_replace('{ESLIP_DATA}', $eslip_data, (string)$this->eslip->configuration->pluginUrl."eslip_oauth.php?eslip_data={ESLIP_DATA}" );

		$identity_provider = $this->eslip->xmlApi->getElementById("identityProvider", $this->server);

		if (is_array($identity_provider))
		{
			$identity_provider = $this->Array2Object($identity_provider);
		}

		$this->client_id = (string)$identity_provider->clientId;
		$this->client_secret = (string)$identity_provider->clientSecret;
		$this->scope = (string)$identity_provider->scope;

		$this->oauth_version = (string)$identity_provider->oauth;
		$this->request_token_url = (string)$identity_provider->requestTokenUrl;
		$this->dialog_url = (string)$identity_provider->dialogUrl;
		$this->access_token_url = (string)$identity_provider->accessTokenUrl;
		$this->authorization_header = ((string)$identity_provider->authorizationHeader == '1') ? TRUE : FALSE ;
		$this->url_parameters = ((string)$identity_provider->urlParameters == '1') ? TRUE : FALSE ;
		$this->has_access_token_extra_parameter = ((string)$identity_provider->hasAccessTokenExtraParameter == '1') ? TRUE : FALSE ;
		$this->access_token_extra_parameter_name = (string)$identity_provider->accessTokenExtraParameterName;
        $this->api_call_parameters = (string)$identity_provider->apiCallParameters;

		$this->user_data_url = (string)$identity_provider->userDataUrl;

		$this->user_data_id_key = (string)$identity_provider->userDataIdKey;

		$this->process();
    }

    /**
    * Devuelve el estado creado y almacenado en la sesion o crea uno y lo almacena en caso de que no exista todavia.
    * El estado es una cadena aleatoria que será usada para prevenir ataques del tipo CSRF (Cross-Site Request Forgery)
    *
    * @access private
    * @return string Cadena de estado alamacenada en la sesion
    */

	private function GetStoredState()
	{
		if( ! IsSet($_SESSION['OAUTH']['OAUTH_STATE']))
			$_SESSION['OAUTH']['OAUTH_STATE'] = time().'-'.substr(md5(rand().time()), 0, 6);
			
		return ($_SESSION['OAUTH']['OAUTH_STATE']);
	}

	/**
    * Devuelve el parametro state incluido en la URL (redirect_uri) a la cual la API OAuth del proveedor de identidad 
    * con el que se está interactuando retorna luego de la autenticacion.
    *
    * @access private
    * @return string Parametro state devuelto en la URL
    */

	private function GetRequestState()
	{
		return (IsSet($_GET['state']) ? $_GET['state'] : FALSE);
	}

	/**
    * Devuelve el authorization code incluido en la URL (redirect_uri) a la cual la API OAuth del proveedor de identidad 
    * con el que se está interactuando retorna luego de la autenticacion. El authorization code luego se utiliza
    * para obtener un token de acceso
    *
    * @access private
    * @return string Parametro code (authorization code) devuelto en la URL
    */

	private function GetRequestCode()
	{
		return (IsSet($_GET['code']) ? $_GET['code'] : FALSE);
	}

	/**
    * Devuelve el error incluido en la URL (redirect_uri) a la cual la API OAuth del proveedor de identidad 
    * con el que se está interactuando retorna luego de la autenticacion en caso de que haya habido un error en
    * dicha autenticacion
    *
    * @access private
    * @return string Parametro error devuelto en la URL
    */

	private function GetRequestError()
	{
		return (IsSet($_GET['error']) ? $_GET['error'] : NULL);
	}

	/**
    * Devuelve el parametro denied incluido en la respuesta (redirect_uri) a la cual la API OAuth del proveedor de identidad 
    * con el que se está interactuando retorna luego de la autenticacion en caso de que el propietario de los recursos
    * no haya autorizado a su acceso a la aplicacion cliente
    *
    * @access private
    * @return string Parametro denied devuelto en la URL
    */

	private function GetRequestDenied()
	{
		return (IsSet($_GET['denied']) ? $_GET['denied'] : FALSE);
	}

	/**
    * Devuelve los parametros Request Token y Verifier incluidos en la URL (redirect_uri) a la cual la API OAuth 
    * del proveedor de identidad con el que se está interactuando retorna luego de su peticion
    *
    * @access private
    * @return array Parametros Request Token y Verifier devueltos en la URL
    */

	private function GetRequestToken()
	{
		$request_token['token'] = (IsSet($_GET['oauth_token']) ? $_GET['oauth_token'] : NULL);
		$request_token['verifier'] = (IsSet($_GET['oauth_verifier']) ? $_GET['oauth_verifier'] : NULL);
		return($request_token);
	}

	/**
    * Almacena en la sesion el token de acceso proporcionado por parametro
    *
    * @access private
    * @param array $access_token Arreglo con las propiedades del token de acceso
    */

	private function StoreAccessToken($access_token)
	{
		$_SESSION['OAUTH']['OAUTH_ACCESS_TOKEN'][$this->access_token_url] = $access_token;
	}

	/**
    * Retorna el token de acceso almacenado previamente en la sesion. Si todavia no fue almacenado
    * retorna un arreglo vacio.
    *
    * @access private
    * @return array Arreglo con las propiedades del token de acceso almacenado en la sesion
    */

	private function GetAccessToken()
	{
		if(IsSet($_SESSION['OAUTH']['OAUTH_ACCESS_TOKEN'][$this->access_token_url]))
		{
			$access_token = $_SESSION['OAUTH']['OAUTH_ACCESS_TOKEN'][$this->access_token_url];
		}
		else
		{
			$access_token = array();
		}
		return($access_token);
	}

	/**
    * Obtiene el identificador del usuario cuyos datos son pasados por parametro. El nombre de la llave
    * del arreglo que es considerado identificador debe ser previamente configurado e inicializado en el 
    * contstructor.
    *
    * @access public
    * @param array $user_data Arreglo con los recursos obtenidos del proveedor de identidad
    * @return string Identificador obtenido del arreglo de datos proporcionado
    */

	public function GetUserId($user_data)
	{
		$id = '';
		if ( !empty($this->user_data_id_key))
		{
			$aux = $this->GetElementOfKey($this->user_data_id_key, $user_data);
			$id = ($aux != FALSE) ? $aux : '';
		}
		return $id;

	}

	/**
    * Busca recursivamente una llave en un arreglo. En caso de encontrarla se devuelve su contenido.
    *
    * @access private
    * @param string $key Llave que se quiere buscar para devolver su contenido
    * @param array $array Arreglo en el cual se quiere buscar el contenido en la llave proporcionada
    * @return Elemento del arreglo en caso de que exista la llave o FALSE en caso de que no exista
    */

	private function GetElementOfKey($key, $array)
	{
		foreach ($array as $key_actual => $value)
		{
			if ($key_actual === $key)
			{
				return $value;
			}
			elseif (is_array($value))
			{
				$return = $this->GetElementOfKey($key, $value);
				if ($return !== FALSE )
				{

					return $return;
				}
			}
		}
		return(FALSE);
    }

    /**
    * Realiza una peticion a la API Oauth del proveedor de identidad con el que se esta interactuando.
    * La URL con la que se desea comunicar, y los parametros que se utilizan son recibidos por este metodo
    * por parametro.
    * Dependiendo de la version del servidor OAuth, será el tratamiento que se le realizarán a los
    * parametros para luego ser enviados. 
    * La conexion con el servidor OAuth se realiza utlizando la libreria cURL.
    *
    * @access private
    * @param string $url URL de la API OAuth con la que nos vamos a comunicar
    * @param string $method Metodo HTTP que se va a utilizar para comunicarse con el servidor
    * @param array $parameters Parametros que se van a enviar en la petición
    * @param array $oauth Parametros especificos de OAuth 1.0 que se van a enviar en la petición
    * @param array $options Informacion adicional para la interfaz o manejo de errores
    * @return array Arreglo con los datos devueltos por el servidor OAuth
    */

	private function SendAPIRequest($url, $method, $parameters, $oauth, $options)
	{
		$config['method'] = strtoupper($method);
		
        $config['curl_followlocation'] = FALSE;
	        
        $config['curl_ssl_verifypeer'] = TRUE;
        $config['curl_ssl_verifyhost'] = 2;
		
        $config['curl_cainfo'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'certs' . DIRECTORY_SEPARATOR . 'cacert.pem';
        $config['curl_capath'] = dirname(__FILE__);
		
		$config['curl_header'] = FALSE;
		$config['curl_returntransfer'] = TRUE;
		
		$config['curl_httpheader'][] = 'Accept: */*';
			
		if(IsSet($oauth))
		{
			// significa que es OAuth 1.0 y pueden venir las llaves oauth_callback, oauth_token o oauth_verifier

			$requestTokenURL = $url;

			$values = array(
				'oauth_consumer_key'=>$this->client_id,
				'oauth_nonce'=>md5(uniqid(rand(), TRUE)),
				'oauth_signature_method'=>'HMAC-SHA1',
				'oauth_timestamp'=>time(),
				'oauth_version'=>'1.0'
			);

			if($this->url_parameters && count($parameters))
			{
				// Es OAuth 1.0 y pasa los parametros a traves de la URL, OAuth 2.0 siempre los pasa traves de la URL
				$url = $this->my_http_build_query($parameters, '&', $url, '?');
				$parameters = array();
			}
			
			$values = array_merge($values, $oauth, $parameters);
			
			$signatureParameters = $values;
			
			$u = parse_url($requestTokenURL); 					// Nos quedamos con los parametros de la requestTokenURL
			if(IsSet($u['query']))								// Si tiene parametros
			{
				parse_str($u['query'], $q);
				foreach($q as $parameter => $value)
				{
					$signatureParameters[$parameter] = $value; 	// Le agregamos esos parametros a los signatureParameters
				}
					
			}
			
			KSort($signatureParameters);
							
			$__requestTokenURL = strtok($requestTokenURL, '?');	// Nos quedamos con la requestTokenURL sin parametros
			
			$sign = $this->rfc3986_encode($config['method']) . "&".  $this->rfc3986_encode($__requestTokenURL) . "&" . $this->rfc3986_encode($this->my_http_build_query($signatureParameters));
						
			$client_secret = $this->rfc3986_encode($this->client_secret);
			$access_token_secret = $this->rfc3986_encode($this->access_token_secret);
						
			$key = $client_secret ."&". $access_token_secret;  
			
			$signature = base64_encode(hash_hmac('sha1', $sign, $key, TRUE));  
			$RFC3986signature = $this->rfc3986_encode($signature);  
			
			$values['oauth_signature'] = $signature;
			
			if($this->authorization_header)
			{
				$_authorization = 'Authorization: OAuth '.$this->my_http_build_query($values, ',', '', '', TRUE);

				$config['curl_httpheader'][] = $_authorization;
				$config['curl_httpheader'][] = 'Expect:';
			}
			else
			{
				if($config['method'] == 'GET')
				{
					$url = $this->my_http_build_query($values, '&', $url, '?');
				}
			}
		}

		$config['url'] = $url;
		$config['parameters'] = $parameters;

		$curl_response = $this->request_curl($config);
		
		if($curl_response['code'] >= 200 && $curl_response['code'] < 300)
		{
			$content_type = $curl_response['content_type'];

			switch($content_type)
			{
				case 'text/javascript':
				case 'application/json':
				case 'application/x-www-form-urlencoded':
				case 'text/plain':
				case 'text/html':
					break;
				default:
					$content_type = strtolower(trim(substr($content_type,0,strpos($content_type,';'))));
					break;
			}

			switch($content_type)
			{
				case 'text/javascript':
				case 'application/json':
					$response = json_decode($curl_response['response'], TRUE);
				break;
				case 'application/x-www-form-urlencoded':
				case 'text/plain':
				case 'text/html':
					parse_str($curl_response['response'], $response);
					break;
				default:
					$response = $curl_response['response'];
				break;
			}

			return($response);
		}
		else
		{
			throw new EslipException(sprintf(CurlResponseError,$options['Resource'], $curl_response['code'], $curl_response['response']));
		}
	}

	/**
    * Realiza una peticion de los recursos del usuario a la API Oauth del proveedor de identidad con el que se 
    * esta interactuando. Para poder realizar este pedido previamente se debe haber obtenido el token de accesp
    * correspondiente
    * La URL a la que se debe comunicar, y los parametros que se utilizan fueron previamente obtenidos y configurados
    * en el plugin. Dependiendo de la version del servidor OAuth, será el tratamiento que se le realizarán a los
    * parametros para luego ser enviados. 
    *
    * @access public
	* @throws EslipException Si el servidor OAuth del proveedor de identidad posee una version no soportada por el plugin
	* @throws EslipException Si todavia no se obtuvo el access token para poder hacer el pedido de recursos
    * @return array Arreglo con los recursos del usuario devueltos por el servidor OAuth
    */

	public function GetUserData()
	{
		if( ! empty($this->access_token))
		{
			if($this->has_access_token_extra_parameter)
			{
				$this->user_data_url = str_replace('{EXTRA_PARAMETER}', UrlEncode($this->access_token_extra_parameter), $this->user_data_url); 
			}

			$url = $this->user_data_url;

			$parameters = array();

			$options['Resource'] = 'API call';

			switch(intval($this->oauth_version))
			{
				case 1:
					$oauth = array(
						'oauth_token'=>$this->access_token
					);
					break;

				case 2:
					$oauth = NULL;

                    $this->api_call_parameters = str_replace('{ACCESS_TOKEN}', $this->access_token, $this->api_call_parameters); 

                    $parameters = array();

                    parse_str($this->api_call_parameters, $parameters);

                    if(empty($this->api_call_parameters))
                    {
                        $parameters = array('access_token' => $this->access_token);
                    }

                    $url = $this->my_http_build_query($parameters, '&', $url, '?');  
					
					break;

				default:
					throw new EslipException(sprintf(OAuthVersionError, $this->oauth_version));
			}

			return($this->SendAPIRequest($url, 'GET', $parameters, $oauth, $options));
		}
		else
		{
			throw new EslipException(NoAccessTokenError);
		}
	}

	/**
    * Realiza el llamado de todos los metodos necesarios por ESLIP para realizar la identificacion y autorizacion 
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

		$user = $this->GetUserData();

		$this->StoreUserDataInSession($user, 'oauth');

		$return = array('user' => $user,
						'user_identification' => $this->GetUserId($user),
						'server' => $this->server, 
						'referer' => $this->referer,
						'state' => 'success',
						'client_callback_url' => (string)$this->eslip->configuration->callbackUrl);

		$return = base64url_encode(json_encode($return));

		$callback_url_preocess = str_replace('{DATA}', $return, (string)$this->eslip->configuration->pluginUrl."eslip_callback_process.php?data={DATA}");

		close_and_redirect_parent_window($callback_url_preocess);
	}

	/**
    * Realiza el procesamiento de la interaccion entre el plugin y el servidor OAuth, de acuerdo a la especificacion
    * del protocolo OAuth
    *
    * @access public
	* @throws EslipException Si la peticion de token de acceso fue denegada o no autorizada
	* @throws EslipException Si en la respuesta a la peticion de token de acceso no viene el token de acceso
	* @throws EslipException Si expiró el token de acceso obtenido
	* @throws EslipException Si el servidor OAuth del proveedor de identidad posee una version no soportada por el plugin
    * @return boolean TRUE si el procesamiento se realiza correctamente y no ocurre ningun error
    */	

	public function Authenticate()
	{
		switch(intval($this->oauth_version))
		{
			case 1:
				$one_a = ($this->oauth_version === '1.0a');

				$access_token = $this->GetAccessToken();  // El access token se guarda en la sesion, nos fijamos si está ahí

				if(IsSet($access_token['authorized']) && IsSet($access_token['value']))
				{
					$expired = (IsSet($access_token['expiry']) && strcmp($access_token['expiry'], gmstrftime('%Y-%m-%d %H:%M:%S')) <= 0);
					
					if(!$access_token['authorized']	|| $expired) // Si la que tenemos no está autorizada o ya expiró
					{
						$request_token = $this->GetRequestToken(); // Nos fijamos si viene en la URL
										
						if(!IsSet($request_token['token']) || ($one_a && !IsSet($request_token['verifier']))) // Me fijo si vino denegada o no vino
						{
							$denied = $this->GetRequestDenied();

							if($denied === $access_token['value']) // Nos fijamos si vino denegada
							{
								throw new EslipException(RequestTokenDeniedError);
							}
							else
							{ 
								$access_token = array(); // No vino, por lo tanto reseteamos el estado como vacio.
							}
						}
						elseif($request_token['token'] !== $access_token['value']) // Nos fijamos si vino en la URL otro distino
						{
							$access_token = array();  // Restablecemos el estado porque el token no coincide con el obtenido anteriormente
						}
						else // Si vino el mismo pero no está autorizado o ya expiro, entonces pedimos uno nuevo
						{
							$url = $this->access_token_url;
							
							$oauth = array(
								'oauth_token'=>$request_token['token'],
							);

							if($one_a)
							{
								$oauth['oauth_verifier'] = $request_token['verifier'];
							}	

							$this->access_token_secret = $access_token['secret'];
							
							$response = $this->SendAPIRequest($url, 'GET', array(), $oauth, array('Resource'=>'OAuth access token'));

							if(!IsSet($response['oauth_token'])	|| !IsSet($response['oauth_token_secret']))
							{
								throw new EslipException(NotReturnedAccessTokenError);
							}

							$access_token = array(
								'value'=>$response['oauth_token'],
								'secret'=>$response['oauth_token_secret'],
								'authorized'=>true
							);
							
							if(IsSet($response['oauth_expires_in']))
							{
								$expires = $response['oauth_expires_in'];
								if(strval($expires) !== strval(intval($expires)) || $expires <= 0)
								{
									throw new EslipException(ExpiryTimeError);
								}
								$this->access_token_expiry = gmstrftime('%Y-%m-%d %H:%M:%S', time() + $expires);
								$access_token['expiry'] = $this->access_token_expiry;
							}

							// Para Yahoo por ejemplo
							if (($this->has_access_token_extra_parameter) && IsSet($response[$this->access_token_extra_parameter_name]))
							{
								$this->access_token_extra_parameter = $response[$this->access_token_extra_parameter_name];
								$access_token['extra_parameter'] = $this->access_token_extra_parameter;
							}
							
							$this->StoreAccessToken($access_token); //Almacenamos el access token

						}
					}

					if(IsSet($access_token['authorized']) && $access_token['authorized'])
					{
						// El token OAuth ya fue autorizado, ya sea porque ya estaba autorizado, o porque se pidio uno nuevo 
						// porque el que estaba no estaba autorizado o habia expirado

						$this->access_token = $access_token['value'];
						$this->access_token_secret = $access_token['secret'];
						if (IsSet($access_token['expiry']))
						{
							$this->access_token_expiry = $access_token['expiry'];
						}
						if (IsSet($access_token['extra_parameter']))
						{
							$this->access_token_extra_parameter = $access_token['extra_parameter'];
						}
						return(TRUE);
					}
				}
				else
				{
					$access_token = array(); // El token de acceso no está establecido
				}
				
				if(empty($access_token))
				{
					//Solicitamos el token OAuth no autorizado. Despues, hay que procearlo de nuevo para autorizarlo
					
					$url = $this->request_token_url;

					$url = str_replace('{SCOPE}', UrlEncode($this->scope), $url); 
					
					$oauth = array(
						'oauth_callback' => $this->redirect_uri,
					);
					
					$response = $this->SendAPIRequest($url, 'GET', array(), $oauth, array('Resource'=>'OAuth request token'));

					if(!IsSet($response['oauth_token'])	|| !IsSet($response['oauth_token_secret']))
					{
						throw new EslipException(NotReturnedAccessTokenError);
					}

					$access_token = array(
						'value'=>$response['oauth_token'],
						'secret'=>$response['oauth_token_secret'],
						'authorized'=>FALSE
					);
					
					$this->StoreAccessToken($access_token);
				}
				
				$url = $this->dialog_url;
				$url .= '?oauth_token='.$access_token['value'];
				if(!$one_a)
				{
					$url .= '&oauth_callback='.UrlEncode($this->redirect_uri);
				}
				Header('HTTP/1.0 302 OAuth Redirection');
				Header('Location: '.$url);
				$this->exit = TRUE;
				return(TRUE);

			case 2:
								
				$access_token = $this->GetAccessToken();  // El access token se guarda en la sesion, nos fijamos si está ahí
				
				// Chequeamos si esta seteado el access token	
				if(IsSet($access_token['value'])) 
				{
					$expired = (IsSet($access_token['expiry']) && strcmp($access_token['expiry'], gmstrftime('%Y-%m-%d %H:%M:%S')) < 0);
					
					if (!$expired)					
					{
						$this->access_token = $access_token['value'];

						if (IsSet($access_token['expiry']))
						{
							$this->access_token_expiry = $access_token['expiry'];
						}
						
						return(TRUE);
					}
				}
				
				$stored_state = $this->GetStoredState();
									
				$state = $this->GetRequestState();
				
				// Chequemos si los state son iguales en caso que sea una respuesta	
				if($state === $stored_state)
				{
					$code = $this->GetRequestCode();
						
					if(strlen($code) == 0) // Si el usuario no aceptó
					{
						$authorization_error = $this->GetRequestError();
	
						if(IsSet($authorization_error))
						{
							throw new EslipException(sprintf(AuthorizationErrorWithCodeError, $authorization_error));
						}
						else
						{
							throw new EslipException(AuthorizationError);
						}
					}
					
					$url = $this->access_token_url;

					$values = array(
						'code'=>$code,
						'client_id'=>$this->client_id,
						'client_secret'=>$this->client_secret,
						'redirect_uri'=>$this->redirect_uri,
						'grant_type'=>'authorization_code'
					);
					
					$response = $this->SendAPIRequest($url, 'POST', $values, NULL, array('Resource'=>'OAuth access token'));
										
					if(!IsSet($response['access_token']))
					{
						if(IsSet($response['error']))
						{
							throw new EslipException(sprintf(ServerNotReturnAccessTokenWhiteCodeError, $response['error']));
						}
						throw new EslipException(ServerNotReturnAccessTokenError);
					}
										
					$access_token = array();
					$access_token['authorized'] = TRUE;			
					
					$this->access_token = $response['access_token'];
					$access_token['value'] = $this->access_token;

					if(IsSet($response['expires']) || IsSet($response['expires_in']))
					{
						$expires = (IsSet($response['expires']) ? $response['expires'] : $response['expires_in']);
						if(strval($expires) !== strval(intval($expires)) || $expires <= 0)
						{
							throw new EslipException(ExpiryTimeError);
						}
						$this->access_token_expiry = gmstrftime('%Y-%m-%d %H:%M:%S', time() + $expires);
						$access_token['expiry'] = $this->access_token_expiry;
					}
					
					$this->StoreAccessToken($access_token);

				}
				else
				{
					$url = $this->dialog_url;
					$url = str_replace(
						'{REDIRECT_URI}', UrlEncode($this->redirect_uri), str_replace(
						'{CLIENT_ID}', UrlEncode($this->client_id), str_replace(
						'{SCOPE}', UrlEncode($this->scope), str_replace(
						'{STATE}', UrlEncode($stored_state),
						$url))));
					Header('HTTP/1.0 302 OAuth Redirection');
					Header('Location: '.$url);
					$this->exit = TRUE;
					return(TRUE);
				}
				break;

			default:
				throw new EslipException(sprintf(OAuthVersionError, $this->oauth_version));
		}
		return(TRUE);
	}
}

if (IsSet($_GET['eslip_data']))
{
	$aux = json_decode(base64url_decode($_GET['eslip_data']));
	$referer = $aux->referer;
	$server = $aux->server;
}
else
{
	$referer = (IsSet($_GET['referer'])) ? $_GET['referer'] : '';
	$server = (IsSet($_GET['server'])) ? $_GET['server'] : '';
}

try
{
	new eslip_oauth($eslip, $server, $referer);
}
catch (EslipException $e)
{
	$return = array('error' => $e->getMessage(), 
					'server' => $server, 
					'referer' => $referer,
					'state' => 'error',
					'client_callback_url' => (string)$eslip->configuration->callbackUrl);

	$return = base64url_encode(json_encode($return));

	$callback_url_preocess = str_replace('{DATA}', $return, (string)$eslip->configuration->pluginUrl."eslip_callback_process.php?data={DATA}");

	close_and_redirect_parent_window($callback_url_preocess);
}