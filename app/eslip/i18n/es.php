<?php

/* WIZARD */

//step titles
define("CreateAdminUser","Crear Usuario Administrador");
define("GeneralConfigs","Configuraciones Generales");
define("IdProviders","Proveedores de Identidad");

//labels
define("AdminUser", "Nombre de Usuario");
define("AdminPass", "Contrase&ntilde;a");
define("AdminPassConfirm", "Confirmar Contrase&ntilde;a");

//texts
define("LoginFormDesc","Debe copiar el siguiente c&oacute;digo en su p&aacute;gina de Login");

//buttons
define("next","Siguiente");
define("previous","Anterior");
define("cancel","Cancelar");
define("finish","Finalizar");

//dialogs
define("SelectLangTitle","Seleccionar lenguaje");
define("WizardEndTitle","Configuración Finalizada");
define("WizardEndSubTitle","Gracias por usar ESLIP Plugin");

/* ADMIN */

//title
define("adminTitle", "Administrador ESLIP");
define("Login", "Iniciar Sesi&oacute;n");

//menu
define("ConfigUser","Configuraciones de Usuario");
define("LanguagesConfig", "Configuración de Idiomas");
define("LoginWidget", "Widget de Social Login");
define("IdProvidersButtons", "Botones de Proveedores");

//labels
define("SiteUrl", "URL del sitio");
define("CallbackUrl", "URL de retorno");
define("PluginUrl", "URL del plugin ESLIP");
define("ClientId", "ID de Cliente");
define("ClientSecret", "Secreto de Cliente");
define("Scope", "Scope");
define("Id", "ID");
define("Name", "Nombre");
define("Label", "Texto del botón");
define("Active", "Activo");
define("Oauth", "Versión de OAuth");
define("RequestTokenUrl", "URL del punto de entrada para la solicitud del token inicial");
define("DialogUrl", "URL del punto de entrada para la autorización");
define("AccessTokenUrl", "URL del punto de entrada para la obtención del token de acceso");
define("ApiCallParameters", "Parámetros para la obtención de recursos del usuario");
define("AuthorizationHeader", "Parámetros en la cabecera de la petición");
define("UrlParameters", "Parámetros en la URL");
define("HasAccessTokenExtraParameter", "Parámetro adicional en la respuesta a la petición del token de acceso");
define("AccessTokenExtraParameterName", "Nombre del parámetro adicional");
define("UserDataUrl", "URL del punto de entrada para la obtención de recursos del usuario");
define("FormUrl", "URL del formulario");
define("ScopeRequired", "Scope Requerido");
define("ScopeOptional", "Scope Opcional");
define("UserDataIdKey", "Recurso utilizado como identificador único del usuario");
define("Immediate", "Autenticación Inmediata");
define("YesLabel", "Si");
define("NoLabel", "No");
define("LangName", "Nombre del idioma");
define("DownloadLangFile", "Descargar el template del archivo de idioma");
define("UploadLangFile", "Subir el archivo de idioma traducido");
define("TranslateLangFile", "Traducir el archivo descargado y renombrarlo con el correspondiente codigo de lenguage ISO de dos letras en minúsculas (<a target='_blank' href='http://www.sitepoint.com/web-foundations/iso-2-letter-language-codes/'>http://www.sitepoint.com/web-foundations/iso-2-letter-language-codes/</a>)");
define("Logo", "Logo");
define("TextColor", "Color de Texto");
define("BackgroundColor", "Color de Fondo");
define("ButtonPreview", "Vista previa del botón");
define("WidgetWidth", "Ancho del Wdiget");
define("WidgetRows", "Cantidad de Filas");
define("WidgetColumns", "Cantidad de Columnas");
define("ButtonLabel", "Texto en los botones");
define("SelectOption", "Seleccione una opci&oacute;n");
define("StandardView", "Vista Estandar");
define("AdvancedView", "Vista Avanzada");

