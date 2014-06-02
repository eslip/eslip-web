(function ( $ ) {

	$.fn.switchOnOff = function() {
		var $elem = $(this);

		$elem.find(".cb-enable").click(function(){
			var parent = $(this).parents('.switch');
			$('.cb-disable',parent).removeClass('selected');
			$(this).addClass('selected');
			$('input[type="hidden"]',parent).val("1");
			$(this).trigger("onoff-switch",{value : "on"});
		});
		
		$elem.find(".cb-disable").click(function(){
			var parent = $(this).parents('.switch');
			$('.cb-enable',parent).removeClass('selected');
			$(this).addClass('selected');
			$('input[type="hidden"]',parent).val("0");
			$(this).trigger("onoff-switch",{value : "off"});
		});

		$.each($elem, function(i,elem){
			if( $(elem).attr("active") == "1"){
				$(elem).find(".cb-enable").addClass('selected');
			}else{
				$(elem).find(".cb-disable").addClass('selected');
			}
		});

		return this;
	};
	
}( jQuery ));
