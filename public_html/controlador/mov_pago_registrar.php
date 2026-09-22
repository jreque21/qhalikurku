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

    $li_cod_cita      = $bd->bd_escapeCadena($_POST['id_cod_cita']);
    $ld_fec_pago      = $bd->bd_escapeCadena($_POST['id_fec_pago']);
    $li_cod_moneda    = $bd->bd_escapeCadena($_POST['id_cod_moneda']);
    $ln_monto_total   = $bd->bd_escapeCadena($_POST['id_monto']);
    $ls_comprobante   = $bd->bd_escapeCadena($_POST['id_comprobante']);
    $ls_observacion   = $bd->bd_escapeCadena($_POST['id_observacion']);
    $li_cod_formapago = $bd->bd_escapeCadena($_POST['id_cod_formapago']); // 1=Contado, 2=Crédito

    // El medio de pago solo es obligatorio al Contado. Al Crédito se define
    // en cada abono (puede variar de una cuota a otra), así que aquí es opcional.
    $li_cod_mediopago = !empty($_POST['id_cod_mediopago']) ? $bd->bd_escapeCadena($_POST['id_cod_mediopago']) : null;

    $ldt_fecha_insercion = date('Y-m-d H:i:s');

    // ==================================== VALIDACIONES SERVIDOR ==================================== //

    // Validar monto total positivo
    if ($lb_result == true && (!is_numeric($ln_monto_total) || floatval($ln_monto_total) <= 0)) {
        $ls_mensaje = 'El monto debe ser un valor mayor a cero.';
        $lb_result  = false;
    }

    // Medio de pago obligatorio SOLO si es al Contado
    if ($lb_result == true && $li_cod_formapago == '1' && empty($li_cod_mediopago)) {
        $ls_mensaje = 'Debe seleccionar el medio de pago para un pago al contado.';
        $lb_result  = false;
    }

    // Validar que la cita exista
    if ($lb_result == true) {

        $array_campo_pk = array('N_COD_CITA');
        $array_valor_pk = array($li_cod_cita);

        $array_cita = $crud->fila_recuperar(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk);

        if ($array_cita === null) {
            $ls_mensaje = 'La cita indicada no existe.';
            $lb_result  = false;
        }
    }

    // Validar que la cita no tenga ya un comprobante registrado (no se puede volver a cobrar)
    if ($lb_result == true) {

        $array_campo_pk = array('N_COD_CITA', 'V_FLAG_ESTADO');
        $array_valor_pk = array($li_cod_cita, '1');

        $li_contador = $crud->fila_contar(DEF_TABLA_COMPROBANTE, $array_campo_pk, $array_valor_pk);

        if ($li_contador > 0) {
            $ls_mensaje = 'Esta cita ya tiene un comprobante de pago registrado. No se puede volver a cobrar.';
            $lb_result  = false;
        }
    }

    // ================================= FIN VALIDACIONES SERVIDOR =================================== //


    // ================================ CREAR COMPROBANTE (CABECERA) =================================== //

    $li_cod_comprobante = null;

    if ($lb_result == true) {

        // Al Contado: se paga todo de una, queda Pagado.
        // Al Crédito: el monto es lo que se debe en total, queda Pendiente hasta abonar en Cuentas por Cobrar.
        $ls_estado_comprobante = ($li_cod_formapago == '1') ? 'PAG' : 'PEN';

        // Calcular siguiente código (la tabla no es AUTO_INCREMENT)
        $li_cod_comprobante = $crud->fila_recuperar_indice(DEF_TABLA_COMPROBANTE, 'N_COD_COMPROBANTE');
        if (empty($li_cod_comprobante)) {
            $li_cod_comprobante = 1;
        }

        $array_campo = array(
            'N_COD_COMPROBANTE',
            'N_COD_CITA',
            'D_FEC_EMISION',
            'N_COD_MONEDA',
            'N_MONTO',
            'N_COD_FORMAPAGO',
            'N_COD_MEDIOPAGO',
            'V_COMPROBANTE',
            'V_OBSERVACION',
            'V_ESTADO_COMPROBANTE',
            'V_FLAG_ESTADO',
            'V_AUD_USR_REG',
            'D_AUD_FEC_REG'
        );

        $array_valor = array(
            $li_cod_comprobante,
            $li_cod_cita,
            $ld_fec_pago,
            $li_cod_moneda,
            $ln_monto_total,
            $li_cod_formapago,
            $li_cod_mediopago,
            $ls_comprobante,
            $ls_observacion,
            $ls_estado_comprobante,
            '1',
            $_SESSION['usr_conectado'],
            $ldt_fecha_insercion
        );

        $lb_result = $crud->fila_registrar(DEF_TABLA_COMPROBANTE, $array_campo, $array_valor, '0');

        if ($lb_result == false) {
            $ls_mensaje = 'No se pudo generar el comprobante.';
        }
    }

    // ============================================================================================== //


    // ====================== REGISTRAR EL PAGO COMPLETO (SOLO SI ES AL CONTADO) ========================= //

    if ($lb_result == true && $li_cod_formapago == '1') {

        // Calcular siguiente código (la tabla no es AUTO_INCREMENT)
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
            'V_COMPROBANTE',
            'V_OBSERVACION',
            'V_FLAG_ESTADO',
            'V_AUD_USR_REG',
            'D_AUD_FEC_REG'
        );

        $array_valor = array(
            $li_cod_pago,
            $li_cod_comprobante,
            $li_cod_cita,
            $ld_fec_pago,
            $li_cod_moneda,
            $ln_monto_total,
            $li_cod_mediopago,
            $ls_comprobante,
            $ls_observacion,
            '1',
            $_SESSION['usr_conectado'],
            $ldt_fecha_insercion
        );

        $lb_result = $crud->fila_registrar(DEF_TABLA_PAGO, $array_campo, $array_valor, '0');

        if ($lb_result == false) {
            $ls_mensaje = 'El comprobante se generó, pero no se pudo registrar el pago. Revise manualmente.';
        }
    }

    // Si es al Crédito, NO se crea ningún pago aquí: el comprobante queda 100% pendiente,
    // listo para que el primer abono se registre desde "Cuentas por Cobrar".

    // ==================================== FIN CRUD REGISTRAR ======================================= //

    // Redireccionar
    if ($lb_result == true) {
        if ($li_cod_formapago == '2') {
            header("Location: ../vista/mov_comprobante_lista.php?id_msgRpta=" . urlencode('OK - Crédito registrado. Ya puedes abonarlo desde Cuentas por Cobrar.'));
        } else {
            header("Location: ../vista/mov_pago_lista.php?id_msgRpta=" . urlencode('OK - Pago registrado correctamente.'));
        }
    } else {
        header("Location: ../vista/mov_pago_nuevo.php?id_msgRpta=" . urlencode($ls_mensaje));
    }
    exit;

}

?>
