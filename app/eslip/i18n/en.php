<?php

/* WIZARD */

//step titles
define("CreateAdminUser","Create Admin User");
define("GeneralConfigs","General Settings");
define("IdProviders","Identity Providers");

//labels
define("AdminUser", "User Name");
define("AdminPass", "Password");
define("AdminPassConfirm", "Password Confirm");

//texts
define("LoginFormDesc","You must copy the following code on your page Login");

//buttons
define("next","Next");
define("previous","Previous");
define("cancel","Cancel");
define("finish","Finish");

//dialogs
define("SelectLangTitle","Select Language");
define("WizardEndTitle","Configuration Ends");
define("WizardEndSubTitle","Thanks for using ESLIP Plugin");

/* ADMIN */

//title
define("adminTitle", "ESLIP Admin");
define("Login", "Account Login");

//menu
define("ConfigUser","Usuer Settings");
define("LanguagesConfig", "Language Settings");
define("LoginWidget", "Social Login Widget");
define("IdProvidersButtons", "Providers Buttons");

//labels
define("SiteUrl", "Site URL");
define("CallbackUrl", "Callback URL");
define("PluginUrl", "ESLIP Plugin URL");
define("ClientId", "Client ID");
define("ClientSecret", "Client Secret");
define("Scope", "Scope");
define("Id", "ID");
define("Name", "Name");
define("Label", "Button Label");
define("Active", "Active");
define("Oauth", "OAuth Version");
define("RequestTokenUrl", "Initial token endpoint URL");
define("DialogUrl", "Authorization endpoint URL");
define("AccessTokenUrl", "Access token endpoint URL");
define("ApiCallParameters", "Parameters for obtaining user resources");
define("AuthorizationHeader", "Parameters in the header of the request");
define("UrlParameters", "Parameters in the URL");
define("HasAccessTokenExtraParameter", "Has access token extra parameter");
define("AccessTokenExtraParameterName", "Access token extra parameter name");
define("UserDataUrl", "Endpoint URL for obtaining user resources");
define("FormUrl", "Form Url");
define("ScopeRequired", "Required Scope");
define("ScopeOptional", "Optional Scope");
define("UserDataIdKey", "Resource used as unique identifier of the user");
define("Immediate", "Immediate Authentication");
define("YesLabel", "Yes");
define("NoLabel", "No");
define("LangName", "Language Name");
define("DownloadLangFile", "Download the template language file");
define("UploadLangFile", "Upload the translated language file");
define("TranslateLangFile", "Translate the downloaded file and rename it with the appropriate two letters ISO language code in lower case (<a target='_blank' href='http://www.sitepoint.com/web-foundations/iso-2-letter-language-codes/'>http://www.sitepoint.com/web-foundations/iso-2-letter-language-codes/</a>)");
define("Logo", "Logo");
define("TextColor", "Text Colot");
define("BackgroundColor", "Background Color");
define("ButtonPreview", "Button Preview");
define("WidgetWidth", "Widget Width");
define("WidgetRows", "Number of Rows");
define("WidgetColumns", "Number of Columns");
define("ButtonLabel", "Buttons Label");
define("SelectOption", "Select an option");
define("StandardView", "Standard View");
define("AdvancedView", "Advanced View");

//dataTable
define("Procesando", "Loading...");
define("dtsLoadingRecords", "Loading...");
define("dtsLengthMenu", "Display _MENU_ records per page");
define("dtsZeroRecords", "No records were found");
define("dtsInfo", "Showing _START_ to _END_ of _TOTAL_ entries");
define("dtsInfoEmpty", "No records were found");
define("dtsInfoFiltered","(filtering from _MAX_ records)");
define("dtsSearch", "Search:");
define("dtsFirst", "First");
define("dtsPrevious", "Previous");
define("dtsNext", "Next");
define("dtsLast", "Last");

//buttons
define("btnNew","New");
define("btnEdit","Edit");
define("btnDelete","Delete");
define("btnSave","Save");
define("btnCancel","Cancel");
define("btnConfirm","Confirm");
define("btnLogin","Log In");
define("btnLogout","Log Out");
define("btnWizard","Configuration Wizard");
define("btnDownload", "Download");
define("btnUpload", "Upload");

//confirm
define("deleteConfirm", "These item will be permanently deleted. Are you sure?");

