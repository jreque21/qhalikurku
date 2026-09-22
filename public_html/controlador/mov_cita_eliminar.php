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

// Según caso: invocado desde la lista (GET) o desde el formulario (POST)
if (isset($_GET['id_codigo'])) {
    $li_codigo    = $bd->bd_escapeCadena($_GET['id_codigo']);
    $ls_motivo    = isset($_GET['id_motivo']) ? $bd->bd_escapeCadena($_GET['id_motivo']) : 'Cancelada desde el listado';
    $lb_form      = false;
} else {
    $li_codigo    = $bd->bd_escapeCadena($_POST['id_codigo']);
    $ls_motivo    = isset($_POST['id_motivo_cancelacion']) ? $bd->bd_escapeCadena($_POST['id_motivo_cancelacion']) : '';
    $lb_form      = true;
}

// ======================================== VALIDACIONES ============================================= //

$lb_result  = true;
$ls_mensaje = '';

// Exigir motivo de cancelación
if (empty(trim($ls_motivo))) {
    $ls_mensaje = 'Debe indicar el motivo de la cancelación.';
    $lb_result  = false;
}

// Verificar que la cita exista y no esté ya cancelada
if ($lb_result == true) {

    $array_campo_pk = array('N_COD_CITA');
    $array_valor_pk = array($li_codigo);

    $ls_estado_actual = $crud->fila_recuperar_campo(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk, 'V_ESTADO_CITA');

    if ($ls_estado_actual === null || $ls_estado_actual === '') {
        $ls_mensaje = 'La cita indicada no existe.';
        $lb_result  = false;
    } elseif ($ls_estado_actual == 'CAN') {
        $ls_mensaje = 'Esta cita ya se encuentra cancelada.';
        $lb_result  = false;
    } elseif ($ls_estado_actual == 'REP') {
        $ls_mensaje = 'Esta cita fue reprogramada y ya no puede cancelarse. Cancele la nueva cita generada si corresponde.';
        $lb_result  = false;
    } elseif ($ls_estado_actual == 'ATE') {
        $ls_mensaje = 'No se puede cancelar una cita que ya fue atendida.';
        $lb_result  = false;
    }
}

// ====================================== FIN VALIDACIONES =========================================== //


// ======================================== CANCELAR (SOFT) =========================================== //

if ($lb_result == true) {

    $array_campo_pk = array('N_COD_CITA');
    $array_valor_pk = array($li_codigo);

    $array_campo = array(
        'V_ESTADO_CITA',
        'V_MOT_CANC',
        'V_AUD_USR_MOD',
        'D_AUD_FEC_MOD'
    );

    $array_valor = array(
        'CAN',
        $ls_motivo,
        $_SESSION['usr_conectado'],
        date('Y-m-d H:i:s')
    );

    $lb_result = $crud->fila_actualizar(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);

    if ($lb_result == false) {
        $ls_mensaje = 'No se pudo cancelar la cita. Intente nuevamente.';
    }
}

// ===================================== FIN CANCELAR (SOFT) ========================================== //

// Redireccionar
if ($lb_result == true) {

    header("Location: ../vista/mov_cita_lista.php?id_msgRpta=" . urlencode('OK - Cita cancelada correctamente.'));

} else {

    if ($lb_form == true) {
        header("Location: ../vista/mov_cita_editar.php?id_codigo=" . $li_codigo . "&id_msgRpta=" . urlencode($ls_mensaje));
    } else {
        header("Location: ../vista/mov_cita_lista.php?id_msgRpta=" . urlencode($ls_mensaje));
    }
}
exit;

?>
