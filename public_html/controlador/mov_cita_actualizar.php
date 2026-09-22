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

    $li_cod_cita         = $bd->bd_escapeCadena($_POST['id_cod_cita_hide']);
    $li_cod_sede         = $bd->bd_escapeCadena($_POST['id_cod_sede']);
    $li_cod_paciente     = $bd->bd_escapeCadena($_POST['id_cod_paciente']);
    $li_cod_especialista = $bd->bd_escapeCadena($_POST['id_cod_especialista']);
    $li_cod_tipoterapia  = isset($_POST['id_cod_tipoterapia']) && $_POST['id_cod_tipoterapia'] !== ''
                            ? $bd->bd_escapeCadena($_POST['id_cod_tipoterapia'])
                            : null;
    $li_cod_tratamiento  = isset($_POST['id_cod_tratamiento']) && $_POST['id_cod_tratamiento'] !== ''
                            ? $bd->bd_escapeCadena($_POST['id_cod_tratamiento'])
                            : null;
    $ld_fec_cita         = $bd->bd_escapeCadena($_POST['id_fec_cita']);
    $ldt_hora_inicio     = $bd->bd_escapeCadena($_POST['id_hora_inicio']);
    $ldt_hora_fin        = $bd->bd_escapeCadena($_POST['id_hora_fin']);
    $ls_motivo_consulta  = $bd->bd_escapeCadena($_POST['id_motivo_consulta']);
    $ls_diagnostico      = $bd->bd_escapeCadena($_POST['id_diagnostico']);
    $ls_observacion_cita = $bd->bd_escapeCadena($_POST['id_observacion_cita']);
    $ls_estado_cita      = $bd->bd_escapeCadena($_POST['id_estado_cita']);

    $ldt_fecha_actualizacion = date('Y-m-d H:i:s');

    // ==================================== VALIDACIONES SERVIDOR ==================================== //

    // Validar que la cita no esté reprogramada, cancelada o completada
    // (una vez reprogramada, la cita original queda congelada como historial).
    // Una cita "Atendida" sí puede seguir modificándose hasta marcarse "Completada".
    if ($lb_result == true) {

        $array_campo_pk = array('N_COD_CITA');
        $array_valor_pk = array($li_cod_cita);

        $ls_estado_actual = $crud->fila_recuperar_campo(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk, 'V_ESTADO_CITA');

        if ($ls_estado_actual == 'REP') {
            $ls_mensaje = 'Esta cita fue reprogramada y ya no puede modificarse. Consulte la nueva cita generada.';
            $lb_result  = false;
        } elseif ($ls_estado_actual == 'CAN') {
            $ls_mensaje = 'Esta cita está cancelada y ya no puede modificarse.';
            $lb_result  = false;
        } elseif ($ls_estado_actual == 'COM') {
            $ls_mensaje = 'Esta cita ya fue completada y ya no puede modificarse.';
            $lb_result  = false;
        }
    }

    // Validar que la hora de fin sea posterior a la hora de inicio
    if ($lb_result == true && strtotime($ldt_hora_fin) <= strtotime($ldt_hora_inicio)) {
        $ls_mensaje = 'La hora de fin debe ser posterior a la hora de inicio.';
        $lb_result  = false;
    }

    // Validar cruce de horario, EXCLUYENDO la propia cita que se está editando
    if ($lb_result == true) {

        $ls_condicion = "N_COD_ESPECIALISTA = '$li_cod_especialista'
                          AND N_COD_CITA <> '$li_cod_cita'
                          AND D_FEC_CITA = '$ld_fec_cita'
                          AND V_ESTADO_CITA <> 'CAN'
                          AND V_FLAG_ESTADO = '1'
                          AND D_HORA_INICIO < '$ldt_hora_fin'
                          AND D_HORA_FIN > '$ldt_hora_inicio'";

        $lr_cruce  = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . ' WHERE ' . $ls_condicion, '', '', -1, 0);
        $li_cuenta = $lr_cruce ? $lr_cruce->num_rows : 0;

        if ($li_cuenta > 0) {
            $ls_mensaje = 'El especialista ya tiene otra cita programada que se cruza con ese horario.';
            $lb_result  = false;
        }
    }

    // ================================= FIN VALIDACIONES SERVIDOR =================================== //


    // ======================================= CRUD ACTUALIZAR =========================================== //

    if ($lb_result == true) {

        $array_campo_pk = array('N_COD_CITA');
        $array_valor_pk = array($li_cod_cita);

        $array_campo = array(
            'N_COD_SEDE',
            'N_COD_PACIENTE',
            'N_COD_ESPECIALISTA',
            'N_COD_TIPOTERAPIA',
            'N_COD_TRATAMIENTO',
            'D_FEC_CITA',
            'D_HORA_INICIO',
            'D_HORA_FIN',
            'V_MOT_CONSULTA',
            'V_DIAGNOSTICO',
            'V_OBSERVACION',
            'V_ESTADO_CITA',
            'V_AUD_USR_MOD',
            'D_AUD_FEC_MOD'
        );

        $array_valor = array(
            $li_cod_sede,
            $li_cod_paciente,
            $li_cod_especialista,
            $li_cod_tipoterapia,
            $li_cod_tratamiento,
            $ld_fec_cita,
            $ldt_hora_inicio,
            $ldt_hora_fin,
            $ls_motivo_consulta,
            $ls_diagnostico,
            $ls_observacion_cita,
            $ls_estado_cita,
            $_SESSION['usr_conectado'],
            $ldt_fecha_actualizacion
        );

        // Invocar Actualizacion
        $lb_result = $crud->fila_actualizar(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);

        if ($lb_result == false) {
            $ls_mensaje = 'No se pudo actualizar la cita. Intente nuevamente.';
        }
    }

    // ===================================== FIN CRUD ACTUALIZAR ========================================= //


    // ============================== SINCRONIZAR HISTORIA CLINICA =================================== //

    if ($lb_result == true) {
        f_sincronizar_historia_clinica(
            $crud,
            $li_cod_paciente,
            $ld_fec_cita,
            $ls_diagnostico,
            $ls_observacion_cita,
            $li_cod_cita,
            null,
            $_SESSION['usr_conectado']
        );
    }

    // ============================================================================================== //

    // Redireccionar
    if ($lb_result == true) {
        header("Location: ../vista/mov_cita_lista.php?id_msgRpta=" . urlencode('OK - Cita actualizada correctamente.'));
    } else {
        header("Location: ../vista/mov_cita_editar.php?id_codigo=" . $li_cod_cita . "&id_msgRpta=" . urlencode($ls_mensaje));
    }
    exit;

}

?>