//messages
define("messageSuccess","Data successfully saved");
define("messageError","An error occurred when saving data");
define("messageLoginInfo","If you have not registered your user you must run the ");
define("messageLoginError","An error occurred when trying to log in, please check your input data");
define("messageDeleteOpenID", "OpenID could not be removed");
define("messageMustSelectProvider", "You must select an Identity Provider");
define("messageMustSelectProviderButton", "You must select an Identity Provider Button");
define("messageErrorIdExists", "Failed to save the data: The ID %s already exists.");
define("messageErrorIdAssociated", "Unable to delete data: The ID %s is associated with an Identity Provider.");
define("messageErrorImageType", "Image type %s is not supported");
define("messageRequired", "This field is required.");
define("messageRemote", "Please fix this field.");
define("messageUrl", "Please enter a valid URL.");
define("messageNumber", "Please enter a valid number.");
define("messageDigits", "Please enter only digits.");
define("messageEqualTo", "Please enter the same value again.");
define("messageMinlength", "Please enter at least {0} characters.");
define("messageMin", "Please enter a value greater than or equal to {0}.");

/* ERRORS */

//textos de errores de excepciones de la api
define("CurlError", "ESLIP ERROR: CURL PHP extension required.");
define("SessionError", "ESLIP ERROR: It was not possible to start the PHP session.");
define("ParametersErrorEslipDataInConstruct", "ESLIP ERROR: No required parameters passed. Need Eslip data in Constructor.");
define("ParametersErrorIPInConstruct", "ESLIP ERROR: No required parameters passed. Identity Provider in Constructor.");
define("NoConfigFileError", "ESLIP ERROR: No XML configuration file setted.");

//openid
define("ParametersErrorURLInOpenIDConstruct", "ESLIP ERROR: No required parameters passed. Need URL in OpenID Constructor.");
define("ParametersErrorIdentityInOpenIDDiscover", "ESLIP ERROR: No required parameters passed. Identity in OpenID Discover.");
define("URIErrorInOpenIDDiscover", "ESLIP ERROR: Could not discover an OpenID identity server endpoint.");
define("CancelInfo", "ESLIP INFO: User has canceled authentication.");
define("NoIdResError", "ESLIP ERROR: There was an error in the verification of the authentication. Was obtained a mode different than id_res.");
define("DifferentsReturnURLError", "ESLIP ERROR: There was an error in the verification of the authentication. Return URLs must match.");
define("ImmediateRedirecting", "Unable to perform the inmediate authentication. Redirecting...");
define("emptyErrorMessage", "The OpenID identifier can not be empty");

//oauth
define("CurlResponseError", "ESLIP ERROR: It was not possible to access the %s: It was returned an unexpected response status %s. Response: %s");
define("OAuthVersionError", "ESLIP ERROR: %s is not a supported version of the OAuth protocol.");
define("NoAccessTokenError", "ESLIP ERROR: Can not perform API call because the access token doesn't exist.");
define("RequestTokenDeniedError", "ESLIP ERROR: The Request Token was denied.");
define("NotReturnedAccessTokenError", "ESLIP ERROR: It was not returned the access token or secret.");
define("ExpiryTimeError", "ESLIP ERROR: OAuth server did not return a supported type of access token expiry time.");
define("AuthorizationErrorWithCodeError", "ESLIP ERROR: There was an error in the authorization. OAuth error code: %s.");
define("AuthorizationError", "ESLIP ERROR: There was an error in the authorization. It was not returned the OAuth dialog code.");
define("ServerNotReturnAccessTokenError", "ESLIP ERROR: OAuth server did not return the access token.");
define("ServerNotReturnAccessTokenWhiteCodeError", "ESLIP ERROR: It was not possible to retrieve the access token: it was returned the error: %s.");

//Popover help
define("HelpSiteUrl", "Website URL where the plugin is including. For example: http://www.example.com");
define("HelpCallbackUrl", "URL to where it should return once the identification has performed. Here the developer process the data returned by the plugin as a result of its interaction with the identity provider. The data are sent to this URL by the HTTP POST method in addition to being stored in the PHP Session. For example: http://www.example.com/index.php");
define("HelpPluginUrl", "URL where the plugin is hosted within your website. For example: http://www.example.com/eslip/");
define("HelpLanguage", "Here you select the language you want display the text of the plugin interface and the dashboard.");

