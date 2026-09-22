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

    $li_cod_sesion      = $bd->bd_escapeCadena($_POST['id_cod_sesion_hide']);
    $li_cod_tratamiento = $bd->bd_escapeCadena($_POST['id_cod_tratamiento_hide']);
    $ld_fec_sesion      = $bd->bd_escapeCadena($_POST['id_fec_sesion']);
    $ls_observacion     = $bd->bd_escapeCadena($_POST['id_observacion']);
    $ls_evolucion       = $bd->bd_escapeCadena($_POST['id_evolucion']);

    $ldt_fecha_actualizacion = date('Y-m-d H:i:s');

    // ======================================= CRUD ACTUALIZAR =========================================== //

    $array_campo_pk = array('N_COD_SESION');
    $array_valor_pk = array($li_cod_sesion);

    $array_campo = array(
        'D_FEC_SESION',
        'V_OBSERVACION',
        'V_EVOLUCION',
        'V_AUD_USR_MOD',
        'D_AUD_FEC_MOD'
    );

    $array_valor = array(
        $ld_fec_sesion,
        $ls_observacion,
        $ls_evolucion,
        $_SESSION['usr_conectado'],
        $ldt_fecha_actualizacion
    );

    $lb_result = $crud->fila_actualizar(DEF_TABLA_SESION, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);

    if ($lb_result == false) {
        $ls_mensaje = 'No se pudo actualizar la sesión.';
    }

    // ===================================== FIN CRUD ACTUALIZAR ========================================= //


    // ============================== SINCRONIZAR HISTORIA CLINICA =================================== //

    if ($lb_result == true) {

        $array_campo_pk = array('N_COD_TRATAMIENTO');
        $array_valor_pk = array($li_cod_tratamiento);
        $array_tratamiento = $crud->fila_recuperar(DEF_TABLA_TRATAMIENTO, $array_campo_pk, $array_valor_pk);

        $array_campo_pk = array('N_COD_SESION');
        $array_valor_pk = array($li_cod_sesion);
        $li_num_sesion = $crud->fila_recuperar_campo(DEF_TABLA_SESION, $array_campo_pk, $array_valor_pk, 'N_NUM_SESION');

        $ls_texto_historia = '';
        if (!empty(trim($ls_evolucion))) {
            $ls_texto_historia .= 'Evolución (Sesión N° ' . intval($li_num_sesion) . '): ' . $ls_evolucion;
        }
        if (!empty(trim($ls_observacion))) {
            $ls_texto_historia .= (!empty($ls_texto_historia) ? ' | ' : '') . 'Observación: ' . $ls_observacion;
        }

        f_sincronizar_historia_clinica(
            $crud,
            $array_tratamiento['N_COD_PACIENTE'],
            $ld_fec_sesion,
            '',
            $ls_texto_historia,
            null,
            $li_cod_sesion,
            $_SESSION['usr_conectado']
        );
    }

    // ============================================================================================== //

    // Redireccionar
    if ($lb_result == true) {
        header("Location: ../vista/mov_sesion_lista.php?id_cod_tratamiento_filtro=" . $li_cod_tratamiento . "&id_msgRpta=" . urlencode('OK - Sesión actualizada correctamente.'));
    } else {
        header("Location: ../vista/mov_sesion_editar.php?id_codigo=" . $li_cod_sesion . "&id_msgRpta=" . urlencode($ls_mensaje));
    }
    exit;

}

?>
