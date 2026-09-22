$(document).on("ready",inicio);

function inicio(){
    // Oculta Span de Ayuda de Campos
    $("span.help-block").hide();
    // Sensitivo
    $("#usuario").keyup(validar);
    $("#nombre").keyup(validar);
	$("#clave").keyup(validar);
}

function validar(){
	
    // Usuario
    var valorUsuario = document.getElementById("usuario").value;
    if(valorUsuario == null || valorUsuario.length == 0){
        $("#icoUsuario").remove();
        $("#usuario").parent().attr("class","form-group has-error has-feedback");
        $("#usuario").parent().children("span").text("Por favor, indicar usuario").show();
        $("#usuario").parent().append("<span id='icoUsuario' class='glyphicon glyphicon-remove form-control-feedback'></span>");
		boton.disabled = true;
        form_mtto.btn_agregar.disabled = true;
        form_mtto.btn_actualizar.disabled = true;
        return false;
    }
    else if(valorUsuario.length < 6){
        $("#icoUsuario").remove();
        $("#usuario").parent().attr("class","form-group has-error has-feedback");
        $("#usuario").parent().children("span").text("Por favor, como minimo 6 caracteres.").show();
        $("#usuario").parent().append("<span id='icoUsuario' class='glyphicon glyphicon-remove form-control-feedback'></span>");
        form_mtto.btn_agregar.disabled = true;
        form_mtto.btn_actualizar.disabled = true;
        return false;
    }
    else{
        $("#icoUsuario").remove();
        $("#usuario").parent().attr("class","form-group has-success has-feedback");
        $("#usuario").parent().children("span").text("").hide();
        $("#usuario").parent().append("<span id='icoUsuario' class='glyphicon glyphicon-ok form-control-feedback'></span>");
    }

    // Nombres
    var valorNombre = document.getElementById("nombre").value;
    if(valorNombre == null || valorNombre.length == 0){
        $("#icoNombre").remove();
        $("#nombre").parent().attr("class","form-group has-error has-feedback");
        $("#nombre").parent().children("span").text("Por favor, indicar nombres").show();
        $("#nombre").parent().append("<span id='icoNombre' class='glyphicon glyphicon-remove form-control-feedback'></span>");
        form_mtto.btn_agregar.disabled = true;
        form_mtto.btn_actualizar.disabled = true;
        return false;
    }
    else{
        $("#icoNombre").remove();
        $("#nombre").parent().attr("class","form-group has-success has-feedback");
        $("#nombre").parent().children("span").text("").hide();
        $("#nombre").parent().append("<span id='icoNombre' class='glyphicon glyphicon-ok form-control-feedback'></span>");
    }

    // Contraseña
    var valorClave = document.getElementById("clave").value;
    if(valorClave == null || valorClave.length == 0){
        $("#icoClave").remove();
        $("#clave").parent().attr("class","form-group has-error has-feedback");
        $("#clave").parent().children("span").text("Por favor, indicar clave").show();
        $("#clave").parent().append("<span id='icoClave' class='glyphicon glyphicon-remove form-control-feedback'></span>");
        form_mtto.btn_agregar.disabled = true;
        form_mtto.btn_actualizar.disabled = true;
        return false;
    }
    else if(valorClave.length < 8){
        $("#icoClave").remove();
        $("#clave").parent().attr("class","form-group has-error has-feedback");
        $("#clave").parent().children("span").text("Por favor, como minimo 8 caracteres.").show();
        $("#clave").parent().append("<span id='icoClave' class='glyphicon glyphicon-remove form-control-feedback'></span>");
        form_mtto.btn_agregar.disabled = true;
        form_mtto.btn_actualizar.disabled = true;
        return false;
    }
    else{
        $("#icoClave").remove();
        $("#clave").parent().attr("class","form-group has-success has-feedback");
        $("#clave").parent().children("span").text("").hide();
        $("#clave").parent().append("<span id='icoClave' class='glyphicon glyphicon-ok form-control-feedback'></span>");
    }
    form_mtto.btn_agregar.disabled = false;
    form_mtto.btn_actualizar.disabled = false;
	
    return true;

}