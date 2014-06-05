//**********************************************************
// Variables Definitions
//**********************************************************

var CSS_URI = "frontend/eslip_plugin.css";
var JS_URI = "frontend/eslip_plugin.js";
var LOGO_URI = "../frontend/img/icons/";
var ESLIP_DIV = "ESLIP_Plugin";
var SERVICES_URL = "backend_services/";
var ESLIP_SERVICES_URL = "../eslip_frontend_services/";
var TEMPLATES_COMMON_DIR = "views/common/";
var TEMPLATES_BASE_DIR = "views/admin/";
var TEMPLATES = {
	"generalConfig": TEMPLATES_BASE_DIR + "generalConfig.html",
	"userConfig": TEMPLATES_BASE_DIR + "userConfig.html",
	"idProviders": TEMPLATES_BASE_DIR + "idProviders.html",
	"openIdProviderStandar": TEMPLATES_COMMON_DIR + "openIdProvider.html", 
	"idProvidersStandar": TEMPLATES_COMMON_DIR + "idProviders.html",
	"idProviderForm": TEMPLATES_BASE_DIR + "idProviderForm.html",
	"openIdProviderForm": TEMPLATES_BASE_DIR + "openIdProviderForm.html",
	"languagesConfig": TEMPLATES_BASE_DIR + "languagesConfig.html",
	"loginWidget": TEMPLATES_BASE_DIR + "loginWidget.html",
	"idProvidersButtons": TEMPLATES_BASE_DIR + "idProvidersButtons.html",
	"idProviderButtonForm": TEMPLATES_BASE_DIR + "idProviderButtonForm.html"
};
var SERVICES = {
	"login": "login",
	"logout": "logout",
	"getGeneralConfigData": "getGeneralConfigData",
	"saveGeneralConfig" : "saveGeneralConfig",
	"getUserConfigData" : "getUserConfigData",
	"saveUserConfig" : "saveUserConfig",
	"getIdProvidersData": "getIdProvidersData",
	"getIdProvidersDataTable": "getIdProvidersDataTable",
	"getIdProviderData" : "getIdProviderData",
	"updateIdProviders" : "updateIdProviders",
	"saveIdProvider" : "saveIdProvider",
	"deleteIdProvider" : "deleteIdProvider",
	"getLanguagesConfigData" : "getLanguagesConfigData",
	"uploadLangFile" : "uploadLangFile",
	"compileLanguageFile" : "compileLanguageFile",
	"getLoginWidgetData": "getLoginWidgetData",
	"saveLoginWidget": "saveLoginWidget",
	"getIdProvidersButtonsData": "getIdProvidersButtonsData",
	"getIdProvidersButtonsDataTable": "getIdProvidersButtonsDataTable",
	"getIdProviderButtonData": "getIdProviderButtonData",
	"saveIdProviderButton": "saveIdProviderButton",
	"deleteIdProviderButton" : "deleteIdProviderButton"
};

var $loginButton;
var $loginForm;
var $content;
var oTable;
var oTableSettings = {};
var selectedTab = 0;

var oTableIdsButtons;
var oTableIdsButtonsSettings = {};

var editLabel = 'Edit';
var deleteLabel = '';
var messageDeleteOpenID = '';

//**********************************************************
// Document Ready
//**********************************************************

$(function() {

	initSelectors();

	bindLoginEvents();

	bindLogoutEvents();

	initMenuOptions();
});

//**********************************************************
// Common Functions
//**********************************************************

function initSelectors(){
	$loginButton = $("#login");
	$loginForm = $("#loginForm");
	$content = $("#page-wrapper");
}

//**********************************************************
// Login Functions
//**********************************************************

function bindLoginEvents(){

	$loginButton.click(function(){
		$data = $loginForm.serialize();
		apiPost(SERVICES.login, $data, function(data){
			if (data.status == "ERROR"){
				$(".errorMessage").show();
			}else{
				window.location.reload(true);
			}
		});
	});

	$loginForm.find('input').keypress(function(e) {
		if(e.which == 13) {
			$(this).blur();
			$loginButton.focus().click();
		}
	});
}

