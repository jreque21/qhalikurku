<?php
@session_start();

// Importar funcionalidades
require_once("../config/global.php");
require_once(DEF_PATH_ADMIN);
require_once(DEF_PATH_HTML_PACIENTE);

// Controlar sesi¨®n activa
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ' . DEF_URL_LOGIN);
    exit;
}

// Instanciar clase
$crud = new crud();

// Definir estructura
$array_campo_pk = array('V_COD_TABLA');
$array_valor_pk = array(DEF_TABLA_PACIENTE);

// Recuperar configuraci¨®n de men¨²
$array_opcion = $crud->fila_recuperar(
                    DEF_TABLA_MENU_OPC,
                    $array_campo_pk,
                    $array_valor_pk
                );

// Invocar contenido
f_admin_carga();
f_admin_cabecera();

f_admin_cuerpo_izda(
    $array_opcion['N_COD_NIVEL'],
    $array_opcion['N_ORDEN']
);

// Formulario Nuevo Paciente
f_formulario(
    $array_opcion['V_ETIQUETA'],
    $array_opcion['V_ICONO'],
    '',
    NULL
);

f_admin_pie();
f_admin_script('1', 'asc');

?>