//dataTable
define("Procesando", "Procesando...");
define("dtsLoadingRecords", "Cargando...");
define("dtsLengthMenu", "Mostrar _MENU_ registros por p&aacute;gina");
define("dtsZeroRecords", "No se han encontrado registros");
define("dtsInfo", "Mostrando del _START_ al _END_ de _TOTAL_ registros");
define("dtsInfoEmpty", "No se han encontrado registros");
define("dtsInfoFiltered","(filtrado de _MAX_ registros totales)");
define("dtsSearch", "Buscar:");
define("dtsFirst", "Primera");
define("dtsPrevious", "Anterior");
define("dtsNext", "Siguiente");
define("dtsLast", "&Uacute;ltima");

//buttons
define("btnNew","Nuevo");
define("btnEdit","Editar");
define("btnDelete","Eliminar");
define("btnSave","Guardar");
define("btnCancel","Cancelar");
define("btnConfirm","Confirmar");
define("btnLogin","Entrar");
define("btnLogout","Salir");
define("btnWizard","Wizard de configuraci&oacute;n");
define("btnDownload", "Descargar");
define("btnUpload", "Subir");

//confirm
define("deleteConfirm", "Este elemento va a ser eliminado definitivamente. Est&aacute; seguro?");

//messages
define("messageSuccess","Datos guardados correctamente");
define("messageError","Ocurri&oacute; un error al guardar los datos");
define("messageLoginInfo","Si a&uacute;n no ha registrado su usuario debe ejecutar el ");
define("messageLoginError","Ocurri&oacute; un error al iniciar sesi&oacute;n, por favor verifique los datos ingresados");
define("messageDeleteOpenID", "OpenID no pude ser eliminado");
define("messageMustSelectProvider", "Debe seleccionar un Proveedor de Identidad");
define("messageMustSelectProviderButton", "Debe seleccionar un Bot&oacute;n de un Proveedor de Identidad");
define("messageErrorIdExists", "Error al guardar los datos: El ID %s ya existe.");
define("messageErrorIdAssociated", "Imposible eliminar los datos: El ID %s est&aacute; asociado a un Proveedor de Identidad.");
define("messageErrorImageType", "Tipo de imagen %s no soportado");
define("messageRequired", "Este campo es obligatorio.");
define("messageRemote", "Por favor, complet&aacute; este campo.");
define("messageUrl", "Por favor, escrib&iacute; una URL v&aacute;lida.");
define("messageNumber", "Por favor, escribí un número entero v&aacute;lido.");
define("messageDigits", "Por favor, escribí sólo dígitos.");
define("messageEqualTo", "Por favor, escrib&iacute; el mismo valor de nuevo.");
define("messageMinlength", "Por favor, no escribas menos de {0} caracteres.");
define("messageMin", "Por favor, escribí un valor mayor o igual a {0}.");

/* ERRORS */

//textos de errores de excepciones de la api
define("CurlError", "ESLIP ERROR: Se requiere la extensión PHP CURL.");
define("SessionError", "ESLIP ERROR: No fué posible iniciar la sesión de PHP.");
define("ParametersErrorEslipDataInConstruct", "ESLIP ERROR: No se han pasado los parámetros necesarios. Datos de Eslip en Constructor.");
define("ParametersErrorIPInConstruct", "ESLIP ERROR: No se han pasado los parámetros necesarios. Proveedor de Identidad en Constructor.");
define("NoConfigFileError", "ESLIP ERROR: No se ha establecido el archivo XML de configuración.");

//openid
define("ParametersErrorURLInOpenIDConstruct", "ESLIP ERROR: No se han pasado los parámetros necesarios. URL en Constructor de OpenID.");
define("ParametersErrorIdentityInOpenIDDiscover", "ESLIP ERROR: No se han pasado los parámetros necesarios. Identity en Discover en OpenID.");
define("URIErrorInOpenIDDiscover", "ESLIP ERROR: Discover: No se pudo encontrar la OpenID endpoint URL.");
define("CancelInfo", "ESLIP INFO: El usuario ha cancelado la autenticación.");
define("NoIdResError", "ESLIP ERROR: Ha ocurrido un error en la verificación de la autenticación. Se obtuvo un modo distinto a id_res.");
define("DifferentsReturnURLError", "ESLIP ERROR: Ha ocurrido un error en la verificación de la autenticación. Las URLs de retorno deben coincidir.");
define("ImmediateRedirecting", "Imposible realizar la autenticación inmediata. Redirigiendo...");
define("emptyErrorMessage", "El identificador de OpenID no puede estar vac&iacute;o");

