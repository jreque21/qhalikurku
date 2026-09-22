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

// Inicializar variable
$lb_result  = true;
$ls_mensaje = '';

// Validar ingreso de datos
if (isset($_POST) && !empty($_POST)) {

    // ======================================= CAPTURA DE DATOS ======================================= //

    $li_cod_pago      = $bd->bd_escapeCadena($_POST['id_cod_pago_hide']);
    $li_cod_comprobante = $bd->bd_escapeCadena($_POST['id_cod_comprobante_hide']);
    $ld_fec_pago      = $bd->bd_escapeCadena($_POST['id_fec_pago']);
    $li_cod_moneda    = $bd->bd_escapeCadena($_POST['id_cod_moneda']);
    $ln_monto         = $bd->bd_escapeCadena($_POST['id_monto']);
    $li_cod_mediopago = $bd->bd_escapeCadena($_POST['id_cod_mediopago']);
    $ls_comprobante   = $bd->bd_escapeCadena($_POST['id_comprobante']);
    $ls_observacion   = $bd->bd_escapeCadena($_POST['id_observacion']);

    $ldt_fecha_actualizacion = date('Y-m-d H:i:s');

    // ==================================== VALIDACIONES SERVIDOR ==================================== //

    if ($lb_result == true && (!is_numeric($ln_monto) || floatval($ln_monto) <= 0)) {
        $ls_mensaje = 'El monto debe ser un valor mayor a cero.';
        $lb_result  = false;
    }

    // Validar que el comprobante no esté en estado final ni tenga cuotas ya pagadas
    if ($lb_result == true) {

        $array_campo_pk = array('N_COD_COMPROBANTE');
        $array_valor_pk = array($li_cod_comprobante);
        $array_comprobante = $crud->fila_recuperar(DEF_TABLA_COMPROBANTE, $array_campo_pk, $array_valor_pk);

        $array_campo_pk = array('N_COD_COMPROBANTE', 'V_FLAG_ESTADO');
        $array_valor_pk = array($li_cod_comprobante, '1');
        $li_cant_pagos = $crud->fila_contar(DEF_TABLA_PAGO, $array_campo_pk, $array_valor_pk);

        if ($array_comprobante === null) {
            $ls_mensaje = 'El comprobante asociado no existe.';
            $lb_result  = false;
        } elseif ($array_comprobante['V_ESTADO_COMPROBANTE'] == 'PAG') {
            $ls_mensaje = 'Este comprobante ya está pagado en su totalidad y no puede modificarse.';
            $lb_result  = false;
        } elseif (intval($li_cant_pagos) > 1) {
            $ls_mensaje = 'Este pago a crédito ya tiene cuotas/abonos registrados y no puede modificarse.';
            $lb_result  = false;
        }
    }

    // ================================= FIN VALIDACIONES SERVIDOR =================================== //


    // ======================================= ACTUALIZAR PAGO =========================================== //

    if ($lb_result == true) {

        $array_campo_pk = array('N_COD_PAGO');
        $array_valor_pk = array($li_cod_pago);

        $array_campo = array(
            'D_FEC_PAGO',
            'N_COD_MONEDA',
            'N_MONTO',
            'N_COD_MEDIOPAGO',
            'V_COMPROBANTE',
            'V_OBSERVACION',
            'V_AUD_USR_MOD',
            'D_AUD_FEC_MOD'
        );

        $array_valor = array(
            $ld_fec_pago,
            $li_cod_moneda,
            $ln_monto,
            $li_cod_mediopago,
            $ls_comprobante,
            $ls_observacion,
            $_SESSION['usr_conectado'],
            $ldt_fecha_actualizacion
        );

        $lb_result = $crud->fila_actualizar(DEF_TABLA_PAGO, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);

        if ($lb_result == false) {
            $ls_mensaje = 'No se pudo actualizar el pago.';
        }
    }

    // ==================================== ACTUALIZAR COMPROBANTE ASOCIADO ============================== //

    if ($lb_result == true) {

        $array_campo_pk = array('N_COD_COMPROBANTE');
        $array_valor_pk = array($li_cod_comprobante);

        $array_campo = array(
            'D_FEC_EMISION',
            'N_COD_MONEDA',
            'N_MONTO',
            'N_COD_MEDIOPAGO',
            'V_COMPROBANTE',
            'V_OBSERVACION',
            'V_AUD_USR_MOD',
            'D_AUD_FEC_MOD'
        );

        $array_valor = array(
            $ld_fec_pago,
            $li_cod_moneda,
            $ln_monto,
            $li_cod_mediopago,
            $ls_comprobante,
            $ls_observacion,
            $_SESSION['usr_conectado'],
            $ldt_fecha_actualizacion
        );

        $lb_result = $crud->fila_actualizar(DEF_TABLA_COMPROBANTE, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);

        if ($lb_result == false) {
            $ls_mensaje = 'El pago se actualizó, pero no se pudo actualizar el comprobante asociado.';
        }
    }

    // ===================================== FIN ACTUALIZAR ============================================= //

    // Redireccionar
    if ($lb_result == true) {
        header("Location: ../vista/mov_pago_lista.php?id_msgRpta=" . urlencode('OK - Pago actualizado correctamente.'));
    } else {
        header("Location: ../vista/mov_pago_editar.php?id_codigo=" . $li_cod_pago . "&id_msgRpta=" . urlencode($ls_mensaje));
    }
    exit;

}

?>
