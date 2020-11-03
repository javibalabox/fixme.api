//<><><><><><><><><><><><><><><><><><><> Registro scripts <><><><><><><><><><><><><><><><><><><><><><
function registrar(){
var clicked_btn =  $("#signup_form .send-btn");
  if ($("#signup_form").valid()) {
    $("#signup_form").append('<input type="hidden" name="action" value="signup" id="form_action">');
    clicked_btn.addClass("disabled-btn");
      $.ajax({
        url: "app/mods/mods",
        type: 'POST',
        data: $("#signup_form").serialize(),
        
        success: function(data) {
            response = processData(data);                
            if (response[0].status == "true") {                  
              window.location = response[2].url;
            } else {                  
              $("#response-text").empty();                 
              $("#response-text").append('<br>'+response[0].message);
              clicked_btn.removeClass("disabled-btn"); 
            }
        },
        error: function(jqXHR, status, error) { 
            console.log(jqXHR); 
            console.log(status); 
            console.log(error); 
            alert("Hubo un error");
        } 
      });

      $("#form_action").remove();
  }   
}

//<><><><><><><><><><><><><><><><><><><> Login scripts <><><><><><><><><><><><><><><><><><><><><><
verify_login();

function verify_login(){
  $.ajax({
    type: "POST",
    url: "app/mods/mods",
    data: {action: 'verify_login_js'},
    success: function(data){
      response = processData(data);                
      console.log(response[0].message);
    },
    error: function(jqXHR, status, error) { 
        console.log(jqXHR); 
        console.log(status); 
        console.log(error); 
        alert("Hubo un error");
    } 
  });
}

function logout(){
  $.ajax({
    type: "POST",
    url: "app/mods/mods",
    data: {action: 'logout'},        
    success: function(data){
      response = processData(data);                
      if (response[0].status == "true") {
        console.log(response[0].message);       
        window.location = response[1].url;
      } else {
        console.log("No se pudo finalizar la sesión");       
        location.reload();
      }
    },
    error: function(jqXHR, status, error) { 
        console.log(jqXHR); 
        console.log(status); 
        console.log(error); 
        alert("Hubo un error");
    } 
  });
}
 
function trylogin(){
var clicked_btn =  $("#login_form .send-btn");
$("#login_form").append('<input type="hidden" name="action" value="login" id="form_action">');
  if ($("#login_form").valid()) {
    clicked_btn.addClass("disabled-btn");
      $.ajax({
        url: "app/mods/mods",
        type: 'POST',
        data: $("#login_form").serialize(),
        
        success: function(data) {
            response = processData(data);                                
            if (response[0].status == "true") {
              clicked_btn.removeClass("disabled-btn");
              //window.location = response[2].link;
              location.reload();
            } else {                 
              clicked_btn.removeClass("disabled-btn"); 
              $("#response-text").empty();                 
              $("#response-text").append(response[0].message);
            }
        },
        error: function(jqXHR, status, error) { 
            console.log(jqXHR); 
            console.log(status); 
            console.log(error); 
            alert("Hubo un error");
        } 
      });
  } else {
    $("#response-text").empty();                 
    $("#response-text").append("Error");
  }
}