//**********************************************************
// Variables Definitions
//**********************************************************

var SERVICES_URL = "backend_services/";
var ESLIP_SERVICES_URL = "../eslip_frontend_services/";
var CSS_URI = "frontend/eslip_plugin.css";
var JS_URI = "frontend/eslip_plugin.js";
var ESLIP_DIV = "ESLIP_Plugin";
var ADMIN_URL = "admin.php";
var TEMPLATES_COMMON_DIR = "views/common/";
var TEMPLATES_BASE_DIR = "views/wizard/";
var TEMPLATES = {
	"wizard": TEMPLATES_BASE_DIR + "wizard.html",
	"openIdProvider": TEMPLATES_COMMON_DIR + "openIdProvider.html", 
	"idProviders": TEMPLATES_COMMON_DIR + "idProviders.html",
	"wizardEnd": TEMPLATES_BASE_DIR + "wizardEndDialog.html"
};
var SERVICES = {
	"runFullWizard": "runFullWizard",
	"getLanguages": "getLanguages",
	"getWizardData": "getWizardData",
	"saveConfiguration" : "saveConfiguration",
	"getWizardEndData" : "getWizardEndData"
};

var $selectLanguageDialog;
var $wizardContainer;
var $selectLangContainer;
var $wizard;

//**********************************************************
// Document Ready
//**********************************************************

$(function() {

	initSelectors();

	apiGet(SERVICES.runFullWizard, {}, function(data){
		if (data.runFullWizard){

			initSelectLanguageDialog();

			populateLanguageSelect();

			showSelectLanguageDialog();

		}else{

			loadWizardContent();
		}
	});

});

//**********************************************************
// Wizard Setup Functions
//**********************************************************

function initSelectors(){
	$selectLanguageDialog = $( "#dialog-lang" );
	$wizardContainer = $("#wizardContainer");
	$selectLangContainer = $( "#dialog-lang" ).find(".modal-body");//$("#selectLangContainer");
}

function initSelectLanguageDialog(){

	$selectLanguageDialog.find(".btn-select").click(function(){
		loadWizardContent();
		$selectLanguageDialog.modal("hide");
	});
}

function populateLanguageSelect(){
	
	apiGet(SERVICES.getLanguages, {}, function(languages){

		var $selectLang = '<select id="selectLang" class="form-control">';
		
		var selected = '';
		$.each(languages, function(i, lang){
			selected = (lang.selected == 1) ? "selected" : "";
			$selectLang += '<option value="'+lang.code+'" '+selected+'>'+lang.name+'</option>';
		});

		$selectLang += '</select>';

		$selectLangContainer.html($selectLang);
	});
}

function showSelectLanguageDialog(){
	$selectLanguageDialog.modal("show");
}

function prepareWizard(data){
	$wizardContainer.hide();
	if (!data.settings.runFullWizard){
		$wizardContainer.find("#step-1").remove();	
	}
}

function loadWizardContent(){

	var selectedLang = $("#selectLang").val();

	init({lang: selectedLang});

	apiPost(SERVICES.getWizardData, {lang: selectedLang}, function(data){

		$wizardContainer.loadTemplate(TEMPLATES.wizard, data, {
			overwriteCache: true,
			complete: function(){

				// mostrar o no el wizard completo
				prepareWizard(data);

				$.each(data.idProviders,function(i, p){ 
					data.idProviders[i].labels = data.labels;
					data.idProviders[i].idClientId = data.idProviders[i].id+'_clientId';
					data.idProviders[i].idClientSecret = data.idProviders[i].id+'_clientSecret';
				});
				$("#idProvidersContainer").loadTemplate(TEMPLATES.idProviders, data.idProviders, {
					overwriteCache: true,
					complete: function(){

						data.openIdProvider.labels = data.labels;
						$("#idProvidersContainer").loadTemplate(TEMPLATES.openIdProvider, data.openIdProvider, {
							prepend: true,
							overwriteCache: true,
							complete: function(){
								loadWizardContentCallback(data);
							}
						});
					}
				});

			}
		});
		
	});

}

function loadWizardContentCallback(data){

	initDefaultValues();

	initWizard(data);

	initWizardFormValidation(data);

	bindWizardEvents();

	sortIdProviders();
	
	initPopover();

	initSwitchOnOff();

}

function initDefaultValues(){
	suggestSiteUrl($("#siteUrl"));
	suggestPluginUrl($("#pluginUrl"));
}