function bindLogoutEvents(){
	$("#logout").click(function(){
		apiPost(SERVICES.logout, {}, function(){
			window.location.reload(true);
		});
	});
}

//**********************************************************
// Admin Menu Functions
//**********************************************************

function initMenuOptions(){
	$("#idProviders").click(function(){
		selectMenuItem($(this));
		loadIdProvidersContent();
	});
	
	$("#configUser").click(function(){
		selectMenuItem($(this));
		loadUserConfigContent();
	});
	
	$("#generalConfig").click(function(){
		selectMenuItem($(this));
		loadGeneralConfigContent();
	});

	$("#languagesConfig").click(function(){
		selectMenuItem($(this));
		loadLanguagesConfigContent();
	});

	$("#loginWidget").click(function(){
		selectMenuItem($(this));
		loadLoginWidgetContent();
	});

	$("#idProvidersButtons").click(function(){
		selectMenuItem($(this));
		loadIdProvidersButtonsContent();
	});

	$("#generalConfig").click();
}

function selectMenuItem($element){
	$(".side-nav li").removeClass("active");
	$element.parents("li").addClass("active");
}

//**********************************************************
// Admin General Config Functions
//**********************************************************

function loadGeneralConfigContent(){
	loadContent(SERVICES.getGeneralConfigData, {}, TEMPLATES.generalConfig, $content, loadGeneralConfigContentCallback);
}

function loadGeneralConfigContentCallback(data){
	
	initPopover();

	initDefaultValues();

	initLanguageSelect(data);

	initGeneralConfigFormValidation();

	bindGeneralConfigEvents();
}

function initDefaultValues(){
	suggestSiteUrl($("#siteUrl"));
	suggestPluginUrl($("#pluginUrl"));
}

function initLanguageSelect(data){
	$("#language").val(data.settings.selectedLang);
	
	$.each( $("#language").find("option"), function(i, element){
		$(element).html($(element).text());
	});
}

