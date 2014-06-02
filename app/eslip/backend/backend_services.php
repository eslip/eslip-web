<?php
	
	include_once("../eslip.php");
	include_once("../eslip_services.php");
	session_start();

	class BackendServiceApi extends EslipServices{
		
		public function __construct($xmlApi){
			parent::__construct($xmlApi);
		}

		/******************************************************
		* General Functions
		*******************************************************/

		protected function init(){

			$validatorMessages = array(
				"required" => messageRequired,
				"remote" => messageRemote,
				"url" => messageUrl,
				"number" => messageNumber,
				"digits" => messageDigits,
				"equalTo" => messageEqualTo,
				"minlength" => messageMinlength,
				"min" => messageMin
			);

			$data = array(
				"validatorMessages" => $validatorMessages
			);

			$this->response($data);
		}

		/******************************************************
		* Admin Functions
		*******************************************************/

		protected function login(){
			$eslipSettings = $this->xmlApi->getElementValue("configuration");
			$adminUser = (string)$eslipSettings->adminUser;
			$adminPass = (string)$eslipSettings->adminPass;
			
			$result = "ERROR";
			if( ($adminUser == $_POST["adminUser"]) && ($adminPass == getEncrypted($_POST["adminPass"])) ){
				$_SESSION["usuario"] = $adminUser;
				 $result = "SUCCESS";
			}

			$data = array(
				"status" => $result
			);

			$this->response($data);
		}

		protected function logout(){
			session_destroy();

			$result = "SUCCESS";
			$data = array(
				"status" => $result
			);
			$this->response($data);
		}

		protected function isAuthenticated(){
			$isAuthenticated = ( isset($_SESSION['usuario']) && ! empty($_SESSION['usuario']) );
			$data = array(
				"isAuthenticated" => $isAuthenticated
			);
			$this->response($data);	
		}

		protected function getGeneralConfigData(){

			$labels = array(
				"generalConfigs" => GeneralConfigs,
				"siteUrl" => SiteUrl,
				"callbackUrl" => CallbackUrl,
				"pluginUrl" => PluginUrl,
				"selectLangTitle" => SelectLangTitle,
				"btnSave" => btnSave,
				"btnCancel" => btnCancel,
				"messageSuccess" => messageSuccess,
				"messageError" => messageError,
				"HelpSiteUrl" => HelpSiteUrl,
				"HelpCallbackUrl" => HelpCallbackUrl,
				"HelpPluginUrl" => HelpPluginUrl,
				"HelpLanguage" => HelpLanguage
			);

			$eslipSettings = $this->xmlApi->getElementValue("configuration");

			$selectedLang = $this->xmlApi->getElementListByFieldValue("selected", "1", "language");	
			$selectedLang = (empty($selectedLang) || empty($selectedLang[0]->code )) ? getSystemLang() : (String)$selectedLang[0]->code;

			$settings = array(
				"siteUrl" => (string)$eslipSettings->siteUrl,
				"callbackUrl" => (string)$eslipSettings->callbackUrl,
				"pluginUrl" => (string)$eslipSettings->pluginUrl,
				"selectedLang" => $selectedLang
			);

			$languageOptions = array();
			$languages = $this->xmlApi->getElementList("language");
 			foreach( $languages as $lang ){
 				$langOption = array(
 					"value" => (string)$lang->code,
 					"content" => (string)$lang->name
 				);
 				array_push($languageOptions, $langOption);
 			}

			//options: [{ value: 3, content: 'test3' }, { value: 4, content: 'test4' }, { value: 5, content: 'test5' }, { value: 6, content: 'test6'}],

			$data = array(
				"labels" => $labels,
				"settings" => $settings,
				"languageOptions" => $languageOptions,
			);

			$this->response($data);
		}

		protected function saveGeneralConfig(){

			//update language
			$this->xmlApi->updateElement(array("selected"), array("0"), "language");
			$this->xmlApi->setElementListByFieldValue("code", $_POST["language"], "language",null,"selected","1");

			//update configuration
			$this->xmlApi->setElementValue("siteUrl", $_POST["siteUrl"], "configuration");
			$this->xmlApi->setElementValue("callbackUrl", $_POST["callbackUrl"], "configuration");
			$this->xmlApi->setElementValue("pluginUrl", $_POST["pluginUrl"], "configuration");

			$result = "SUCCESS";

			$data = array(
				"status" => $result
			);

			$this->response($data);
		}

		protected function getUserConfigData(){

			$labels = array(
				"configUser" => ConfigUser,
				"adminUser" => AdminUser,
				"adminPass" => AdminPass,
				"adminPassConfirm" => AdminPassConfirm,
				"btnSave" => btnSave,
				"btnCancel" => btnCancel,
				"messageSuccess" => messageSuccess,
				"messageError" => messageError
			);

			$eslipSettings = $this->xmlApi->getElementValue("configuration");
			$settings = array(
				"adminUser" => (string)$eslipSettings->adminUser,
			);

			$data = array(
				"labels" => $labels,
				"settings" => $settings
			);

			$this->response($data);
		}

		protected function saveUserConfig(){

			//update admin user
			$this->xmlApi->setElementValue("adminUser", $_POST["adminUser"], "configuration");
			$this->xmlApi->setElementValue("adminPass", getEncrypted($_POST["adminPass"]), "configuration");

			$result = "SUCCESS";

			$data = array(
				"status" => $result
			);

			$this->response($data);
		}

		protected function getIdProviders(){

			$configuration = $this->xmlApi->getElementValue("configuration");
			
			// Identity Provider
			$identityProviders = $this->xmlApi->getElementList("identityProvider");
			$openIdProvider = array();
			$idProviders = array();
			foreach( $identityProviders as $idProvider ){
				$idProvider->id = $idProvider->attributes()->id;
				
				$idProviderFixed = array();
				foreach ($idProvider as $key => $value){
					if ( is_object($value) ){
						$idProviderFixed[$key] = (string)$value;
					}
				}

				$styles = $this->xmlApi->getElementById("buttonStyle", $idProvider->id);
				$idProviderFixed["styles"] = SimpleXMLElementToObject($styles);

				if (isset($idProviderFixed["styles"]->logo)){
					$idProviderFixed["styles"]->logo_url = $configuration->pluginUrl . 'frontend/img/icons/' . $idProviderFixed["styles"]->logo;
				}

				if ($idProviderFixed["id"] == "openid"){
					$openIdProvider = $idProviderFixed;
				}else{
					array_push($idProviders, $idProviderFixed);
				}

			}

			$data = array(
				"idProviders" => $idProviders,
				"openIdProvider" => $openIdProvider
			);

			return $data;
		}

		protected function getIdProvidersData(){

			$labels = array(
				"idProviders" => IdProviders,
				"standardView" => StandardView,
				"advancedView" => AdvancedView,
				"id" => Id,
				"name" => Name,
				"oauth" => Oauth,
				"active" => Active,
				"clientId" => ClientId,
				"clientSecret" => ClientSecret,
				"userDataIdKey" => UserDataIdKey,
				"scopeRequired" => ScopeRequired,
				"scopeOptional" => ScopeOptional,
				"scope" => Scope,
				"buttonPreview" => ButtonPreview,
				"btnNew" => btnNew,
				"btnEdit" => btnEdit,
				"btnDelete" => btnDelete,
				"btnSave" => btnSave,
				"btnCancel" => btnCancel,
				"btnConfirm" => btnConfirm,
				"deleteConfirm" => deleteConfirm,
				"processing" => Procesando,
				"dtsLoadingRecords" => dtsLoadingRecords,
				"dtsLengthMenu" => dtsLengthMenu,
				"dtsZeroRecords" => dtsZeroRecords,
				"dtsInfo" => dtsInfo,
				"dtsInfoEmpty" => dtsInfoEmpty,
				"dtsInfoFiltered" => dtsInfoFiltered,
				"dtsSearch" => dtsSearch,
				"dtsFirst" => dtsFirst,
				"dtsPrevious" => dtsPrevious,
				"dtsNext" => dtsNext,
				"dtsLast" => dtsLast,
				"messageSuccess" => messageSuccess,
				"messageError" => messageError,
				"messageDeleteOpenID" => messageDeleteOpenID,
				"messageMustSelectProvider" => messageMustSelectProvider,
				"HelpScopeRequired" => HelpScopeRequired,
				"HelpScopeOptional" => HelpScopeOptional,
				"HelpClientId" => HelpClientId,
				"HelpClientSecret" => HelpClientSecret,
				"HelpScope" => HelpScope
			);

			$idProvidersData = $this->getIdProviders();
			$idProviders = $idProvidersData["idProviders"];
			$openIdProvider = $idProvidersData["openIdProvider"];
			
			$data = array(
				"labels" => $labels,
				"idProviders" => $idProviders,
				"openIdProvider" => $openIdProvider
			);

			$this->response($data);
		}

		protected function getIdProvidersDataTable(){

			$data = array();
			$row = array();
			$totalRecords = 0;
			// Identity Provider
			$identityProviders = $this->xmlApi->getElementList("identityProvider");
			foreach( $identityProviders as $idProvider ){
				
				$row = array((String)$idProvider->attributes()->id, (String)$idProvider->name, (String)$idProvider->label, (String)$idProvider->active, 0);
				array_push($data,$row);
				$totalRecords++;
			}
			
			$data = array("sEcho" => 1, "iTotalRecords" => $totalRecords, "iTotalDisplayRecords" => $totalRecords, "aaData" => $data);

			$this->response($data);
		}

		protected function getIdProviderData(){

			$labels = array(
				"id" => Id,
				"name" => Name,
				"label" => Label,
				"active" => Active,
				"no" => NoLabel,
				"yes" => YesLabel,
				"oauth" => Oauth,
				"requestTokenUrl" => RequestTokenUrl,
				"dialogUrl" => DialogUrl,
				"accessTokenUrl" => AccessTokenUrl,
				"apiCallParameters" => ApiCallParameters,
				"authorizationHeader" => AuthorizationHeader,
				"urlParameters" => UrlParameters,
				"clientId" => ClientId,
				"clientSecret" => ClientSecret,
				"scope" => Scope,
				"hasAccessTokenExtraParameter" => HasAccessTokenExtraParameter,
				"accessTokenExtraParameterName" => AccessTokenExtraParameterName,
				"userDataUrl" => UserDataUrl,
				"formUrl" => FormUrl,
				"scopeRequired" => ScopeRequired,
				"scopeOptional" => ScopeOptional,
				"userDataIdKey" => UserDataIdKey,
				"immediate" => Immediate,
				"buttonPreview" => ButtonPreview,
				"selectOption" => SelectOption,
				"btnNew" => btnNew,
				"btnEdit" => btnEdit,
				"HelpClientId" => HelpClientId,
				"HelpClientSecret" => HelpClientSecret,
				"HelpScope" => HelpScope,
				"HelpId" => HelpId,
				"HelpName" => HelpName,
				"HelpLabel" => HelpLabel,
				"HelpActive" => HelpActive,
				"HelpOauth" => HelpOauth,
				"HelpRequestTokenUrl" => HelpRequestTokenUrl,
				"HelpDialogUrl" => HelpDialogUrl,
				"HelpAccessTokenUrl" => HelpAccessTokenUrl,
				"HelpApiCallParameters" => HelpApiCallParameters,
				"HelpAuthorizationHeader" => HelpAuthorizationHeader,
				"HelpUrlParameters" => HelpUrlParameters,
				"HelpHasAccessTokenExtraParameter" => HelpHasAccessTokenExtraParameter,
				"HelpAccessTokenExtraParameterName" => HelpAccessTokenExtraParameterName,
				"HelpUserDataUrl" => HelpUserDataUrl,
				"HelpUserDataIdKey" => HelpUserDataIdKey,
				"HelpScopeRequired" => HelpScopeRequired,
				"HelpScopeOptional" => HelpScopeOptional,
				"HelpFormUrl" => HelpFormUrl,
				"HelpImmediate" => HelpImmediate,
				"btnNew" => btnNew,
				"btnEdit" => btnEdit
			);

			$new = true;
			$method = "new";
			$id = "";
			if (isset($_GET["id"]) && !empty($_GET["id"])){
				$id = $_GET["id"];
				$new = false;
				$method = "update";
			}
			
			$idProviderFixed = array();
			$idProvider = new stdClass();
			if (!empty($id)){
				$idProvider = $this->xmlApi->getElementById("identityProvider",$id);
				$idProvider->id = $idProvider->attributes()->id;

				foreach ($idProvider as $key => $value){
					if ( is_object($value) ){
						$idProviderFixed[$key] = (string)$value;
					}
				}
			}

			$idsInUseArray = array();
			$idsInUse = $this->xmlApi->getElementList("identityProvider");
			foreach( $idsInUse as $i ){
 				array_push($idsInUseArray, (String)$i->attributes()->id);
 			}

			$buttonsArray = array();
			$buttons = $this->xmlApi->getElementList("buttonStyle");
 			foreach( $buttons as $b ){
 				array_push($buttonsArray, (String)$b->attributes()->id);
 			}

 			$buttonsArray = array_diff($buttonsArray, $idsInUseArray);

			asort($buttonsArray);

			$buttonsOptions = array();
 			foreach( $buttonsArray as $b ){
 				$bOption = array(
 					"value" => $b,
 					"content" => $b
 				);
 				array_push($buttonsOptions, $bOption);
 			}
			//options: [{ value: 3, content: 'test3' }, { value: 4, content: 'test4' }, { value: 5, content: 'test5' }, { value: 6, content: 'test6'}],

			$data = array(
				"labels" => $labels,
				"new" => $new,
				"method" => $method,
				"idProvider" => $idProviderFixed,
				"idProvidersButtons" => $buttonsOptions
			);

			$this->response($data);
		}

		protected function deleteIdProvider(){
			$this->xmlApi->removeElementById("identityProvider", $_POST["id"]);

			$result = "SUCCESS";

			$data = array(
				"status" => $result
			);

			$this->response($data);
		}

		protected function updateIdProvidersImpl(){

			//update idprovider data
			for($i=0;$i<count($_POST["idProviderId"]);$i++){
				$this->xmlApi->updateElementById(
					array("active","clientId","clientSecret","scope"),
					array($_POST["active"][$i],$_POST["clientId"][$i],$_POST["clientSecret"][$i],$_POST["scope"][$i]),
					"identityProvider",
					$_POST["idProviderId"][$i]
				);
			}

			// update openid idprovider data
			$this->xmlApi->updateElementById(
				array("active","scopeRequired","scopeOptional"),
				array($_POST["openIdActive"],$_POST["scopeRequired"],$_POST["scopeOptional"]),
				"identityProvider",
				$_POST["openIdProviderId"]
			);
			
		}

		protected function updateIdProviders(){

			$this->updateIdProvidersImpl();

			$data = array(
				"status" => "SUCCESS"
			);

			$this->response($data);	
		}

		protected function saveIdProvider(){

			//crear uno nuevo si no existe
			if ($_POST["method"] == "new"){
				
				$this->newIdProvider();
				
			}else{
				//update idprovider data
				$this->updateIdProvider();
			}

			$result = "SUCCESS";

			$data = array(
				"status" => $result
			);

			$this->response($data);
		}

		protected function newIdProvider(){
			if($_POST["id"] != "openid"){

				$this->xmlApi->addElement(
				
					$_POST["id"],
					
					array(
						"name",
						"label",
						"active",
						"oauth",
						"requestTokenUrl",
						"dialogUrl",
						"accessTokenUrl",
						"apiCallParameters",
						"authorizationHeader",
						"urlParameters",
						"clientId",
						"clientSecret",
						"scope",
						"hasAccessTokenExtraParameter",
						"accessTokenExtraParameterName",
						"userDataUrl",
						"userDataIdKey"
					),
					
					array(
						$_POST["name"],
						$_POST["label"],
						$_POST["active"],
						$_POST["oauth"],
						$_POST["requestTokenUrl"],
						$_POST["dialogUrl"],
						$_POST["accessTokenUrl"],
						$_POST["apiCallParameters"],
						$_POST["authorizationHeader"],
						$_POST["urlParameters"],
						$_POST["clientId"],
						$_POST["clientSecret"],
						$_POST["scope"],
						$_POST["hasAccessTokenExtraParameter"],
						$_POST["accessTokenExtraParameterName"],
						$_POST["userDataUrl"],
						$_POST["userDataIdKey"]
					),
					
					"identityProvider",
					
					"identityProviders"
				);
			}
		}

		protected function updateIdProvider(){
			if($_POST["id"] == "openid"){
					
				$this->updateOpenIdProvider();
			
			}else{
			
				$this->xmlApi->updateElementById(
					
					array(
						"name",
						"label",
						"active",
						"oauth",
						"requestTokenUrl",
						"dialogUrl",
						"accessTokenUrl",
						"apiCallParameters",
						"authorizationHeader",
						"urlParameters",
						"clientId",
						"clientSecret",
						"scope",
						"hasAccessTokenExtraParameter",
						"accessTokenExtraParameterName",
						"userDataUrl",
						"userDataIdKey"
					),
					
					array(
						$_POST["name"],
						$_POST["label"],
						$_POST["active"],
						$_POST["oauth"],
						$_POST["requestTokenUrl"],
						$_POST["dialogUrl"],
						$_POST["accessTokenUrl"],
						$_POST["apiCallParameters"],
						$_POST["authorizationHeader"],
						$_POST["urlParameters"],
						$_POST["clientId"],
						$_POST["clientSecret"],
						$_POST["scope"],
						$_POST["hasAccessTokenExtraParameter"],
						$_POST["accessTokenExtraParameterName"],
						$_POST["userDataUrl"],
						$_POST["userDataIdKey"]
					),
					
					"identityProvider",
					
					$_POST["id"]
				);
			}
		}

		protected function updateOpenIdProvider(){
			$this->xmlApi->updateElementById(
					
				array(
					"name",
					"label",
					"active",
					"formUrl",
					"scopeRequired",
					"scopeOptional",
					"userDataIdKey",
					"immediate"
				),
				
				array(
					$_POST["name"],
					$_POST["label"],
					$_POST["active"],
					$_POST["formUrl"],
					$_POST["scopeRequired"],
					$_POST["scopeOptional"],
					$_POST["userDataIdKey"],
					$_POST["immediate"]
				),
				
				"identityProvider",
				
				$_POST["id"]
			);
		}

		protected function getLanguagesConfigData(){
			$labels = array(
				"languagesConfig" => LanguagesConfig,
				"downloadLangFile" => DownloadLangFile,
				"uploadLangFile" => UploadLangFile,
				"translateLangFile" => TranslateLangFile,
				"langName" => LangName,
				"btnDownload" => btnDownload,
				"btnUpload" => btnUpload,
				"messageSuccess" => messageSuccess,
				"messageError" => messageError
			);

			$data = array(
				"labels" => $labels
			);

			$this->response($data);
		}

		protected function uploadLangFile(){

			$targetFilepath = "i18n/" . basename($_FILES['langFile']['name']);
 			
 			$result = "ERROR";

			if (move_uploaded_file($_FILES['langFile']['tmp_name'], $targetFilepath)) {
				$result = "SUCCESS";
			}

			$data = array(
				"status" => $result,
				"file" => $_FILES['langFile']['name'],
				"name" => $_POST['langName']
			);

			$this->response($data);
		}

		protected function compileLanguageFile(){

			$content = array();

			function printDefine($key,$value,&$content){
				//global $content;
				array_push($content, 'define("'.$key.'","'.$value.'");' );
				array_push($content, "\n");
			}

			function printSection($key,&$content){
				//global $content;
				array_push($content, "\n");
				array_push($content, '/* '.$key.' */' );
				array_push($content, "\n\n");
			}

			function recorrer($anArray,&$content){
				foreach ($anArray as $key => $value){
		    		printDefine($key,$value,$content);
				}
			}

			$langFile = $_POST["langFile"];
			$langName = $_POST["langName"];
			$langCode = str_replace(".ini", "", $langFile);

			if ($langFile != ""){

				$dir = "i18n/";
				$fileCompiled = "../".$dir.str_replace("ini", "php", $langFile);
				$inifile = parse_ini_file($dir.$langFile, true);

				if ($inifile){

					array_push($content, "<?php");
					array_push($content, "\n");

					foreach ($inifile as $key => $value){
				    	if ( is_array($value) ){
							printSection($key,$content);
							recorrer($value,$content);
						}else{
							printDefine($key,$value,$content);
						}
						
					}

					array_push($content, "\n");
					array_push($content, "?>");

					file_put_contents($fileCompiled, $content);

					$this->xmlApi->addElement(
					
						"",
						
						array(
							"name",
							"code",
							"selected",
						),
						
						array(
							$langName,
							$langCode,
							"0",
						),
						
						"language",
						
						"languages"
					);

					$result = "SUCCESS";

				}else{
					$result = "ERROR";
				}

			}else{
				$result = "ERROR";
			}

			$data = array(
				"status" => $result
			);

			$this->response($data);
			
		}

		protected function getLoginWidgetData(){

			$labels = array(
				"loginFormDesc" => LoginFormDesc,
				"loginWidget" => LoginWidget,
				"widgetWidth" => WidgetWidth,
				"widgetRows" => WidgetRows,
				"widgetColumns" => WidgetColumns,
				"buttonLabel" => ButtonLabel,
				"yes" => YesLabel,
				"no" => NoLabel,
				"btnSave" => btnSave,
				"btnCancel" => btnCancel,
				"messageSuccess" => messageSuccess,
				"messageError" => messageError,
				"HelpWidgetWidth" => HelpWidgetWidth,
				"HelpWidgetRows" => HelpWidgetRows,
				"HelpWidgetColumns" => HelpWidgetColumns,
				"HelpWidgetButtonLabel" => HelpWidgetButtonLabel
			);

			$eslipSettings = $this->xmlApi->getElementValue("configuration");
			$pluginUrl = (string)$eslipSettings->pluginUrl;

			$loginWidget = $eslipSettings->loginWidget->children();
			$loginWidgetFixed = array();

			foreach ( $loginWidget as $key => $value){
					$loginWidgetFixed[$key] = (string)$value;
			}

			$identityProviders = $this->getActiveIdentityProviders();
			$identityProvidersFixed = array();
			foreach( $identityProviders as $idProvider ){
				$idProviderFixed = array(
					"id" => (string)$idProvider->id,
					"label" => (string)$idProvider->label,
					"styles" => $idProvider->styles
				);
				array_push($identityProvidersFixed, $idProviderFixed);
			}

			$data = array(
				"labels" => $labels,
				"loginWidget" => $loginWidgetFixed,
				"identityProviders" => $identityProvidersFixed,
				"pluginUrl"  => $pluginUrl,
				"pluginCss" => $pluginUrl.$_GET["cssUri"],
				"pluginJs" => $pluginUrl.$_GET["jsUri"],
				"eslipDivId" => $_GET["eslipDiv"]
			);

			$this->response($data);
		}

		protected function saveLoginWidget(){

			$this->xmlApi->updateElement(
				array(
					"widgetWidth",
					"widgetRows",
					"widgetColumns",
					"buttonLabel"
				), 
				array(
					$_POST["widgetWidth"].$_POST["widgetWidthUnit"],
					$_POST["widgetRows"],
					$_POST["widgetColumns"],
					$_POST["buttonLabel"]
				), 
				"loginWidget"
			);

			$result = "SUCCESS";

			$data = array(
				"status" => $result
			);

			$this->response($data);
		}

		protected function getIdProvidersButtonsData(){

			$labels = array(
				"idProvidersButtons" => IdProvidersButtons,
				"id" => Id,
				"logo" => Logo,
				"textColor" => TextColor,
				"backgroundColor" => BackgroundColor,
				"btnNew" => btnNew,
				"btnEdit" => btnEdit,
				"btnDelete" => btnDelete,
				"btnSave" => btnSave,
				"btnCancel" => btnCancel,
				"btnConfirm" => btnConfirm,
				"deleteConfirm" => deleteConfirm,
				"processing" => Procesando,
				"dtsLoadingRecords" => dtsLoadingRecords,
				"dtsLengthMenu" => dtsLengthMenu,
				"dtsZeroRecords" => dtsZeroRecords,
				"dtsInfo" => dtsInfo,
				"dtsInfoEmpty" => dtsInfoEmpty,
				"dtsInfoFiltered" => dtsInfoFiltered,
				"dtsSearch" => dtsSearch,
				"dtsFirst" => dtsFirst,
				"dtsPrevious" => dtsPrevious,
				"dtsNext" => dtsNext,
				"dtsLast" => dtsLast,
				"messageSuccess" => messageSuccess,
				"messageError" => messageError,
				"messageDeleteOpenID" => messageDeleteOpenID,
				"messageMustSelectProviderButton" => messageMustSelectProviderButton
			);

			$data = array(
				"labels" => $labels
			);

			$this->response($data);
		}

		protected function getIdProvidersButtonsDataTable(){
			$data = array();
			$row = array();
			$totalRecords = 0;
			
			$eslipSettings = $this->xmlApi->getElementValue("configuration");
			$pluginUrl = (string)$eslipSettings->pluginUrl;
			if ( !isset($pluginUrl) || empty($pluginUrl) ){
				$pluginUrl = "http://".$_SERVER['HTTP_HOST']."/eslip-plugin/eslip/";	
			}

			// Identity Provider
			$idButtons = $this->xmlApi->getElementList("buttonStyle");
			foreach( $idButtons as $idButton ){
				$logo_url = $pluginUrl . 'frontend/img/icons/' . (String)$idButton->logo;
				$row = array((String)$idButton->attributes()->id, $logo_url, (String)$idButton->textColor, (String)$idButton->backgroundColor, 0);
				array_push($data,$row);
				$totalRecords++;
			}
			
			$data = array("sEcho" => 1, "iTotalRecords" => $totalRecords, "iTotalDisplayRecords" => $totalRecords, "aaData" => $data);

			$this->response($data);
		}

		protected function getIdProviderButtonData(){
			$labels = array(
				"id" => Id,
				"logo" => Logo,
				"textColor" => TextColor,
				"backgroundColor" => BackgroundColor,
				"HelpButtonID" => HelpButtonID,
				"HelpButtonLogo" => HelpButtonLogo,
				"HelpButtonTextColor" => HelpButtonTextColor,
				"HelpButtonBackgroundColor" => HelpButtonBackgroundColor
			);

			$new = true;
			$method = "new";
			$id = "";
			if (isset($_GET["id"]) && !empty($_GET["id"])){
				$id = $_GET["id"];
				$new = false;
				$method = "update";
			}
			
			$eslipSettings = $this->xmlApi->getElementValue("configuration");
			$pluginUrl = (string)$eslipSettings->pluginUrl;
			if ( !isset($pluginUrl) || empty($pluginUrl) ){
				$pluginUrl = "http://".$_SERVER['HTTP_HOST']."/eslip-plugin/eslip/";	
			}
			
			$buttonFixed = array();
			$button = new stdClass();
			if (!empty($id)){
				$button = $this->xmlApi->getElementById("buttonStyle",$id);
				$button->id = $button->attributes()->id;
				$button->id = $button->attributes()->id;
				$button->logo_url = $pluginUrl . 'frontend/img/icons/' . (String)$button->logo;
				//aca
				foreach ($button as $key => $value){
					if ( is_object($value) ){
						$buttonFixed[$key] = (string)$value;
					}
				}
			}

			$data = array(
				"labels" => $labels,
				"new" => $new,
				"method" => $method,
				"button" => $buttonFixed
			);

			$this->response($data);
		}

		protected function saveIdProviderButton(){

			$result = "ERROR";

			if ($_POST["method"] == "new"){
				// check id provider associated
				$button = $this->xmlApi->getElementById("buttonStyle",$_POST["id"]);
				if ( !empty($button) ){
					// id exists
					$data = array(
						"status" => "ERROR",
						"message" => sprintf(messageErrorIdExists,$_POST["id"]),
						"id" => $_POST["id"]
					);

					$this->response($data);
				}
			}

			if ( ! empty($_FILES['logo']['name']) ){

				// check image type
				$imgType = $_FILES['logo']['type'];
				$imgType = explode("/", $imgType);
				$isImage = exif_imagetype($_FILES['logo']['tmp_name']) || $imgType[0] === 'image';

				if (! $isImage){
					$data = array(
						"status" => "ERROR",
						"message" => sprintf(messageErrorImageType,$_FILES['logo']['type']),
						"id" => $_POST["id"]
					);

					$this->response($data);
				}

				$nameParts = explode('.', $_FILES['logo']['name']);
				$logo = $_POST["id"].'.'.$nameParts[1];
				$targetFilepath = '../frontend/img/icons/' . $logo;

				if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetFilepath)) {
					$_POST["logo"] = $logo;

					//crear uno nuevo si no existe
					if ($_POST["method"] == "new"){
						
						$this->newIdProviderButton();
						
					}else{
						//update idprovider button data
						$this->updateIdProviderButton();
					}

					$result = "SUCCESS";
				}
			}else if ($_POST["method"] == "update"){
				$button = $this->xmlApi->getElementById("buttonStyle",$_POST["id"]);
				$_POST["logo"] = (string)$button->logo;
				$this->updateIdProviderButton();
				$result = "SUCCESS";
			}

			$data = array(
				"status" => $result,
				"id" => $_POST["id"]
			);

			$this->response($data);
		}

		protected function newIdProviderButton(){

			$this->xmlApi->addElement(
			
				$_POST["id"],
				
				array(
					"logo",
					"textColor",
					"backgroundColor"
				),
				
				array(
					$_POST["logo"],
					$_POST["textColor"],
					$_POST["backgroundColor"]
				),
				
				"buttonStyle",
				
				"buttonStyles"
			);
		}

		protected function updateIdProviderButton(){
			
			$this->xmlApi->updateElementById(
				
				array(
					"logo",
					"textColor",
					"backgroundColor"
				),
				
				array(
					$_POST["logo"],
					$_POST["textColor"],
					$_POST["backgroundColor"]
				),
				
				"buttonStyle",
				
				$_POST["id"]
			);
		}

		protected function deleteIdProviderButton(){
			
			$idProvider = $this->xmlApi->getElementById("identityProvider", $_POST["id"]);
			if (!empty($idProvider)){
				$data = array(
					"status" => "ERROR",
					"message" => sprintf(messageErrorIdAssociated,$_POST["id"]),
					"id" => $_POST["id"]
				);

				$this->response($data);
			}

			$button = $this->xmlApi->getElementById("buttonStyle",$_POST["id"]);
			$logo = '../frontend/img/icons/' . (string)$button->logo;
			
			if (file_exists ($logo)){
				unlink($logo);	
			}

			$this->xmlApi->removeElementById("buttonStyle", $_POST["id"]);
			$result = "SUCCESS";

			$data = array(
				"status" => $result
			);

			$this->response($data);
		}

		/******************************************************
		* Wizard Setup Functions
		*******************************************************/

		protected function getLanguages(){
			$eslipLangs = $this->xmlApi->getElementList("language");
			$this->response($eslipLangs);	
		}
		
		protected function runFullWizard(){
			$runWizard = (bool)(String)$this->xmlApi->getElementValue("runWizard","configuration");
			$data = array(
				"runFullWizard" => $runWizard
			);
			$this->response($data);
		}

		protected function getWizardData(){


			if ( isset($_POST["lang"]) ){
				$selectedLang = $_POST["lang"];
			}else{
				$selectedLang = $this->xmlApi->getElementListByFieldValue("selected", "1", "language");	
				$selectedLang = (empty($selectedLang) || empty($selectedLang[0]->code )) ? getSystemLang() : (String)$selectedLang[0]->code;
			}
			
			$labels = array(
				"createAdminUser" => CreateAdminUser,
				"adminUser" => AdminUser,
				"adminPass" => AdminPass,
				"adminPassConfirm" => AdminPassConfirm,
				"generalConfigs" => GeneralConfigs,
				"siteUrl" => SiteUrl,
				"callbackUrl" => CallbackUrl,
				"pluginUrl" => PluginUrl,
				"idProviders" => IdProviders,
				"clientId" => ClientId,
				"clientSecret" => ClientSecret,
				"scopeRequired" => ScopeRequired,
				"scopeOptional" => ScopeOptional,
				"loginWidget" => LoginWidget,
				"widgetWidth" => WidgetWidth,
				"widgetRows" => WidgetRows,
				"widgetColumns" => WidgetColumns,
				"buttonLabel" => ButtonLabel,
				"HelpSiteUrl" => HelpSiteUrl,
				"HelpCallbackUrl" => HelpCallbackUrl,
				"HelpPluginUrl" => HelpPluginUrl,
				"HelpScopeRequired" => HelpScopeRequired,
				"HelpScopeOptional" => HelpScopeOptional,
				"HelpClientId" => HelpClientId,
				"HelpClientSecret" => HelpClientSecret,
				"HelpScope" => HelpScope,
				"HelpWidgetWidth" => HelpWidgetWidth,
				"HelpWidgetRows" => HelpWidgetRows,
				"HelpWidgetColumns" => HelpWidgetColumns,
				"HelpWidgetButtonLabel" => HelpWidgetButtonLabel,
				"yes" => YesLabel,
				"no" => NoLabel,
				"scope" => Scope,
				"next" => next,
				"previous" => previous,
				"cancel" => cancel,
				"finish" => finish
			);

			// Configuration
			$eslipSettings = $this->xmlApi->getElementValue("configuration");
			$settings = array(
				"adminUser" => (string)$eslipSettings->adminUser,
				"siteUrl" => (string)$eslipSettings->siteUrl,
				"callbackUrl" => (string)$eslipSettings->callbackUrl,
				"pluginUrl" => (string)$eslipSettings->pluginUrl,
				"runFullWizard" => (bool)(string)$eslipSettings->runWizard
			);

			$loginWidget = $eslipSettings->loginWidget->children();
			$loginWidgetFixed = array();

			foreach ( $loginWidget as $key => $value){
					$loginWidgetFixed[$key] = (string)$value;
			}

			$idProvidersData = $this->getIdProviders();
			$idProviders = $idProvidersData["idProviders"];
			$openIdProvider = $idProvidersData["openIdProvider"];
			
			$data = array(
				"selectedLang" => $selectedLang,
				"labels"  => $labels,
				"settings" => $settings,
				"loginWidget" => $loginWidgetFixed,
				"idProviders" => $idProviders,
				"openIdProvider" => $openIdProvider
			);

			$this->response($data);	
		}

		protected function saveConfiguration(){

			//update first time config
			$this->xmlApi->setElementValue("runWizard", "0", "configuration");
			
			//update language
			$this->xmlApi->updateElement(array("selected"), array("0"), "language");
			$this->xmlApi->setElementListByFieldValue("code", $_POST["language"], "language",null,"selected","1");

			//update admin user
			if (isset($_POST["adminUser"])){
				$this->xmlApi->setElementValue("adminUser", $_POST["adminUser"], "configuration");
				$this->xmlApi->setElementValue("adminPass", getEncrypted($_POST["adminPass"]), "configuration");	
			}
			
			
			//update configuration
			$this->xmlApi->setElementValue("siteUrl", $_POST["siteUrl"], "configuration");
			$this->xmlApi->setElementValue("callbackUrl", $_POST["callbackUrl"], "configuration");
			$this->xmlApi->setElementValue("pluginUrl", $_POST["pluginUrl"], "configuration");
			
			$this->xmlApi->updateElement(
				array(
					"widgetWidth",
					"widgetRows",
					"widgetColumns",
					"buttonLabel"
				), 
				array(
					$_POST["widgetWidth"].$_POST["widgetWidthUnit"],
					$_POST["widgetRows"],
					$_POST["widgetColumns"],
					$_POST["buttonLabel"]
				), 
				"loginWidget"
			);

			// update id providers and open id provider
			$this->updateIdProvidersImpl();

			$data = array(
				"status" => "SUCCESS"
			);

			$this->response($data);	
		}

		protected function getWizardEndData(){

			$labels = array(
				"wizardEndTitle" => WizardEndTitle,
				"wizardEndSubTitle" => WizardEndSubTitle,
				"loginFormDesc" => LoginFormDesc
			);

			$eslipSettings = $this->xmlApi->getElementValue("configuration");
			$pluginUrl = (string)$eslipSettings->pluginUrl;

			$data = array(
				"labels" => $labels,
				"pluginUrl"  => $pluginUrl,
				"pluginCss" => $pluginUrl.$_GET["cssUri"],
				"pluginJs" => $pluginUrl.$_GET["jsUri"],
				"eslipDivId" => $_GET["eslipDiv"]
			);

			$this->response($data);	
		}

	}
	
	// Inicializar la API
	new BackendServiceApi($xmlApi);