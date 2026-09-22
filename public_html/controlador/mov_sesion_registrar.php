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

    $li_cod_tratamiento = $bd->bd_escapeCadena($_POST['id_cod_tratamiento']);
    $li_cod_cita        = isset($_POST['id_cod_cita']) && $_POST['id_cod_cita'] !== ''
                            ? $bd->bd_escapeCadena($_POST['id_cod_cita'])
                            : null;
    $ld_fec_sesion      = $bd->bd_escapeCadena($_POST['id_fec_sesion']);
    $ls_observacion     = $bd->bd_escapeCadena($_POST['id_observacion']);
    $ls_evolucion       = $bd->bd_escapeCadena($_POST['id_evolucion']);

    $ldt_fecha_insercion = date('Y-m-d H:i:s');

    // ==================================== VALIDACIONES SERVIDOR ==================================== //

    // Validar que el tratamiento exista y esté Activo
    $array_tratamiento = null;
    if ($lb_result == true) {

        $array_campo_pk = array('N_COD_TRATAMIENTO');
        $array_valor_pk = array($li_cod_tratamiento);

        $array_tratamiento = $crud->fila_recuperar(DEF_TABLA_TRATAMIENTO, $array_campo_pk, $array_valor_pk);

        if ($array_tratamiento === null) {
            $ls_mensaje = 'El tratamiento indicado no existe.';
            $lb_result  = false;
        } elseif ($array_tratamiento['V_ESTADO_TRATAMIENTO'] != 'ACT') {
            $ls_mensaje = 'Solo se pueden registrar sesiones para tratamientos Activos.';
            $lb_result  = false;
        }
    }

    // Calcular el número de sesión automáticamente (siguiente correlativo)
    $li_num_sesion = 1;
    if ($lb_result == true) {

        $array_campo_pk = array('N_COD_TRATAMIENTO', 'V_FLAG_ESTADO');
        $array_valor_pk = array($li_cod_tratamiento, '1');

        $li_num_sesion = intval($crud->fila_contar(DEF_TABLA_SESION, $array_campo_pk, $array_valor_pk)) + 1;

        // Validar que no exceda el número de sesiones planificadas
        if ($li_num_sesion > intval($array_tratamiento['N_NUM_SESIONES'])) {
            $ls_mensaje = 'Este tratamiento ya alcanzó el número de sesiones planificadas (' . $array_tratamiento['N_NUM_SESIONES'] . '). Actualice el plan de tratamiento si necesita agregar más.';
            $lb_result  = false;
        }
    }

    // ================================= FIN VALIDACIONES SERVIDOR =================================== //


    // ====================================== CRUD REGISTRAR ========================================= //

    if ($lb_result == true) {

        $array_campo = array(
            'N_COD_TRATAMIENTO',
            'N_COD_CITA',
            'N_NUM_SESION',
            'D_FEC_SESION',
            'V_OBSERVACION',
            'V_EVOLUCION',
            'V_FLAG_ESTADO',
            'V_AUD_USR_REG',
            'D_AUD_FEC_REG'
        );

        $array_valor = array(
            $li_cod_tratamiento,
            $li_cod_cita,
            $li_num_sesion,
            $ld_fec_sesion,
            $ls_observacion,
            $ls_evolucion,
            '1',
            $_SESSION['usr_conectado'],
            $ldt_fecha_insercion
        );

        // Registrar sesión
        $lb_result = $crud->fila_registrar(
                        DEF_TABLA_SESION,
                        $array_campo,
                        $array_valor,
                        '0'
                     );

        if ($lb_result == false) {
            $ls_mensaje = 'No se pudo registrar la sesión. Intente nuevamente.';
        }
    }

    // ==================================== FIN CRUD REGISTRAR ======================================= //


    // ============================== SINCRONIZAR HISTORIA CLINICA =================================== //

    if ($lb_result == true) {

        $li_cod_sesion_nueva = $crud->fila_recuperar_lastId(DEF_TABLA_SESION, 'N_COD_SESION');

        // Combinar evolución + observación en un solo texto para la historia clínica
        $ls_texto_historia = '';
        if (!empty(trim($ls_evolucion))) {
            $ls_texto_historia .= 'Evolución (Sesión N° ' . $li_num_sesion . '): ' . $ls_evolucion;
        }
        if (!empty(trim($ls_observacion))) {
            $ls_texto_historia .= (!empty($ls_texto_historia) ? ' | ' : '') . 'Observación: ' . $ls_observacion;
        }

        f_sincronizar_historia_clinica(
            $crud,
            $array_tratamiento['N_COD_PACIENTE'],
            $ld_fec_sesion,
            '', // Las sesiones no traen diagnóstico propio; eso se define a nivel de Cita
            $ls_texto_historia,
            null,
            $li_cod_sesion_nueva,
            $_SESSION['usr_conectado']
        );
    }

    // ============================================================================================== //


    // ============================== MARCAR CITA ASOCIADA COMO ATENDIDA =============================== //

    if ($lb_result == true && !empty($li_cod_cita)) {

        $array_campo_pk = array('N_COD_CITA');
        $array_valor_pk = array($li_cod_cita);

        $array_campo = array('V_ESTADO_CITA', 'V_AUD_USR_MOD', 'D_AUD_FEC_MOD');
        $array_valor = array('ATE', $_SESSION['usr_conectado'], $ldt_fecha_insercion);

        $crud->fila_actualizar(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);
        // No se controla el resultado de este paso: la sesión ya quedó registrada,
        // que la cita no se pueda marcar como Atendida no debe bloquear el flujo.
    }

    // ============================================================================================== //

    // Redireccionar
    if ($lb_result == true) {
        header("Location: ../vista/mov_sesion_lista.php?id_cod_tratamiento_filtro=" . $li_cod_tratamiento . "&id_msgRpta=" . urlencode('OK - Sesión registrada correctamente.'));
    } else {
        header("Location: ../vista/mov_sesion_nuevo.php?id_cod_tratamiento=" . $li_cod_tratamiento . "&id_msgRpta=" . urlencode($ls_mensaje));
    }
    exit;

}

?>
