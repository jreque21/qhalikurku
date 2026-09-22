<?php
@session_start();

// Importar funcionalidades
require_once("../config/global.php");
require_once(DEF_PATH_ADMIN);
require_once(DEF_PATH_HTML_RPT_INGRESOS);

// Controlar sesion activa
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ' . DEF_URL_LOGIN);
    exit;
}

// Instanciar clase
$crud = new crud();

// Definir estructura
$array_campo_pk	= array('V_COD_TABLA');
$array_valor_pk	= array(DEF_TABLA_RPT_INGRESOS);

// Recuperar registro
$array_opcion = $crud->fila_recuperar(DEF_TABLA_MENU_OPC, $array_campo_pk, $array_valor_pk);

// Capturar filtros (por defecto: mes actual)
$ls_fec_ini = isset($_GET['id_fec_ini']) && !empty($_GET['id_fec_ini']) ? $_GET['id_fec_ini'] : date('Y-m-01');
$ls_fec_fin = isset($_GET['id_fec_fin']) && !empty($_GET['id_fec_fin']) ? $_GET['id_fec_fin'] : date('Y-m-d');

// Invocar Contenido
f_admin_carga();
f_admin_cabecera();
f_admin_cuerpo_izda($array_opcion['N_COD_NIVEL'], $array_opcion['N_ORDEN']);
f_reporte($array_opcion['V_ETIQUETA'], $array_opcion['V_ICONO'], $ls_fec_ini, $ls_fec_fin);
f_admin_pie();
f_admin_script('', '');

?>