define("HelpScopeRequired", "Attributes that you want to require the identity provider. Must be separated by commas.");
define("HelpScopeOptional", "Attributes that you want to require the identity provider but are optional. The identity provider may or may not return them. Different scopes that can be request can be viewed at <a target='_blank' href='http://openid.net/specs/openid-attribute-properties-list-1_0-01.html#Prop_List'>http://openid.net/specs/openid-attribute-properties-list-1_0-01.html#Prop_List</a>. For each scope must be discard first portion corresponding to http://openid.net/schema/ which is repeated in all scopes, and only to use what is before.");
define("HelpClientId", "Identifier provided by the OAuth API for external application created in the corresponding identity provider.");
define("HelpClientSecret", "Secret key provided by the OAuth API for external application created in the corresponding identity provider.");
define("HelpScope", "Resources that are desired and its owner who is authenticated through the identity provider must be authorized to be granted.");
define("HelpFormUrl", "Relative address within the plugin folder where is located the form where the user enters their OpenID identifier.");
define("HelpImmediate", "Here you set if authentication is to be performed using immediate mode.");

define("HelpId", "Unique identifier for each identity provider. ESLIP provides a list of preloaded  identifiers. The plugin provides a button style along with the icon for each identifier.");
define("HelpName", "Name to identify the identity provider within the plugin manager.");
define("HelpLabel", "Text displayed in the corresponding identity provider button within the widget.");
define("HelpActive", "With this option you can enable or disable this identity provider to not be visible in the widget.");
define("HelpOauth", "Protocol version that supports the identity provider OAuth server.");
define("HelpRequestTokenUrl", "OAuth server URL of the identity provider for the initial token request when working with OAuth 1.0 and 1.0a servers.");
define("HelpDialogUrl", "Login form URL of the identity provider. It will redirect the resource owner to that URL to login and authorize or not the application created in this identity provider to access the requested resources.");
define("HelpAccessTokenUrl", "OAuth server URL of the identity provider which returns the access token.");
define("HelpApiCallParameters", "String with the parameters that are sent to the API of the identity provider, one of these must be the access token.");
define("HelpAuthorizationHeader", "Sets whether the identity provider API require, when performing the initial token request, that you send the parameters in the HTTP request header. If you are unsure of it, set the option to 'Yes'.");
define("HelpUrlParameters", "Sets whether the identity provider API require, when performing the initial token request, that you send the parameters in the URL. If you are unsure of it, set the option to 'Yes'.");
define("HelpHasAccessTokenExtraParameter", "Indicates whether the identity provider OAuth API returns an extra parameter in the response to the request for access token required for authentication.");
define("HelpAccessTokenExtraParameterName", "Name of the key in the response to the request for access token to access the extra parameter if the identity provider returns one. An example is Yahoo!, which returns the necessary resources requested for 'xoauth_yahoo_guid' parameter.");
define("HelpUserDataUrl", "URL that provides the identity provider OAuth API to which you are making the request for the required resources.");
define("HelpUserDataIdKey", "Name of the key in the object that returns the identity provider response, to the resource request, which is used to access the unique identifier of the resource owner. If the key exists with the name entered in the object returned by the identity provider, the value is sent to the 'callback URL' established by the developer, on an object with the rest of the funds obtained under the key 'id'. This object is also stored in the PHP session.");

define("HelpWidgetWidth", "Wide that have the widget. It can be set in pixels or percentage depending on the needs of each developer.");
define("HelpWidgetRows", "Number of rows of buttons to display in the widget. If you opt for the 'auto' option, all buttons are displayed vertically obeying the number of columns fixed regardless of the number of rows used. Whereas if greater or equal to one value and the number of buttons to display the widget exceeds capacity, according to the number of columns and rows set, a kind of gallery with sliding horizontal panels set is created, where for each panel the corresponding number of buttons is displayed.");
define("HelpWidgetColumns", "Number of columns of buttons displayed in the widget. It is important to the value set here, because with this amount the width of each button displayed in the widget is calculated.");
define("HelpWidgetButtonLabel", "Defines whether to display in the the buttons only the icon of the correspondent identity provider or will be displayed next to it a descriptive text called label.");

define("HelpButtonID", "It must be unique and used to identify each of identity providers.");
define("HelpButtonLogo", "Logo of the identity provider that is associated with this identifier.");
define("HelpButtonTextColor", "Color to be used in the text inside the identity provider button that is displayed in the widget when it is active.");
define("HelpButtonBackgroundColor", "Color to be used as background in the identity provider button that is displayed in the widget when it is active.");

?>