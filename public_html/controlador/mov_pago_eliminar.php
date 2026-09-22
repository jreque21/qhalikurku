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

// Recuperar el comprobante asociado antes de borrar
$array_campo_pk = array('N_COD_PAGO');
$array_valor_pk = array($li_codigo);
$li_cod_comprobante = $crud->fila_recuperar_campo(DEF_TABLA_PAGO, $array_campo_pk, $array_valor_pk, 'N_COD_COMPROBANTE');

// ======================================== PRE ELIMINAR ============================================= //

$lb_result  = true;
$ls_mensaje = '';

if (!empty($li_cod_comprobante)) {

    $array_campo_pk = array('N_COD_COMPROBANTE');
    $array_valor_pk = array($li_cod_comprobante);
    $array_comprobante = $crud->fila_recuperar(DEF_TABLA_COMPROBANTE, $array_campo_pk, $array_valor_pk);

    $array_campo_pk = array('N_COD_COMPROBANTE', 'V_FLAG_ESTADO');
    $array_valor_pk = array($li_cod_comprobante, '1');
    $li_cant_pagos = $crud->fila_contar(DEF_TABLA_PAGO, $array_campo_pk, $array_valor_pk);

    if ($array_comprobante && $array_comprobante['V_ESTADO_COMPROBANTE'] == 'PAG') {
        $ls_mensaje = 'Este comprobante ya está pagado en su totalidad y no puede eliminarse.';
        $lb_result  = false;
    } elseif (intval($li_cant_pagos) > 1) {
        $ls_mensaje = 'Este pago a crédito ya tiene cuotas/abonos registrados y no puede eliminarse.';
        $lb_result  = false;
    }
}

if ($lb_result == false) {
    if ($lb_form == true) {
        header("Location: ../vista/mov_pago_editar.php?id_codigo=" . $li_codigo . "&id_msgRpta=" . urlencode($ls_mensaje));
    } else {
        header("Location: ../vista/mov_pago_lista.php?id_msgRpta=" . urlencode($ls_mensaje));
    }
    exit;
}

// ======================================= FIN PRE ELIMINAR ========================================== //

// ======================================== CRUD ELIMINAR =========================================== //

$array_campo_pk = array('N_COD_PAGO');
$array_valor_pk = array($li_codigo);
$lb_result = $crud->fila_eliminar(DEF_TABLA_PAGO, $array_campo_pk, $array_valor_pk);

if ($lb_result == true && !empty($li_cod_comprobante)) {
    $array_campo_pk = array('N_COD_COMPROBANTE');
    $array_valor_pk = array($li_cod_comprobante);
    $crud->fila_eliminar(DEF_TABLA_COMPROBANTE, $array_campo_pk, $array_valor_pk);
}

// ======================================= FIN CRUD ELIMINAR ======================================== //

// Redireccionar
if ($lb_result == true) {
    header("Location: ../vista/mov_pago_lista.php?id_msgRpta=" . urlencode('OK - Pago eliminado correctamente.'));
} else {
    header("Location: ../vista/mov_pago_lista.php?id_msgRpta=" . urlencode('No se pudo eliminar el pago.'));
}
exit;

?>
