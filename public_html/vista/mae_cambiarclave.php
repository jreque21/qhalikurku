<?php
@session_start();

// Importar funcionalidades
require_once("../config/global.php");  
require_once(DEF_PATH_ADMIN);

// Controlar sesión activa
if(!isset($_SESSION['logged_in']) && !$_SESSION['logged_in']){
	header('Location: '+ DEF_URL_LOGIN);
}

// Capturar Variable GET Msg Rpta
if (isset($_GET['id_msgRpta'])) {
	$ls_msgRpta = $_GET["id_msgRpta"];
}else{
	$ls_msgRpta = '';
}

// Carga
f_admin_carga();
f_admin_cabecera();
f_admin_cuerpo_izda(1,0);
f_form_cambiarclave($ls_msgRpta);
f_admin_pie();
f_admin_script();

?>