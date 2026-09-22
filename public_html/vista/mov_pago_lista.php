<?php
@session_start();

// Importar funcionalidades
require_once("../config/global.php");
require_once(DEF_PATH_ADMIN);
require_once(DEF_PATH_HTML_PAGO);

// Controlar sesion activa
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ' . DEF_URL_LOGIN);
    exit;
}

// Instanciar clase
$crud = new crud();

// Definir estructura
$array_campo_pk	= array('V_COD_TABLA');
$array_valor_pk	= array(DEF_TABLA_PAGO);

// Recuperar registro
$array_opcion = $crud->fila_recuperar(DEF_TABLA_MENU_OPC, $array_campo_pk, $array_valor_pk);

// Capturar Variable GET Msg Rpta
if (isset($_GET['id_msgRpta'])) {
	$ls_msgRpta = $_GET["id_msgRpta"];
}else{
	$ls_msgRpta = '';
}

// Capturar filtro de Fecha Inicio (por defecto: hoy)
if (isset($_GET['id_fec_ini_filtro'])) {
	$ls_fec_ini_filtro = $_GET["id_fec_ini_filtro"];
}else{
	$ls_fec_ini_filtro = date('Y-m-d');
}

// Capturar filtro de Fecha Fin (por defecto: fin del mes actual)
if (isset($_GET['id_fec_fin_filtro'])) {
	$ls_fec_fin_filtro = $_GET["id_fec_fin_filtro"];
}else{
	$ls_fec_fin_filtro = date('Y-m-t');
}

// Capturar filtro de Paciente (por defecto: todos)
$ls_cod_paciente_filtro = isset($_GET['id_cod_paciente_filtro']) ? $_GET["id_cod_paciente_filtro"] : '';

// Capturar filtro de Forma de Pago (por defecto: todas)
$ls_cod_formapago_filtro = isset($_GET['id_cod_formapago_filtro']) ? $_GET["id_cod_formapago_filtro"] : '';

// Invocar Contenido
f_admin_carga();
f_admin_cabecera();
f_admin_cuerpo_izda($array_opcion['N_COD_NIVEL'], $array_opcion['N_ORDEN']);
f_listado($array_opcion['V_ETIQUETA'], $array_opcion['V_ICONO'], $ls_msgRpta, $ls_fec_ini_filtro, $ls_fec_fin_filtro, $ls_cod_paciente_filtro, $ls_cod_formapago_filtro);
f_admin_pie();
f_admin_script('', '');

?>
