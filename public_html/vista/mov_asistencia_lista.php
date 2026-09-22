<?php
@session_start();

// Importar funcionalidades
require_once("../config/global.php");  
require_once(DEF_PATH_ADMIN);
require_once(DEF_PATH_HTML_ASISTENCIA);

// Controlar sesión activa
if(!isset($_SESSION['logged_in']) && !$_SESSION['logged_in']){
	header('Location: '+ DEF_URL_LOGIN);
}

// Instanciar clase
$crud = new crud();

// Definir estructura
$array_campo_pk	= array('V_COD_TABLA');
$array_valor_pk	= array(DEF_TABLA_ASISTENCIA);

// Recuperar registro
$array_opcion = $crud->fila_recuperar(DEF_TABLA_MENU_OPC, $array_campo_pk, $array_valor_pk);

// Almacena ID
$id_codigo_padre = $_GET["id_codigo_padre"];
$id_codigo 		 = $_GET["id_codigo"];

// Capturar Variable GET Msg Rpta
if (isset($_GET['id_msgRpta'])) {
	$ls_msgRpta = $_GET["id_msgRpta"];
}else{
	$ls_msgRpta = '';
}

// Capturar User
$ls_user = $_SESSION['usr_conectado'];

// Capturar Instructor
$li_cod_instructor = '1'; // FALTA

// Invocar SP para poblar tabla detalle
$lb_result = $crud->ejecutar_sp("call sp_generar_asistenciaxclase('$id_codigo_padre', '$id_codigo', '$li_cod_instructor', '$ls_user')");

// Invocar Contenido
f_admin_carga();
f_admin_cabecera();
f_admin_cuerpo_izda($array_opcion['N_COD_NIVEL'], $array_opcion['N_ORDEN']);
f_listado_asistencia($array_opcion['V_ETIQUETA'], $array_opcion['V_ICONO'], $ls_msgRpta, $id_codigo_padre, $id_codigo);
f_admin_pie();
f_admin_script('1', 'asc');

?>