function initWizard(data){

	$wizard = $('#wizard');

	$wizardContainer.show();

	var options = {
    	//show: true,
    	keyboard : false,
		contentHeight : 500,
		contentWidth : 800,	
		backdrop: 'static',
		buttons: {
			cancelText: data.labels.cancel,
			nextText: data.labels.next,
			backText: data.labels.previous,
			submitText: data.labels.finish,
			submittingText: "..."
		}
    };

    $wizard = $wizard.wizard(options);

    $wizard.find(".wizard-card").outerHeight($wizard.find(".wizard-card-container").height());

    $wizard.show();
}

function initWizardFormValidation(data){
						
	$wizard.find("#form1").validate({
		rules: {
			adminUser: "required",
			adminPass: {
				required: true,
				minlength: 6
			},
			adminPassConfirm: {
				equalTo: "#adminPass"
			}
		}
	});
	
	$wizard.find("#form2").validate({
		rules: {
			siteUrl: {
				required: true,
				urlCustom: true
			},
			callbackUrl: {
				required: true,
				urlCustom: true
			},
			pluginUrl: {
				required: true,
				urlCustom: true
			}
		}
	});

	$wizard.find("#form3").validate({
		rules: {
			'clientId[]': {
				required: {
					depends: function(element) {
						return parseInt($(element).parents(".idProviderData").attr("active")) === 1;
					}
				}
		    },
		    'clientSecret[]': {
				required: {
					depends: function(element) {
						return parseInt($(element).parents(".idProviderData").attr("active")) === 1;
					}
				}
		    }
		}
	});

	$wizard.find("#form4").validate({
		rules: {
			widgetWidth: {
				required: true,
      			number: true
			},
			widgetRows: {
				required: true,
      			number: true,
      			min: 0
			},
			widgetColumns: {
				required: true,
      			number: true,
      			min: 1
			},
			
		}
	});
	
	if ( $.exists($wizard.find("#step-1")) ){
		$wizard.cards[data.labels.createAdminUser].on("validate", function(card) {
		    if (card.el.find("form").valid()){
				return true;
			}else{
				return false;
			}
		});
	}
	

	$wizard.cards[data.labels.generalConfigs].on("validate", function(card) {
	    if (card.el.find("form").valid()){
			return true;
		}else{
			return false;
		}
	});

	$wizard.cards[data.labels.idProviders].on("validate", function(card) {
	    if (card.el.find("form").valid()){
			return true;
		}else{
			return false;
		}
	});

	$wizard.cards[data.labels.loginWidget].on("validate", function(card) {
	    if (card.el.find("form").valid()){
			return true;
		}else{
			return false;
		}
	});

	$wizard.cards[data.labels.loginWidget].on("selected", function(card) {
		initLoginWidget(data);
	});
	

}

function bindWizardEvents(){

	//wizard finish
	$wizard.on("submit", function(wizard) {

		$("#siteUrl").val( formatUrl( $("#siteUrl").val() ) );
		$("#pluginUrl").val( formatUrl( $("#pluginUrl").val() ) );

		var $formData = $wizard.serialize();
		apiPost(SERVICES.saveConfiguration, $formData, function(data){
			$wizard.submitSuccess();
			$wizard.close();
			wizardEnd();
		});
	});
}

function wizardEnd(){
	var params = {cssUri: CSS_URI, jsUri: JS_URI, eslipDiv: ESLIP_DIV};
	loadContent(SERVICES.getWizardEndData, params, TEMPLATES.wizardEnd, $wizardContainer, showWizardEndDialog);
}

function showWizardEndDialog(){

	$("#dialog-wizard-end").find(".btn-end").click(function(){
		$("#dialog-wizard-end").modal("hide");
		window.location.href = ADMIN_URL;
	});

	$("#dialog-wizard-end").modal("show");
}

/*** Helper Functions ***/

function validateEmpty(el) {
    var name = el.val();
    var retValue = {};

    if (name == "") {
        retValue.status = false;
        retValue.msg = "Please enter a name";
    }
    else {
        retValue.status = true;
    }

    return retValue;
}

function findWithAttr(array, attr, value) {
    for(var i = 0; i < array.length; i += 1) {
        if(array[i][attr] === value) {
            return i;
        }
    }
}

function initLoginWidget(data){

	var activeIds = [];
	$.each($('#active[value="1"]'), function(i, idP){
		activeIds.push($(idP).parents('.idProvider').attr('id'));
	});

	data.identityProviders = [];
	for (var i in activeIds){
		if (activeIds[i] == 'openid'){
			data.identityProviders.push(data.openIdProvider);
		}else{
			var j = findWithAttr(data.idProviders, 'id', activeIds[i]);
			data.identityProviders.push(data.idProviders[j]);
		}
	}

	initLoginWidgetValues(data);

	bindLoginWidgetPreviewEvents(data);

	ESLIP.renderWidget(data);

	preventLoginButtonClick();

}