//oauth
define("CurlResponseError", "ESLIP ERROR: No fue posible acceder a %s: Se ha devuelto un estado inesperado %s. Respuesta: %s");
define("OAuthVersionError", "ESLIP ERROR: %s no es una versión soportada del protocolo OAuth.");
define("NoAccessTokenError", "ESLIP ERROR: Imposible realizar llamada a la API ya que no se posee token de acceso.");
define("RequestTokenDeniedError", "ESLIP ERROR: El Request Token fue denegado.");
define("NotReturnedAccessTokenError", "ESLIP ERROR: No se devolvió el token de acceso o el secret.");
define("ExpiryTimeError", "ESLIP ERROR: El servidor OAuth devolvió un tipo no compatible de fecha de expiración del token de acceso.");
define("AuthorizationErrorWithCodeError", "ESLIP ERROR: Ha ocurrido un error en la autorización. Código de error OAuth: %s.");
define("AuthorizationError", "ESLIP ERROR: Ha ocurrido un error en la autorización. No se ha devuelto el código de diálogo OAuth.");
define("ServerNotReturnAccessTokenError", "ESLIP ERROR: El servidor OAuth no devolvió el token de acceso.");
define("ServerNotReturnAccessTokenWhiteCodeError", "ESLIP ERROR: No fue posible recuperar el token de acceso: se devolvió el error: %s.");

//Popover help
define("HelpSiteUrl", "URL del sitio web en donde está incluyendo el plugin. Por ejemplo: http://www.example.com");
define("HelpCallbackUrl", "URL a donde se debería retornar una vez realizada la identificación. Aquí el desarrollador procesará los datos devueltos por el plugin como resultado de su interacción con el proveedor de identidad. Los datos son enviados a esta URL por el método HTTP POST además de ser almacenados en la Sesión PHP. Por ejemplo: http://www.example.com/index.php");
define("HelpPluginUrl", "URL donde se encuentra alojado el plugin dentro de su sitio web. Por ejemplo: http://www.example.com/eslip/");
define("HelpLanguage", "Aquí debe seleccionar el idioma en que quiera que se muestren los textos de la interfaz del plugin y del administrador.");

define("HelpScopeRequired", "Atributos que se desean requerir al proveedor de identidad. Deben estar separados por coma.");
define("HelpScopeOptional", "Atributos que se desean requerir al proveedor de identidad pero que serán opcionales. El proveedor de identidad puede o no retornarlos. Los diferentes scopes que se pueden solicitar pueden ser consultados en <a href='http://openid.net/specs/openid-attribute-properties-list-1_0-01.html#Prop_List' target='_blank'>http://openid.net/specs/openid-attribute-properties-list-1_0-01.html#Prop_List</a>. De cada scope se debe descartar la primer parte correspondiente a http://openid.net/schema/ la cual se repite en todos los scopes, y solamente se debe utilizar lo que se encuentra a continuación.");
define("HelpClientId", "Identificador provisto por la API OAuth para la aplicación externa creada en el proveedor de identidad correspondiente.");
define("HelpClientSecret", "Clave secreta provista por la API OAuth para la aplicación externa creada en el proveedor de identidad correspondiente.");
define("HelpScope", "Recursos que se desean obtener y que su propietario quien se autentica a través del proveedor de identidad debe autorizar para que sean concedidos.");
define("HelpFormUrl", "Dirección relativa dentro de la carpeta del plugin donde se encuentra el formulario donde el el usuario ingresa su identificador OpenID.");
define("HelpImmediate", "Aquí se establece si la autenticación se va a realizar utilizando el modo inmediato.");