function initGeneralConfigFormValidation(){
	$("#form").validate({
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
}

function bindGeneralConfigEvents(){
	$('#save').off("click").on("click", function() {
		if ($("#form").valid()){
			$("#siteUrl").val( formatUrl( $("#siteUrl").val() ) );
			$("#pluginUrl").val( formatUrl( $("#pluginUrl").val() ) );
			$data = $("#form").serialize();
			submitData(SERVICES.saveGeneralConfig, $data);
		}
	});
	
	$('#cancel').off("click").on("click", function() {
		$("#generalConfig").click();
	});
}

//**********************************************************
// Admin User Config Functions
//**********************************************************

function loadUserConfigContent(){
	loadContent(SERVICES.getUserConfigData, {}, TEMPLATES.userConfig, $content, loadUserConfigContentCallback);
}

function loadUserConfigContentCallback(data){

	initPopover();

	initUserConfigFormValidation();

	bindUserConfigEvents();
}

function initUserConfigFormValidation(){
	$("#form").validate({
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
}

function bindUserConfigEvents(){
	$('#save').off("click").on("click", function() {
		if ($("#form").valid()){
			$data = $("#form").serialize();
			submitData(SERVICES.saveUserConfig, $data);
		}

	});

	$('#cancel').off("click").on("click", function() {
		$("#configUser").click();
	});
}

//**********************************************************
// Admin Languages Config Functions
//**********************************************************

function loadLanguagesConfigContent(){
	loadContent(SERVICES.getLanguagesConfigData, {}, TEMPLATES.languagesConfig, $content, loadLanguagesConfigContentCallback);
}

function loadLanguagesConfigContentCallback(data){

	initPopover();

	initLanguagesConfigFormValidation();

	bindLanguagesConfigEvents();	
}

function initLanguagesConfigFormValidation(){
	$("#uploadForm").validate({
		rules: {
			langName: "required",
			langFile: "required"
		}
	});
}

function bindLanguagesConfigEvents(){

	$("#downloadLangFile").click(function(){
		window.location.href = "download.php";
	});

	var $uploadForm = $("#uploadForm");
	$uploadForm.attr("action", SERVICES_URL+SERVICES.uploadLangFile);
	$("#uploadLangFile").click(function(){
		if ($uploadForm.valid()){
			$uploadForm.submit();
		}
	});
	
	var $hiddenUploadFrame = $("#hiddenUpload");
	$hiddenUploadFrame.load(function(){
		uploadLangFileCallback(this);
	});
}

function uploadLangFileCallback(hiddenUploadFrame) {

	var response = $(hiddenUploadFrame).contents().find("body").html();
	if (response.length) {
		// Convertir a objeto JSON
		var responseObject = eval("("+response+")");
		var params = {langFile: responseObject.file, langName: responseObject.name};
		submitData(SERVICES.compileLanguageFile, params);
	}
}

//**********************************************************
// Admin Login Widget Functions
//**********************************************************

function loadLoginWidgetContent(){
	var params = {cssUri: CSS_URI, jsUri: JS_URI, eslipDiv: ESLIP_DIV};
	loadContent(SERVICES.getLoginWidgetData, params, TEMPLATES.loginWidget, $content, loadLoginWidgetContentCallback);
}

function loadLoginWidgetContentCallback(data){
	
	initPopover();

	initLoginWidgetFormValidation();

	initLoginWidgetValues(data);

	bindLoginWidgetEvents(data);

	ESLIP.renderWidget(data);

	preventLoginButtonClick();
}

function initLoginWidgetFormValidation(){
	$("#form").validate({
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
}

function bindLoginWidgetEvents(data){
	
	$('#save').off("click").on("click", function() {
		$("#form").find("input.disabled").removeAttr('disabled');
		if ($("#form").valid()){
			$data = $("#form").serialize();
			submitData(SERVICES.saveLoginWidget, $data);
		}
	});

	$('#cancel').off("click").on("click", function() {
		$("#loginWidget").click();
	});

	bindLoginWidgetPreviewEvents(data);
}

//**********************************************************
// Admin Id Providers Functions
//**********************************************************

function loadIdProvidersStandardTemplate(data, callback){
	$.each(data.idProviders,function(i, p){ 
		data.idProviders[i].labels = data.labels;
		data.idProviders[i].idClientId = data.idProviders[i].id+'_clientId';
		data.idProviders[i].idClientSecret = data.idProviders[i].id+'_clientSecret';
	});
	$("#idProvidersContainer").loadTemplate(TEMPLATES.idProvidersStandar, data.idProviders, {
		overwriteCache: true,
		complete: function(){

			data.openIdProvider.labels = data.labels;
			$("#idProvidersContainer").loadTemplate(TEMPLATES.openIdProviderStandar, data.openIdProvider, {
				prepend: true,
				overwriteCache: true,
				complete: function(){
					callback(data);
				}
			});
		}
	});
}

function loadIdProvidersStandarContent(){
	apiGet(SERVICES.getIdProvidersData, {}, function(data){
		loadIdProvidersStandardTemplate(data, loadIdProvidersStandarContentCallback);
	});
}

function loadIdProvidersContent(){
	loadContent(SERVICES.getIdProvidersData, {}, TEMPLATES.idProviders, $content, function(data){
		loadIdProvidersStandardTemplate(data, loadIdProvidersContentCallback);
	});
}

function loadIdProvidersStandarContentCallback(data){

	sortIdProviders();
	
	initPopover();

	initSwitchOnOff();

	initIdProviderStandarValidation();

	bindIdProvidersEvents(data);
}

function loadIdProvidersContentCallback(data){

	sortIdProviders();
	
	initPopover();

	initDataTables(data);

	initSwitchOnOff();

	initTabs();

	initIdProviderStandarValidation();

	bindIdProvidersEvents(data);

	initIdProvidersButtonsDialogs(data);
}

function initTabs(){
	selectedTab = selectedTab || 0;

	$("#idProvidersTab").find("li").eq(selectedTab).find("a").click();

	$('a[data-toggle="tab"]', $("#idProvidersTab")).on('shown.bs.tab', function (e) {
		selectedTab =  $("#idProvidersTab").find("li.active").index();
	})
}

// Get the rows which are currently selected
function fnGetSelected( oTableLocal ){
	return oTableLocal.$('tr.row_selected');
}

function initDataTables(data){

	editLabel = data.labels.btnEdit;
	deleteLabel = data.labels.btnDelete;

	oTableSettings = {
		"bAutoWidth": false,
		"iDisplayLength": 10,
		"bRetrieve":true,
		"sAjaxSource": SERVICES_URL+SERVICES.getIdProvidersDataTable,
		"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
			// Add row id
			$(nRow).attr("id",aData[0]);
			buttonStylePreview(aData[0],aData[2],$(nRow).find('.rowButtonPreview'));
		},
		"aoColumnDefs": [{
			"bVisible": false,
			"aTargets": [ 0 ]
		},{
			"sClass": "text-center",
			"aTargets": [ 1 ]
		},{ 
        	"sClass": "rowButtonPreview",
        	"aTargets": [ 2 ] 
        },{
        	"sClass": "text-center",
            "mRender": function ( data, type, row ) {
            	var icon = (parseInt(data)) ? "ok" : "remove";
                return '<span class="glyphicon glyphicon-'+icon+'"></span>';
            },
            "aTargets": [ 3 ]
        },{
			"sClass": "actions",
			"mRender": function ( data, type, row ) {
				return '' +
					'<button type="button" class="btn btn-success edit" onclick="editIdProvider(\''+row[0]+'\')"><span class="glyphicon glyphicon-pencil"></span>&nbsp;'+editLabel+'</button>'+
					'&nbsp;'+
                	'<button type="button" class="btn btn-danger delete" onclick="deleteIdProvider(\''+row[0]+'\')"><span class="glyphicon glyphicon-remove"></span>&nbsp;'+deleteLabel+'</button>'
                ;
            },
			"aTargets": [ 4 ]
		}],
		"oLanguage": {
			"sProcessing": data.labels.processing,
			"sLoadingRecords": data.labels.dtsLoadingRecords,
			"sLengthMenu": data.labels.dtsLengthMenu,
			"sZeroRecords": data.labels.dtsZeroRecords,
			"sInfo": data.labels.dtsInfo,
			"sInfoEmpty": data.labels.dtsInfoEmpty,
			"sInfoFiltered": data.labels.dtsInfoFiltered,
			"sSearch": data.labels.dtsSearch,
			"oPaginate": {
				"sFirst": data.labels.dtsFirst,
				"sPrevious": data.labels.dtsPrevious,
				"sNext": data.labels.dtsNext,
				"sLast": data.labels.dtsLast
			}
		}
	};

	oTable = $('#idProviderTable').dataTable(oTableSettings);

	$('.dataTables_length label select').addClass('form-control input-sm');
	$('.dataTables_filter label input').addClass('form-control input-sm');
}

function resetIdProvidersTable(){
	oTable.fnDestroy();
	oTable = $('#idProviderTable').dataTable(oTableSettings);
	$('.dataTables_length label select').addClass('form-control input-sm');
	$('.dataTables_filter label input').addClass('form-control input-sm');
}

function bindIdProvidersEvents(data){

	messageDeleteOpenID = data.labels.messageDeleteOpenID;

	// Vista Avanzada
	
	$('#new').click( function() {
		//$("#dialog-edit" ).dialog( "option", "title", data.labels.btnNew );
		$("#dialog-edit" ).find(".modal-title").html(data.labels.btnNew);
		loadIdProviderData();
	});

	$("#dialog-edit").find(".btn-save").off("click").on("click",function(){

		$("#dialog-edit").find('.form-group').removeClass('has-error');
		if ($("#dialog-edit").find("form").valid()){
			$("#dialog-edit").find("form").find("#id").removeAttr('disabled');
			$("#dialog-edit").find("form").find("#name").removeAttr('disabled');
			var $data = $("#dialog-edit").find("form").serialize();
			$('#dialog-edit').modal('hide');
			setTimeout(function(){
				saveOrUpdateIdProvider($data);
			},100);
		}
	});

	$("#dialog-confirm").find(".btn-confirm").off("click").on("click",function(){
		deleteIdProviderImpl($('#dialog-confirm').attr('selectedId'));
		$('#dialog-confirm').modal('hide');
	});

	// Vista estandar
	
	$('#save').off("click").on("click", function() {
		$("#form3").find('.form-group').removeClass('has-error');
		if ($("#form3").valid()){
			$data = $("#form3").serialize();
			submitData(SERVICES.updateIdProviders, $data);
			scrollToTop();
			resetIdProvidersTable();
		}
	});
	
	$('#cancel').off("click").on("click", function() {
		$("#idProviders").click();
	});

}

function editIdProvider(selectedId){
	$("#dialog-edit").find(".modal-title").html(editLabel);
	loadIdProviderData(selectedId);
}

function deleteIdProvider(selectedId){
	if (selectedId == "openid"){
		showGeneralMessage(messageDeleteOpenID);
		return false;	
	}else{
		$('#dialog-confirm').attr('selectedId', selectedId).modal('show');
	}
}

function deleteIdProviderImpl(selectedId){
	submitData(SERVICES.deleteIdProvider, {id: selectedId}, function(){
		resetIdProvidersTable();
		loadIdProvidersStandarContent();
	});
}

function saveOrUpdateIdProvider($data){
	submitData(SERVICES.saveIdProvider, $data, function(){
		resetIdProvidersTable();
		loadIdProvidersStandarContent();
	});
}

function loadIdProviderData(selectedId){
	var formTemplate = (selectedId == "openid") ? TEMPLATES.openIdProviderForm : TEMPLATES.idProviderForm;
	loadContent(SERVICES.getIdProviderData, {id: selectedId}, formTemplate, $("#dialog-edit").find(".modal-body"), loadIdProviderDataCallback);
}

function loadIdProviderDataCallback(data){

	initPopover();

	bindSelectEvents(data);

	updateSelectValues(data);
	
	
	if( $( "#form" ).find("#method").val() == "new" ){
		$( "#form" ).find("input#id").remove();
		setIdProvidersDefaultValues(data);
	}else{
		$( "#form" ).find("#selectContainer").remove();
		$( "#form" ).find("#add").remove();
	}

	// valor por defecto para apiCallParameters
	if ($("#apiCallParameters").val() == ""){
		$("#apiCallParameters").val("access_token={ACCESS_TOKEN}");
	}

	var idProviderId = data.idProvider.id || $('#selectContainer').find('select#id').val();
	buttonStylePreview(idProviderId, data.idProvider.label, $('#buttonStylePreview').find('.buttonStyle'));

	bindToolsIdProviderEvents(data);

	$( "#form" ).find('#label').off('onkeyup').on('keyup', function(){
		$('#buttonStylePreview').find('.buttonStyle .eslip_button_label').text($(this).val());
	});

	$('#dialog-edit').modal('show');

	initIdProviderFormValidation();
}

function setIdProvidersDefaultValues(data){
	var $form = $( "#form" );
	$("#oauth", $form).val("2.0").change();
	$("#authorizationHeader", $form).val(1).change();
	$("#urlParameters", $form).val(1).change();
	$("#id", $form).prepend('<option value="" selected>'+data.labels.selectOption+'</option>');
}

function updateSelectValues(data){
	var $form = $( "#form" );
	$("#active", $form).val(data.idProvider.active).change();
	$("#oauth", $form).val(data.idProvider.oauth).change();
	$("#authorizationHeader", $form).val(data.idProvider.authorizationHeader).change();
	$("#urlParameters", $form).val(data.idProvider.urlParameters).change();
	$("#hasAccessTokenExtraParameter", $form).val(data.idProvider.hasAccessTokenExtraParameter).change();
	$("#immediate", $form).val(data.idProvider.immediate).change();
	$("#selectContainer", $form).find("select#id").val(data.idProvider.id).change();
}

function bindSelectEvents(data){
	$( "#form" ).find('#oauth').off('change').on('change', function(){
		var version = $(this).val();
		if (version == '1.0' || version == '1.0a'){
			$( "#form" ).find('#requestTokenUrl').parents(".form-group").show();
			$( "#form" ).find('#urlParameters').parents(".form-group").show();
		}else{
			$( "#form" ).find('#requestTokenUrl').parents(".form-group").hide();
			$( "#form" ).find('#urlParameters').parents(".form-group").hide();
		}
	});

	$( "#form" ).find('#hasAccessTokenExtraParameter').off('change').on('change', function(){
		var value = parseInt( $(this).val() );
		if ( value == 0){
			$( "#form" ).find('#accessTokenExtraParameterName').parents(".form-group").hide();
		}else{
			$( "#form" ).find('#accessTokenExtraParameterName').parents(".form-group").show();
		}
	});
}

function bindToolsIdProviderEvents(data){
	
	var isNew = $( "#form" ).find("#method").val() == "new";

	if( isNew ){
		$( "#form" ).find("#add").click( function() {
			$("#dialog-edit-id").find(".modal-title").html(data.labels.btnNew);
			loadIdProviderButtonData();
		} );
	}
	
	$( "#form" ).find('#edit').click( function() {
		var selected = $( "#form" ).find("#id").val();
		if ( selected !== "" ) {
			$("#dialog-edit-id").find(".modal-title").html(data.labels.btnEdit);
			loadIdProviderButtonData(selected);
		}
	} );

	$content.off('idProviderButton.updated').on('idProviderButton.updated', function(event, data){
		var $select = $( "#form" ).find('#selectContainer').find('select#id');
		if ($.exists($select)){
			var $option = $select.find('option[value="'+data.id+'"]');
			if (!$.exists($option)){
				$select.append('<option value="'+data.id+'">'+data.id+'</option>');
			}
			$select.val(data.id).change();
		}else{
			buttonStylePreview(data.id, $( "#form" ).find('#label').val(), $('#buttonStylePreview').find('.buttonStyle'));
		}
	});

	if( isNew ){
		$( "#form" ).find('#selectContainer').find('select#id').off('change').on('change', function(){
			buttonStylePreview($(this).val(), $( "#form" ).find('#label').val(), $('#buttonStylePreview').find('.buttonStyle'));
		});
	}
}

function buttonStylePreview(id, label, element){
	if (id === ''){
		element.html('');
		return false;
	}
	apiGet(SERVICES.getIdProviderButtonData, {id: id}, function(data){
		var button = data.button;
		button.label = label;
		button.logo = button.logo_url;
		button.width = '100%';
    	button.showLabel = true;

		var $a = '<div id="ESLIP_Plugin">' + ESLIP.buttonStyleElement(button) + '</div>';
		$a = $($a);
		element.html($a);

		preventLoginButtonClick();

	});
}

function initIdProviderStandarValidation(){
	$("#form3").validate({
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
}

function initIdProviderFormValidation(){

	$("#form").validate({
		rules: {
			id: "required",
			name: "required",
			active: "required",
			oauth: "required",
			requestTokenUrl: {
				required: {
					depends: function(element) {
						return ($("#oauth").val() == '1.0' || $("#oauth").val() == '1.0a');
					}
				}
		    },
		    dialogUrl: "required",
			accessTokenUrl: "required",
			apiCallParameters: "required",
			authorizationHeader: "required",
			urlParameters: {
				required: {
					depends: function(element) {
						return ($("#oauth", "#form").val() == '1.0' || $("#oauth", "#form").val() == '1.0a');
					}
				}
		    },
			clientId: {
				required: {
					depends: function(element) {
						return (parseInt($("#active", "#form").val()) === 1);
					}
				}
		    },
		    clientSecret: {
				required: {
					depends: function(element) {
						return (parseInt($("#active", "#form").val()) === 1);
					}
				}
		    },
			hasAccessTokenExtraParameter: "required",
			accessTokenExtraParameterName: {
				required: {
					depends: function(element) {
						return (parseInt($("#hasAccessTokenExtraParameter", "#form").val()) === 1);
					}
				}
		    },
			userDataUrl: "required"
		}
	});
}

//**********************************************************
// Admin Id Providers Buttons Functions
//**********************************************************

function loadIdProvidersButtonsContent(){
	loadContent(SERVICES.getIdProvidersButtonsData, {}, TEMPLATES.idProvidersButtons, $content, loadIdProvidersButtonsContentCallback);
}

function loadIdProvidersButtonsContentCallback(data){
	
	initPopover();

	initDataTableIdsButtons(data);

	initIdProvidersButtonsDialogs(data);

	bindIdProvidersButtonsEvents(data);

	preventLoginButtonClick();
}

function initDataTableIdsButtons(data){

	editLabel = data.labels.btnEdit;
	deleteLabel = data.labels.btnDelete;

	oTableIdsButtonsSettings = {
		"iDisplayLength": 10,
		"bRetrieve":true,
		"sAjaxSource": SERVICES_URL+SERVICES.getIdProvidersButtonsDataTable,
		"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
			// Add row id
			$(nRow).attr("id",aData[0]);
		},
		"aoColumnDefs": [{ 
			"sClass": "rowButtonPreview",
			"mRender": function ( data, type, row ) {
            	var button = {
            		id :  row[0],
            		label: row[0],
            		logo: row[1],
            		textColor: row[2],
            		backgroundColor: row[3],
            		width: '100%',
            		showLabel: true
            	};
            	return '<div id="ESLIP_Plugin">' + ESLIP.buttonStyleElement(button) + '</div>';
            },
			"aTargets": [ 1 ]
		},{
			"mRender": function ( data, type, row ) {
            	return '<span class="colorPreview" style="background-color:'+data+';"></span> '+data;
            },
			"aTargets": [ 2, 3 ]			
		},{
			"sClass": "actions",
			"mRender": function ( data, type, row ) {
				return '' +
					'<button type="button" class="btn btn-success edit" onclick="editIdProvidersButton(\''+row[0]+'\')"><span class="glyphicon glyphicon-pencil"></span>&nbsp;'+editLabel+'</button>'+
					'&nbsp;'+
                	'<button type="button" class="btn btn-danger delete" onclick="deleteIdProviderButton(\''+row[0]+'\')"><span class="glyphicon glyphicon-remove"></span>&nbsp;'+deleteLabel+'</button>'
                ;
            },
			"aTargets": [ 4 ]
		}],
		"oLanguage": {
			"sProcessing": data.labels.processing,
			"sLoadingRecords": data.labels.dtsLoadingRecords,
			"sLengthMenu": data.labels.dtsLengthMenu,
			"sZeroRecords": data.labels.dtsZeroRecords,
			"sInfo": data.labels.dtsInfo,
			"sInfoEmpty": data.labels.dtsInfoEmpty,
			"sInfoFiltered": data.labels.dtsInfoFiltered,
			"sSearch": data.labels.dtsSearch,
			"oPaginate": {
				"sFirst": data.labels.dtsFirst,
				"sPrevious": data.labels.dtsPrevious,
				"sNext": data.labels.dtsNext,
				"sLast": data.labels.dtsLast
			}
		}
	};

	oTableIdsButtons = $('#idProviderButtonTable').dataTable(oTableIdsButtonsSettings);

	$('.dataTables_length label select').addClass('form-control input-sm');
	$('.dataTables_filter label input').addClass('form-control input-sm');

	preventLoginButtonClick();
}


function initIdProvidersButtonsDialogs(data){

	$("#dialog-edit-id").find(".btn-save").off("click").on("click",function(){
		$("#dialog-edit-id").find("form").find("#id").removeAttr('disabled');
		
		if ($("#dialog-edit-id").find("form").valid()){
			$("#dialog-edit-id").find("form").submit();
			$('#dialog-edit-id').modal('hide');
		}
	});

	$("#dialog-confirm-id").find(".btn-confirm").off("click").on("click",function(){
		deleteIdProviderButtonImpl($('#dialog-confirm-id').attr('selectedId'));
		$('#dialog-confirm-id').modal('hide');
	});
}

function bindIdProvidersButtonsEvents(data){

	messageDeleteOpenID = data.labels.messageDeleteOpenID;

	$('#new').click( function() {
		$("#dialog-edit-id").find(".modal-title").html(data.labels.btnNew);
		loadIdProviderButtonData();
	});
}

function editIdProvidersButton(selectedId){
	$("#dialog-edit-id").find(".modal-title").html(editLabel);
	loadIdProviderButtonData(selectedId);
}

function deleteIdProviderButton(selectedId){
	if (selectedId == "openid"){
		showGeneralMessage(messageDeleteOpenID);
		return false;	
	}else{
		$('#dialog-confirm-id').attr('selectedId', selectedId).modal('show');
	}
}

function deleteIdProviderButtonImpl(selectedId){
	submitData(SERVICES.deleteIdProviderButton, {id: selectedId}, function(response){
		oTableIdsButtons.fnDestroy();
		oTableIdsButtons = $('#idProviderButtonTable').dataTable(oTableIdsButtonsSettings);
		$('.dataTables_length label select').addClass('form-control input-sm');
		$('.dataTables_filter label input').addClass('form-control input-sm');
	});
}

function loadIdProviderButtonData(selectedId){
	loadContent(SERVICES.getIdProviderButtonData, {id: selectedId}, TEMPLATES.idProviderButtonForm, $("#dialog-edit-id").find(".modal-body"), loadIdProviderButtonDataCallback);
}

function loadIdProviderButtonDataCallback(data){

	initPopover();

	if( $( "#form-id" ).find("#method").val() == "new" ){
		$( "#form-id" ).find("#id").removeAttr('disabled').removeClass("disabled");
		setNewIdProviderButtonActions();
	}else{
		$( "#form-id" ).find("#id").attr('disabled','disabled').addClass("disabled");
	}
	
	initIdProviderButtonFormValidation();

	$( "#form-id" ).attr("action", SERVICES_URL+SERVICES.saveIdProviderButton);
	
	$("#hiddenUpload").load(function(){
		saveIdProviderButtonCallback(this);
	});

	//$('.colorPicker').minicolors({theme: 'default'});

	$('.colorPicker').each( function() {
		$(this).minicolors({
			control: $(this).attr('data-control') || 'hue',
			defaultValue: $(this).attr('data-defaultValue') || '',
			inline: $(this).attr('data-inline') === 'true',
			letterCase: $(this).attr('data-letterCase') || 'uppercase',
			opacity: $(this).attr('data-opacity'),
			position: $(this).attr('data-position') || 'bottom left',
			change: function(hex, opacity) {
				var log;
				try {
					log = hex ? hex : 'transparent';
					if( opacity ) log += ', ' + opacity;
				} catch(e) {}
			},
			theme: 'eslip'
		});
	});
	
	$('#dialog-edit-id').modal('show');
}

function saveIdProviderButtonCallback(hiddenUploadFrame){

	var response = $(hiddenUploadFrame).contents().find("body").html();
	if (response.length) {
		// Convertir a objeto JSON
		var responseObject = eval("("+response+")");
		if (responseObject.status == "ERROR"){
			showGeneralMessage(responseObject.message);
			$(".errorMessage").show();
		} else if ($.exists('#idProviderButtonTable')) {
			oTableIdsButtons.fnDestroy();
			oTableIdsButtons = $('#idProviderButtonTable').dataTable(oTableIdsButtonsSettings);
			$('.dataTables_length label select').addClass('form-control input-sm');
			$('.dataTables_filter label input').addClass('form-control input-sm');
		}
		$content.trigger('idProviderButton.updated', {id: responseObject.id});
	}
}

function setNewIdProviderButtonActions(){
	// prevenir caracterres especiales en el id
	$( "#form-id" ).find('#id').off('onkeyup').on('keyup', function(){
		var str = $(this).val();
		//str = str.replace(/[_\s]/g, '-').replace(/[^a-z0-9-\s]/gi, '');
		str = str.replace(/[_\W]+/g, "-");
		$(this).val(str);
	});
}

function initIdProviderButtonFormValidation(){
	var rules = {
		id: "required"
	};
	
	if( $( "#form-id" ).find("#method").val() == "new" ){
		rules.logo = "required";
	}

	$("#form-id").validate({
		rules: rules
	});
}