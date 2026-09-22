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

    $li_cod_tratamiento  = $bd->bd_escapeCadena($_POST['id_cod_tratamiento_hide']);
    $li_cod_sede         = $bd->bd_escapeCadena($_POST['id_cod_sede']);
    $li_cod_paciente     = $bd->bd_escapeCadena($_POST['id_cod_paciente']);
    $li_cod_especialista = $bd->bd_escapeCadena($_POST['id_cod_especialista']);
    $ld_fec_inicio       = $bd->bd_escapeCadena($_POST['id_fec_inicio']);
    $ld_fec_fin          = !empty($_POST['id_fec_fin']) ? $bd->bd_escapeCadena($_POST['id_fec_fin']) : null;
    $li_num_sesiones     = $bd->bd_escapeCadena($_POST['id_num_sesiones']);
    $ls_diagnostico      = $bd->bd_escapeCadena($_POST['id_diagnostico']);
    $ls_observacion_trat = $bd->bd_escapeCadena($_POST['id_observacion_trat']);
    $ls_estado_trat      = $bd->bd_escapeCadena($_POST['id_estado_tratamiento']);

    $ldt_fecha_actualizacion = date('Y-m-d H:i:s');

    // ==================================== VALIDACIONES SERVIDOR ==================================== //

    // Validar número de sesiones positivo
    if ($lb_result == true && (!is_numeric($li_num_sesiones) || intval($li_num_sesiones) <= 0)) {
        $ls_mensaje = 'El número de sesiones debe ser un valor mayor a cero.';
        $lb_result  = false;
    }

    // Si se marca como Finalizado, exigir fecha de fin
    if ($lb_result == true && $ls_estado_trat == 'FIN' && empty($ld_fec_fin)) {
        $ls_mensaje = 'Debe indicar la fecha de fin para marcar el tratamiento como Finalizado.';
        $lb_result  = false;
    }

    // ================================= FIN VALIDACIONES SERVIDOR =================================== //


    // ======================================= CRUD ACTUALIZAR =========================================== //

    if ($lb_result == true) {

        $array_campo_pk = array('N_COD_TRATAMIENTO');
        $array_valor_pk = array($li_cod_tratamiento);

        $array_campo = array(
            'N_COD_SEDE',
            'N_COD_PACIENTE',
            'N_COD_ESPECIALISTA',
            'D_FEC_INICIO',
            'D_FEC_FIN',
            'N_NUM_SESIONES',
            'V_DIAGNOSTICO',
            'V_OBSERVACION',
            'V_ESTADO_TRATAMIENTO',
            'V_AUD_USR_MOD',
            'D_AUD_FEC_MOD'
        );

        $array_valor = array(
            $li_cod_sede,
            $li_cod_paciente,
            $li_cod_especialista,
            $ld_fec_inicio,
            $ld_fec_fin,
            $li_num_sesiones,
            $ls_diagnostico,
            $ls_observacion_trat,
            $ls_estado_trat,
            $_SESSION['usr_conectado'],
            $ldt_fecha_actualizacion
        );

        $lb_result = $crud->fila_actualizar(DEF_TABLA_TRATAMIENTO, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);

        if ($lb_result == false) {
            $ls_mensaje = 'No se pudo actualizar el tratamiento. Intente nuevamente.';
        }
    }

    // ===================================== FIN CRUD ACTUALIZAR ========================================= //

    // Redireccionar
    if ($lb_result == true) {
        header("Location: ../vista/mov_tratamiento_lista.php?id_msgRpta=" . urlencode('OK - Tratamiento actualizado correctamente.'));
    } else {
        header("Location: ../vista/mov_tratamiento_editar.php?id_codigo=" . $li_cod_tratamiento . "&id_msgRpta=" . urlencode($ls_mensaje));
    }
    exit;

}

?>
