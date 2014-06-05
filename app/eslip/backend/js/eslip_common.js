//**********************************************************
// Variables Definitions
//**********************************************************

var SERVICES_URL = "backend_services/";
var ESLIP_SERVICES_URL = "../eslip_frontend_services/";
var COMMON_SERVICES = {
    "init": "init"
};

//**********************************************************
// Common Functions
//**********************************************************

init();

function init(params){

    apiPost(COMMON_SERVICES.init, params, function(data){

        $.extend($.validator.messages, data.validatorMessages);

        $.extend($.validator.messages, {
            minlength: $.validator.format(data.validatorMessages.minlength),
            min: $.validator.format(data.validatorMessages.min),
            urlCustom: data.validatorMessages.url
        });

    });
}

function initButtons(){
	$( "input[type=button], input[type=submit], button" ).button();
}

function initSwitchOnOff(){
	
	$(".switch").switchOnOff();
	
	$(".idProviderData[active='0']").hide();

	$(".cb-enable, .cb-disable").bind( "onoff-switch" , {}, function(event, params){
		var $idProvider = $(event.target).parents(".idProvider");
		if (params.value == "on"){
			$idProvider.find(".idProviderData").attr("active", 1).show();
		}else{
			$idProvider.find(".idProviderData").attr("active", 0).hide();
		}
		
	});
}

function initPopover(){
    $('[data-toggle="popover"]').popover({trigger:'hover'});
}

function showGeneralMessage(message){
	$('#dialog-general-message').find(".modal-body").html(message);
	$('#dialog-general-message').modal("show");
}

function loadContent(url, params, template, container, callback){
	apiGet(url, params, function(data){
		$(container).loadTemplate(template, data, {
			overwriteCache: true,
			complete: function(){
				callback(data);
			}
		});
	});
}

function submitData(url, params, callback){
	apiPost(url, params, function(data){
        console.log(data);
        console.log(data.status == "SUCCESS");
		if (data.status == "SUCCESS"){
			if (typeof callback === "function"){
				callback(data);
			}
			$(".successMessage").show().delay(3000).slideUp("slow");;
		}else{
			if (typeof data.message !== "undefined" && data.message !== ''){
				showGeneralMessage(data.message);
			}
			$(".errorMessage").show().delay(3000).slideUp("slow");;
		}
	});
}

function formatUrl(url){
	var hasTrailingSlash = url.charAt(url.length - 1) === "/";
	if (! hasTrailingSlash){
		url += "/";
	}
	return url;
}

function suggestSiteUrl(input){
	var siteUrl = $(input).val();
	if (siteUrl == ""){
		siteUrl = window.location.origin + "/";
		$(input).val(siteUrl);
	}
}

function suggestPluginUrl(input){
	var pluginUrl = $(input).val();
	if ( pluginUrl == ""){
		pluginUrl = window.location.origin + "/eslip-plugin/eslip/";
		$(input).val(pluginUrl);
	}
}

function sortIdProviders(){	
	$(".idProvider").sort(function(a,b){
		return $(a).attr("id") > $(b).attr("id") ? 1 : -1;
	}).appendTo('#idProvidersContainer');

	$.each($(".idProvider").find(".switch[active='1']").get().reverse(), function(i,elem){
		$('#idProvidersContainer').prepend($(elem).parents(".idProvider"));
	});
}

function initLoginWidgetValues(data){
	var width = data.loginWidget.widgetWidth;
	var widthFixed = width;
	var unit = '';
	if (width.indexOf('px') >= 0){
		widthFixed = width.replace('px','');
		unit = 'px';
	}else if (width.indexOf('%') >= 0){
		widthFixed = width.replace('%','');
		unit = '%';
	}

	$("#widgetWidth").val(widthFixed);
	$("#widgetWidthUnit").val(unit).change();
	
	$("input[name='buttonLabel']").removeAttr('checked');
	$("input[name='buttonLabel'][value='"+data.loginWidget.buttonLabel+"']").attr('checked', 'checked').click();

    if (data.loginWidget.widgetRows == 0){
        $('#autoRows').attr('checked', 'checked');
        $("#widgetRows").val("0").attr('oldValue', "1");
        $("#widgetRows").parents(".spinner_wraper").hide();
    }

    $('.spinner .spinnerUp').on('click', function() {
        updateSpinner(this, 1);
    });

    $('.spinner .spinnerDown').on('click', function() {
        updateSpinner(this, -1);
    });

}

function updateSpinner(elem, sum){
    var $input = $(elem).parents('.spinner').find('input');
    $input.val( parseInt($input.val(), 10) + sum).keyup();
}

