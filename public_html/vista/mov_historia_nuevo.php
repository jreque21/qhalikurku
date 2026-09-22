<?php
@session_start();

// Importar funcionalidades
require_once("../config/global.php");
require_once(DEF_PATH_ADMIN);
require_once(DEF_PATH_HTML_HISTORIA);

// Controlar sesion activa
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ' . DEF_URL_LOGIN);
    exit;
}

// Instanciar clase
$crud = new crud();

// Definir estructura
$array_campo_pk	= array('V_COD_TABLA');
$array_valor_pk	= array(DEF_TABLA_HISTORIA_CLINICA);

// Recuperar registro
$array_opcion = $crud->fila_recuperar(DEF_TABLA_MENU_OPC, $array_campo_pk, $array_valor_pk);

// El paciente es obligatorio
if (!isset($_GET['id_cod_paciente']) || empty($_GET['id_cod_paciente'])) {
	header('Location: mae_paciente_lista.php');
	exit;
}
$li_cod_paciente = $_GET['id_cod_paciente'];

// Invoca Contenido
f_admin_carga();
f_admin_cabecera();
f_admin_cuerpo_izda($array_opcion['N_COD_NIVEL'], $array_opcion['N_ORDEN']);
f_formulario($array_opcion['V_ETIQUETA'], $array_opcion['V_ICONO'], '', NULL, $li_cod_paciente);
f_admin_pie();
f_admin_script('', '');

?>
