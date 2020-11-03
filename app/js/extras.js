function processData(data){
  var json = $.parseJSON(data);
  var respuesta = JSON.stringify(json);
  json = $.parseJSON(respuesta);
  return json;
}

/*function new_ajax(url,type,action,is_form,params,button){

	var responseArray = null;

	if (is_form) {
		var form = params;
		form.append('<input type="hidden" name="action" value="'+action+'" id="form_action">');
		params = params.serialize();
	}

	if (button != null) {
		button.addClass("disabled-btn");
	}
	
	$.ajax({
	url: url,
	type: type,
	data: params,

	success: function(data) {
	    response = processData(data);
	    clicked_btn.removeClass("disabled-btn");
	    responseArray = response;
	},
	error: function(jqXHR, status, error) { 
	    console.log(jqXHR); 
	    console.log(status); 
	    console.log(error);
	} 
	});

	if (is_form) {
		$("#form_action").remove();
	}	
	return responseArray;
}*/