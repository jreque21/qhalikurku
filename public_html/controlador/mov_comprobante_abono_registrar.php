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

    $li_cod_comprobante = $bd->bd_escapeCadena($_POST['id_cod_comprobante']);
    $ld_fec_pago        = $bd->bd_escapeCadena($_POST['id_fec_pago']);
    $ln_monto_abono     = $bd->bd_escapeCadena($_POST['id_monto_abono']);
    $li_cod_mediopago   = $bd->bd_escapeCadena($_POST['id_cod_mediopago']);
    $ls_observacion     = $bd->bd_escapeCadena($_POST['id_observacion']);

    $ldt_fecha_insercion = date('Y-m-d H:i:s');

    // ==================================== VALIDACIONES SERVIDOR ==================================== //

    // Validar monto positivo
    if ($lb_result == true && (!is_numeric($ln_monto_abono) || floatval($ln_monto_abono) <= 0)) {
        $ls_mensaje = 'El monto del abono debe ser mayor a cero.';
        $lb_result  = false;
    }

    // Recuperar comprobante y calcular saldo pendiente
    $array_comprobante = null;
    $ln_saldo_pendiente = 0;

    if ($lb_result == true) {

        $array_campo_pk = array('N_COD_COMPROBANTE');
        $array_valor_pk = array($li_cod_comprobante);
        $array_comprobante = $crud->fila_recuperar(DEF_TABLA_COMPROBANTE, $array_campo_pk, $array_valor_pk);

        if ($array_comprobante === null) {
            $ls_mensaje = 'El comprobante indicado no existe.';
            $lb_result  = false;
        } elseif ($array_comprobante['V_ESTADO_COMPROBANTE'] == 'PAG') {
            $ls_mensaje = 'Este comprobante ya está pagado en su totalidad.';
            $lb_result  = false;
        } elseif ($array_comprobante['V_ESTADO_COMPROBANTE'] == 'ANU') {
            $ls_mensaje = 'Este comprobante está anulado.';
            $lb_result  = false;
        }
    }

    // Calcular saldo pendiente real (total - suma de pagos activos)
    if ($lb_result == true) {

        $ls_condicion = "N_COD_COMPROBANTE = '$li_cod_comprobante' AND V_FLAG_ESTADO = '1'";
        $lr_pagado = $crud->fila_listar_solocondicion(
                        "(SELECT SUM(N_MONTO) AS TOTAL_PAGADO FROM " . DEF_TABLA_PAGO . " WHERE $ls_condicion) t",
                        '', '', -1, 0
                     );
        $row_pagado = $lr_pagado ? mysqli_fetch_assoc($lr_pagado) : null;
        $ln_total_pagado = $row_pagado && $row_pagado['TOTAL_PAGADO'] ? floatval($row_pagado['TOTAL_PAGADO']) : 0;

        $ln_saldo_pendiente = round(floatval($array_comprobante['N_MONTO']) - $ln_total_pagado, 2);

        if (floatval($ln_monto_abono) > $ln_saldo_pendiente) {
            $ls_mensaje = 'El abono (' . number_format($ln_monto_abono, 2) . ') no puede ser mayor al saldo pendiente (' . number_format($ln_saldo_pendiente, 2) . ').';
            $lb_result  = false;
        }
    }

    // ================================= FIN VALIDACIONES SERVIDOR =================================== //


    // ====================================== CRUD REGISTRAR ABONO ==================================== //

    if ($lb_result == true) {

        $li_cod_pago = $crud->fila_recuperar_indice(DEF_TABLA_PAGO, 'N_COD_PAGO');
        if (empty($li_cod_pago)) {
            $li_cod_pago = 1;
        }

        $array_campo = array(
            'N_COD_PAGO',
            'N_COD_COMPROBANTE',
            'N_COD_CITA',
            'D_FEC_PAGO',
            'N_COD_MONEDA',
            'N_MONTO',
            'N_COD_MEDIOPAGO',
            'V_OBSERVACION',
            'V_FLAG_ESTADO',
            'V_AUD_USR_REG',
            'D_AUD_FEC_REG'
        );

        $array_valor = array(
            $li_cod_pago,
            $li_cod_comprobante,
            $array_comprobante['N_COD_CITA'],
            $ld_fec_pago,
            $array_comprobante['N_COD_MONEDA'],
            $ln_monto_abono,
            $li_cod_mediopago,
            $ls_observacion,
            '1',
            $_SESSION['usr_conectado'],
            $ldt_fecha_insercion
        );

        $lb_result = $crud->fila_registrar(DEF_TABLA_PAGO, $array_campo, $array_valor, '0');

        if ($lb_result == false) {
            $ls_mensaje = 'No se pudo registrar el abono.';
        }
    }

    // ============================== ACTUALIZAR ESTADO DEL COMPROBANTE SI QUEDÓ SALDADO ================= //

    if ($lb_result == true) {

        $ln_nuevo_saldo = round($ln_saldo_pendiente - floatval($ln_monto_abono), 2);

        if ($ln_nuevo_saldo <= 0) {

            $array_campo_pk = array('N_COD_COMPROBANTE');
            $array_valor_pk = array($li_cod_comprobante);

            $array_campo = array('V_ESTADO_COMPROBANTE', 'V_AUD_USR_MOD', 'D_AUD_FEC_MOD');
            $array_valor = array('PAG', $_SESSION['usr_conectado'], $ldt_fecha_insercion);

            $crud->fila_actualizar(DEF_TABLA_COMPROBANTE, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);
        }
    }

    // ============================================================================================== //

    // Redireccionar
    if ($lb_result == true) {
        header("Location: ../vista/mov_comprobante_lista.php?id_msgRpta=" . urlencode('OK - Abono registrado correctamente.'));
    } else {
        header("Location: ../vista/mov_comprobante_abono.php?id_codigo=" . $li_cod_comprobante . "&id_msgRpta=" . urlencode($ls_mensaje));
    }
    exit;

}

?>
