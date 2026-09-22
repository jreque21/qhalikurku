<?php
@session_start();

// Importar funcionalidades
require_once("../config/global.php");  
require_once(DEF_PATH_ADMIN);
require_once(DEF_PATH_HTML_RPT_EXTXSEDE);

// Controlar sesión activa
if(!isset($_SESSION['logged_in']) && !$_SESSION['logged_in']){
	header('Location: '+ DEF_URL_LOGIN);
}

// Instanciar clase de la B.D
$bd = new baseDatos();
$crud = new crud();

// Definir estructura
$array_campo_pk	= array('V_COD_TABLA');
$array_valor_pk	= array(DEF_TABLA_RPT_ESTXSEDE);

// Recuperar registro
$array_opcion = $crud->fila_recuperar(DEF_TABLA_MENU_OPC, $array_campo_pk, $array_valor_pk);

// Validar ingreso de datos
if(isset($_POST) && !empty($_POST)){
	// Almacenar contenido con escape
	$li_cod_sede		= $bd->bd_escapeCadena($_POST['id_cod_sede']);
	$li_cod_cinturon	= $bd->bd_escapeCadena($_POST['id_cod_cinturon']);
}else{
	$li_cod_sede = '%';
	$li_cod_cinturon = '%';
}

// Invocar Contenido
f_admin_carga();
f_admin_cabecera();
f_admin_cuerpo_izda($array_opcion['N_COD_NIVEL'], $array_opcion['N_ORDEN']);
f_listado($array_opcion['V_ETIQUETA'], $array_opcion['V_ICONO'], $li_cod_sede, $li_cod_cinturon);
f_admin_pie();
f_admin_script('1', 'asc');

?>