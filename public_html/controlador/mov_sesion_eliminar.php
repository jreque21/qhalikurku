<?php
@session_start();

// Importar funcionalidades
require_once("../config/global.php");
require_once(DEF_PATH_ADMIN);

// Controlar sesion activa
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ' . DEF_URL_LOGIN);
    exit;
}

// Instanciar clase de la B.D
$bd   = new baseDatos();
$crud = new crud();

// Según caso
if (isset($_GET['id_codigo'])) {
    $li_codigo = $bd->bd_escapeCadena($_GET['id_codigo']);
    $lb_form   = false;
} else {
    $li_codigo = $bd->bd_escapeCadena($_POST['id_codigo']);
    $lb_form   = true;
}

// ======================================== PRE ELIMINAR ============================================= //

$lb_result  = true;
$ls_mensaje = '';

$array_campo_pk = array('N_COD_SESION');
$array_valor_pk = array($li_codigo);
$array_sesion = $crud->fila_recuperar(DEF_TABLA_SESION, $array_campo_pk, $array_valor_pk);

if ($array_sesion === null) {
    $ls_mensaje = 'La sesión indicada no existe.';
    $lb_result  = false;
}

// Solo se puede eliminar la última sesión registrada del tratamiento (evita huecos en la numeración)
if ($lb_result == true) {

    $array_campo_pk = array('N_COD_TRATAMIENTO', 'V_FLAG_ESTADO');
    $array_valor_pk = array($array_sesion['N_COD_TRATAMIENTO'], '1');

    $lr_ultima = $crud->fila_listar(DEF_TABLA_SESION, $array_campo_pk, $array_valor_pk, 'N_NUM_SESION', 'D', 0, 1);
    $array_ultima = $lr_ultima ? mysqli_fetch_assoc($lr_ultima) : null;

    if ($array_ultima === null || $array_ultima['N_COD_SESION'] != $array_sesion['N_COD_SESION']) {
        $ls_mensaje = 'Solo se puede eliminar la última sesión registrada del tratamiento.';
        $lb_result  = false;
    }
}

// Controlar error
if ($lb_result == false) {

    if ($lb_form == true) {
        header("Location: ../vista/mov_sesion_editar.php?id_codigo=" . $li_codigo . "&id_msgRpta=" . urlencode($ls_mensaje));
    } else {
        header("Location: ../vista/mov_sesion_lista.php?id_cod_tratamiento_filtro=" . (isset($array_sesion['N_COD_TRATAMIENTO']) ? $array_sesion['N_COD_TRATAMIENTO'] : '') . "&id_msgRpta=" . urlencode($ls_mensaje));
    }
    exit;
}

// ======================================= FIN PRE ELIMINAR ========================================== //


// ======================================== CRUD ELIMINAR =========================================== //

$li_cod_tratamiento = $array_sesion['N_COD_TRATAMIENTO'];

$array_campo_pk = array('N_COD_SESION');
$array_valor_pk = array($li_codigo);

$lb_result = $crud->fila_eliminar(DEF_TABLA_SESION, $array_campo_pk, $array_valor_pk);

// ======================================= FIN CRUD ELIMINAR ======================================== //

// Redireccionar
if ($lb_result == true) {
    header("Location: ../vista/mov_sesion_lista.php?id_cod_tratamiento_filtro=" . $li_cod_tratamiento . "&id_msgRpta=" . urlencode('OK - Sesión eliminada correctamente.'));
} else {
    header("Location: ../vista/mov_sesion_lista.php?id_cod_tratamiento_filtro=" . $li_cod_tratamiento . "&id_msgRpta=" . urlencode('No se pudo eliminar la sesión.'));
}
exit;

?>