function bindLoginWidgetPreviewEvents(data){
    
    $("#widgetWidth").off("keyup").on("keyup", function(event) {
        
        var str = $(this).val();

        if (str.slice(-1) == '.' ) {
            return false;
        }

        if ( isPositiveFloat(str) ){
            updateLoginWidgetPreview(data);
        }else{
            $(this).val(str.slice(0,str.length-1));
        }
    });

    $("#widgetWidthUnit").off("change").on("change", function(event) {
        updateLoginWidgetPreview(data);
    });

    $("input[type='radio']").off("change").on("change", function(event) {
        updateLoginWidgetPreview(data);
    });

    $("#widgetRows").off("keyup").on("keyup", function(event) {
        spinnerKeyupHandler(this, event, data);
    });

    $("#widgetColumns").off("keyup").on("keyup", function(event) {
        spinnerKeyupHandler(this, event, data);
    });
    
    $('#autoRows').off("click").on("click", function() {
        var $widgetRowsInput = $("#widgetRows");
        if($(this).is(':checked')){
            $widgetRowsInput.attr('oldValue', $widgetRowsInput.val());
            $widgetRowsInput.val("0");
            $widgetRowsInput.parents(".spinner_wraper").hide();
        }else{
            $widgetRowsInput.val($widgetRowsInput.attr('oldValue'))
            $widgetRowsInput.parents(".spinner_wraper").show();
        }
        //$widgetRowsInput.keyup();
        updateLoginWidgetPreview(data);
    });
}

function spinnerKeyupHandler(elem, event, data){

    var str = $(elem).val();
    if ( isNormalInteger(str) ){
        updateLoginWidgetPreview(data);
    }else{
        $(elem).val(str.slice(0,str.length-1) || 1);
    }

    if (event.keyCode == 38) {
        updateSpinner(elem, 1);   
    }    
    if (event.keyCode == 40) {
        updateSpinner(elem, -1);
    }
}

function updateLoginWidgetPreview(data){
    var $buttons = $("#loginWidgetPreview").find(".button")
    var showLabel = parseInt($('input[name=buttonLabel]:checked').val());
    var width = $("#widgetWidth").val();
    var unit = $("#widgetWidthUnit").val();
    width = width + unit;

    var rows = $("#widgetRows").val();
    var columns = $("#widgetColumns").val();

    data.loginWidget = {
        widgetWidth: width,
        widgetRows: rows,
        widgetColumns: columns,
        buttonLabel: showLabel
    };

    ESLIP.renderWidget(data);

    preventLoginButtonClick();
}

function preventLoginButtonClick(){
    setTimeout(function(){
        $('.eslip_button').attr('onclick','').off('click');
    },100);
}

//**********************************************************
// Helper Functions
//**********************************************************

function apiGet(uri, data, callback){
	$.ajax(SERVICES_URL+uri,{
		data: data,
		dataType: 'JSON',
		type: 'GET',
		cache: false,
		async: true
	}).done(function(data){
		callback(data);
	});
}

function apiPost(uri, data, callback){
	$.ajax(SERVICES_URL+uri,{
		data: data,
		dataType: 'JSON',
		type: 'POST',
		cache: false,
		async: true
	}).done(function(data){
		callback(data);
	}).fail(function(data) {
        callback(data);
    });
}

function getFormData($form){
	var unindexed_array = $form.serializeArray();
	var indexed_array = {};

	$.map(unindexed_array, function(n, i){
		indexed_array[n['name']] = n['value'];
	});

	return indexed_array;
}

$.exists = function(selector) {return ($(selector).length > 0);}

function isNormalInteger(str) {
    return /^\+?([1-9]\d*)$/.test(str);
}

function isPositiveFloat(str) {
    return /^\+?([1-9]\d*)+(\.\d+)?$/.test(str);
}

function scrollToTop(offset){
    offset = offset || 0;
    if ($.exists(".wizard")){
        $('.wizard-card:visible').animate({
            scrollTop: offset
        }, 500);
    }else{
        $('html, body').animate({
            scrollTop: offset
        }, 500);
    }
}

$.validator.setDefaults({
    errorElement: "span",
    errorClass: "help-block",
    highlight: function (element, errorClass, validClass) {
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
    },
    unhighlight: function (element, errorClass, validClass) {
        //$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
        $(element).closest('.form-group').removeClass('has-error');
    },
   /* errorElement: 'span',
    errorClass: 'help-block',*/
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length || element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    },
    //focusInvalid: false,
    invalidHandler: function(event, validator) {

        if (!validator.numberOfInvalids()){
            return;
        }

        // sort by top
        validator.errorList.sort(function(a, b){
          var aTop = $(a.element).offset().top;
          var bTop = $(b.element).offset().top; 
          return ((aTop < bTop) ? -1 : ((aTop > bTop) ? 1 : 0));
        });

        var offsetElement = $(validator.errorList[0].element)[0];
        var offsetNumber = $(offsetElement).offset().top;

        if (offsetNumber < 0){
            $(offsetElement).focus();
        }else{
            //if ($.exists(".wizard"))
            //{
                scrollToTop(offsetNumber-$(offsetElement).parents('form').offset().top);
            //}
            //else
            //{
            //    scrollToTop(offsetNumber-100);    
            //}
        }
    }
});

$.validator.addMethod("urlCustom", function(value, element) {
    var regexp = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    return this.optional(element) || regexp.test(value);
}, "Please specify the correct url");