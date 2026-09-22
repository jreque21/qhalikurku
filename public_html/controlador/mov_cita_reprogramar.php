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

    $li_cod_cita_origen = $bd->bd_escapeCadena($_POST['id_cod_cita_origen']);
    $ld_nueva_fecha      = $bd->bd_escapeCadena($_POST['id_nueva_fecha']);
    $ldt_nueva_hora_ini  = $bd->bd_escapeCadena($_POST['id_nueva_hora_inicio']);
    $ldt_nueva_hora_fin  = $bd->bd_escapeCadena($_POST['id_nueva_hora_fin']);
    $ls_motivo_reprog    = $bd->bd_escapeCadena($_POST['id_motivo_reprog']);

    $ldt_fecha_actual = date('Y-m-d H:i:s');

    // ==================================== VALIDACIONES SERVIDOR ==================================== //

    // Exigir motivo de reprogramación
    if (empty(trim($ls_motivo_reprog))) {
        $ls_mensaje = 'Debe indicar el motivo de la reprogramación.';
        $lb_result  = false;
    }

    // Validar que la hora de fin sea posterior a la hora de inicio
    if ($lb_result == true && strtotime($ldt_nueva_hora_fin) <= strtotime($ldt_nueva_hora_ini)) {
        $ls_mensaje = 'La hora de fin debe ser posterior a la hora de inicio.';
        $lb_result  = false;
    }

    // Validar que la nueva fecha no sea anterior a hoy
    if ($lb_result == true && strtotime($ld_nueva_fecha) < strtotime(date('Y-m-d'))) {
        $ls_mensaje = 'No se puede reprogramar una cita a una fecha pasada.';
        $lb_result  = false;
    }

    // Recuperar datos de la cita original
    $array_cita_origen = null;
    if ($lb_result == true) {

        $array_campo_pk = array('N_COD_CITA');
        $array_valor_pk = array($li_cod_cita_origen);

        $array_cita_origen = $crud->fila_recuperar(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk);

        if ($array_cita_origen === null) {
            $ls_mensaje = 'La cita original no existe.';
            $lb_result  = false;
        } elseif (in_array($array_cita_origen['V_ESTADO_CITA'], array('CAN', 'ATE', 'REP'))) {
            $ls_mensaje = 'Esta cita no puede reprogramarse (está cancelada, atendida o ya fue reprogramada).';
            $lb_result  = false;
        }
    }

    // Validar cruce de horario del especialista en el NUEVO horario
    if ($lb_result == true) {

        $ls_condicion = "N_COD_ESPECIALISTA = '" . $array_cita_origen['N_COD_ESPECIALISTA'] . "'
                          AND N_COD_CITA <> '$li_cod_cita_origen'
                          AND D_FEC_CITA = '$ld_nueva_fecha'
                          AND V_ESTADO_CITA <> 'CAN'
                          AND V_FLAG_ESTADO = '1'
                          AND D_HORA_INICIO < '$ldt_nueva_hora_fin'
                          AND D_HORA_FIN > '$ldt_nueva_hora_ini'";

        $lr_cruce  = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . ' WHERE ' . $ls_condicion, '', '', -1, 0);
        $li_cuenta = $lr_cruce ? $lr_cruce->num_rows : 0;

        if ($li_cuenta > 0) {
            $ls_mensaje = 'El especialista ya tiene otra cita programada que se cruza con ese nuevo horario.';
            $lb_result  = false;
        }
    }

    // ================================= FIN VALIDACIONES SERVIDOR =================================== //


    // ================================ CREAR NUEVA CITA (REPROGRAMADA) ================================ //

    if ($lb_result == true) {

        $array_campo = array(
            'N_COD_SEDE',
            'N_COD_PACIENTE',
            'N_COD_ESPECIALISTA',
            'N_COD_TIPOTERAPIA',
            'D_FEC_CITA',
            'D_HORA_INICIO',
            'D_HORA_FIN',
            'V_MOT_CONSULTA',
            'N_COD_CITA_ORIGEN',
            'V_ESTADO_CITA',
            'V_FLAG_ESTADO',
            'V_AUD_USR_REG',
            'D_AUD_FEC_REG'
        );

        $array_valor = array(
            $array_cita_origen['N_COD_SEDE'],
            $array_cita_origen['N_COD_PACIENTE'],
            $array_cita_origen['N_COD_ESPECIALISTA'],
            $array_cita_origen['N_COD_TIPOTERAPIA'],
            $ld_nueva_fecha,
            $ldt_nueva_hora_ini,
            $ldt_nueva_hora_fin,
            $array_cita_origen['V_MOT_CONSULTA'],
            $li_cod_cita_origen,
            'PRO',
            '1',
            $_SESSION['usr_conectado'],
            $ldt_fecha_actual
        );

        $lb_result = $crud->fila_registrar(DEF_TABLA_CITA, $array_campo, $array_valor, '0');

        if ($lb_result == false) {
            $ls_mensaje = 'No se pudo crear la nueva cita reprogramada.';
        }
    }

    // ============================================================================================== //


    // ================================ MARCAR CITA ORIGINAL COMO REPROGRAMADA ========================= //

    if ($lb_result == true) {

        $li_nuevo_cod_cita = $crud->fila_recuperar_lastId(DEF_TABLA_CITA, 'N_COD_CITA');

        $array_campo_pk = array('N_COD_CITA');
        $array_valor_pk = array($li_cod_cita_origen);

        $array_campo = array(
            'V_ESTADO_CITA',
            'V_MOT_REPROG',
            'V_AUD_USR_MOD',
            'D_AUD_FEC_MOD'
        );

        $array_valor = array(
            'REP',
            $ls_motivo_reprog,
            $_SESSION['usr_conectado'],
            $ldt_fecha_actual
        );

        $lb_result = $crud->fila_actualizar(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);

        if ($lb_result == false) {
            $ls_mensaje = 'La nueva cita se creó, pero no se pudo marcar la cita original como reprogramada. Revise manualmente.';
        }
    }

    // ============================================================================================== //

    // Redireccionar
    if ($lb_result == true) {
        header("Location: ../vista/mov_cita_editar.php?id_codigo=" . $li_nuevo_cod_cita . "&id_msgRpta=" . urlencode('OK - Cita reprogramada correctamente.'));
    } else {
        header("Location: ../vista/mov_cita_editar.php?id_codigo=" . $li_cod_cita_origen . "&id_msgRpta=" . urlencode($ls_mensaje));
    }
    exit;

}

?>
