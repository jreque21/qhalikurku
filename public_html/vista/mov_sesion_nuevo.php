<?php
@session_start();

// Importar funcionalidades
require_once("../config/global.php");
require_once(DEF_PATH_ADMIN);
require_once(DEF_PATH_HTML_SESION);

// Controlar sesion activa
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ' . DEF_URL_LOGIN);
    exit;
}

// Instanciar clase
$crud = new crud();

// Definir estructura
$array_campo_pk	= array('V_COD_TABLA');
$array_valor_pk	= array(DEF_TABLA_SESION);

// Recuperar registro
$array_opcion = $crud->fila_recuperar(DEF_TABLA_MENU_OPC, $array_campo_pk, $array_valor_pk);

// El tratamiento es obligatorio para registrar una sesión
if (!isset($_GET['id_cod_tratamiento']) || empty($_GET['id_cod_tratamiento'])) {
	header('Location: mov_tratamiento_lista.php');
	exit;
}
$li_cod_tratamiento = $_GET['id_cod_tratamiento'];

// Invoca Contenido
f_admin_carga();
f_admin_cabecera();
f_admin_cuerpo_izda($array_opcion['N_COD_NIVEL'], $array_opcion['N_ORDEN']);
f_formulario($array_opcion['V_ETIQUETA'], $array_opcion['V_ICONO'], '', NULL, $li_cod_tratamiento);
f_admin_pie();
f_admin_script('', '');

?>