define("HelpId", "Identificador único para cada proveedor de identidad. ESLIP ofrece una lista de identificadores precargados. El plugin provee un estilo de botón junto con el correspondiente icono para cada identificador.");
define("HelpName", "Nombre para identificar el proveedor de identidad dentro del administrador del plugin.");
define("HelpLabel", "Texto que aparece en el botón del proveedor de identidad correspondiente dentro del widget.");
define("HelpActive", "Con esta opción puede activar o desactivar este proveedor de identidad para que no sea visible en el widget.");
define("HelpOauth", "Versión del protocolo que soporta el servidor OAuth del proveedor de identidad.");
define("HelpRequestTokenUrl", "URL del servidor OAuth del proveedor de identidad para solicitar el token inicial cuando trabajamos con servidores OAuth 1.0 y 1.0a.");
define("HelpDialogUrl", "URL del formulario de login del proveedor de identidad. Se redirigirá al propietario del recurso a dicha URL para que inicie sesión y autorice o no a la aplicación creada en dicho proveedor de identidad a acceder a los recursos solicitados.");
define("HelpAccessTokenUrl", "URL del servidor OAuth del proveedor de identidad que retornará el token de acceso.");
define("HelpApiCallParameters", "Cadena con los parámetros que se le envían a la API del proveedor de identidad, entre los cuales debe estar el token de acceso.");
define("HelpAuthorizationHeader", "Establece si la API del proveedor de identidad requiere, a la hora de realizar la petición del token inicial, que se le envíen los parámetros en la cabecera de la petición HTTP. Si no se está seguro de ello dejar la opción en 'Si'.");
define("HelpUrlParameters", "Establece si la API del proveedor de identidad requiere, a la hora de realizar la petición del token inicial, que se le envíen los parámetros en la URL. Si no se está seguro de ello dejar la opción en 'Si'.");
define("HelpHasAccessTokenExtraParameter", "Señala si la API OAuth del proveedor de identidad devuelve un parámetro extra en la respuesta a la petición de token de acceso necesario para la autenticación.");
define("HelpAccessTokenExtraParameterName", "Nombre de la llave dentro de la respuesta a la petición de token de acceso para acceder al parámetro extra en caso de que el proveedor de identidad devuelva uno. Un ejemplo es Yahoo!, el cual devuelve el parámetro 'xoauth_yahoo_guid' necesario para obtener los recursos solicitados.");
define("HelpUserDataUrl", "URL que proporciona la API OAuth del proveedor de identidad a la cual se le realiza la petición para obtener los recursos requeridos.");
define("HelpUserDataIdKey", "Nombre de la clave en el objeto que retorna el proveedor de identidad como respuesta a la petición de recursos, la cual se utiliza para acceder al identificador único del propietario de los recursos. Si la clave con el nombre ingresado existe en el objeto devuelto por el proveedor de identidad, su valor es enviado a la 'callback URL' establecida por el desarrollador en un objeto junto al resto de los recursos obtenidos bajo la clave 'id'. Este objeto también es almacenado en la sesión PHP.");

define("HelpWidgetWidth", "Ancho que tendrá el widget. Se puede establecer en pixeles o en porcentaje dependiendo la necesidad de cada desarrollador.");
define("HelpWidgetRows", "Cantidad de filas de botones que se mostrarán en el widget. Si se opta por la opción 'auto', todos los botones se mostrarán en forma vertical obedeciendo la cantidad de columnas fijadas sin importar la cantidad de filas utilizadas. Mientras que, si se establece un valor mayor o igual a uno y la cantidad de botones a mostrar excede la capacidad del widget, acorde a la cantidad de columnas y filas configuradas, se generará una especie de galería con paneles horizontales deslizantes, en donde por cada panel se exhibirá el número de botones correspondientes.");
define("HelpWidgetColumns", "Cantidad de columnas de botones que se mostrarán en el widget. Es importante el valor que se establece aquí, ya que a partir de ésta cantidad se calcula el ancho de cada botón que se muestra en el widget.");
define("HelpWidgetButtonLabel", "Define si se mostrará en los botones solo el icono del correspondiente proveedor de identidad o se mostrará junto a él un texto descriptivo llamado label.");

define("HelpButtonID", "Debe ser único y se utilizará para identificar cada una de los proveedores de identidad.");
define("HelpButtonLogo", "Logo del proveedor de identidad que está asociado a este identificador.");
define("HelpButtonTextColor", "Color que será utilizado en el texto dentro del botón del proveedor de identidad que se muestra en el widget cuando éste está activo.");
define("HelpButtonBackgroundColor", "Color que será utilizado como fondo del botón del proveedor de identidad que se muestra en el widget cuando éste está activo.");

?>