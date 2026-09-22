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

    $ldt_fecha_insercion = date('Y-m-d H:i:s');

    // ==================================== VALIDACIONES SERVIDOR ==================================== //

    // Validar que la hora de fin sea posterior a la hora de inicio
    if ($lb_result == true && strtotime($ldt_hora_fin) <= strtotime($ldt_hora_inicio)) {
        $ls_mensaje = 'La hora de fin debe ser posterior a la hora de inicio.';
        $lb_result  = false;
    }

    // Validar que la fecha de la cita no sea anterior a hoy
    if ($lb_result == true && strtotime($ld_fec_cita) < strtotime(date('Y-m-d'))) {
        $ls_mensaje = 'No se pueden registrar citas en fechas pasadas.';
        $lb_result  = false;
    }

    // Validar cruce de horario: mismo especialista, misma fecha,
    // rango de horas que se superpone, y la cita no está cancelada
    if ($lb_result == true) {

        $ls_condicion = "N_COD_ESPECIALISTA = '$li_cod_especialista'
                          AND D_FEC_CITA = '$ld_fec_cita'
                          AND V_ESTADO_CITA <> 'CAN'
                          AND V_FLAG_ESTADO = '1'
                          AND D_HORA_INICIO < '$ldt_hora_fin'
                          AND D_HORA_FIN > '$ldt_hora_inicio'";

        $lr_cruce  = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . ' WHERE ' . $ls_condicion, '', '', -1, 0);
        $li_cuenta = $lr_cruce ? $lr_cruce->num_rows : 0;

        if ($li_cuenta > 0) {
            $ls_mensaje = 'El especialista ya tiene una cita programada que se cruza con ese horario.';
            $lb_result  = false;
        }
    }

    // ================================= FIN VALIDACIONES SERVIDOR =================================== //


    // ====================================== CRUD REGISTRAR ========================================= //

    if ($lb_result == true) {

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
            'V_FLAG_ESTADO',
            'V_AUD_USR_REG',
            'D_AUD_FEC_REG'
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
            'PRO',
            '1',
            $_SESSION['usr_conectado'],
            $ldt_fecha_insercion
        );

        // Registrar cita
        $lb_result = $crud->fila_registrar(
                        DEF_TABLA_CITA,
                        $array_campo,
                        $array_valor,
                        '0'
                     );

        if ($lb_result == false) {
            $ls_mensaje = 'No se pudo registrar la cita. Intente nuevamente.';
        }
    }

    // ==================================== FIN CRUD REGISTRAR ======================================= //


    // ============================== SINCRONIZAR HISTORIA CLINICA =================================== //

    if ($lb_result == true) {

        $li_cod_cita_nueva = $crud->fila_recuperar_lastId(DEF_TABLA_CITA, 'N_COD_CITA');

        f_sincronizar_historia_clinica(
            $crud,
            $li_cod_paciente,
            $ld_fec_cita,
            $ls_diagnostico,
            $ls_observacion_cita,
            $li_cod_cita_nueva,
            null,
            $_SESSION['usr_conectado']
        );
    }

    // ============================================================================================== //

    // Redireccionar
    if ($lb_result == true) {
        header("Location: ../vista/mov_cita_lista.php?id_msgRpta=" . urlencode('OK - Cita registrada correctamente.'));
    } else {
        header("Location: ../vista/mov_cita_nuevo.php?id_msgRpta=" . urlencode($ls_mensaje));
    }
    exit;

}

?>
