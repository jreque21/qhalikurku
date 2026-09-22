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
    $ld_fec_inicio       = $bd->bd_escapeCadena($_POST['id_fec_inicio']);
    $li_num_sesiones     = $bd->bd_escapeCadena($_POST['id_num_sesiones']);
    $ls_diagnostico      = $bd->bd_escapeCadena($_POST['id_diagnostico']);
    $ls_observacion_trat = $bd->bd_escapeCadena($_POST['id_observacion_trat']);

    $ldt_fecha_insercion = date('Y-m-d H:i:s');

    // ==================================== VALIDACIONES SERVIDOR ==================================== //

    // Validar número de sesiones positivo
    if ($lb_result == true && (!is_numeric($li_num_sesiones) || intval($li_num_sesiones) <= 0)) {
        $ls_mensaje = 'El número de sesiones debe ser un valor mayor a cero.';
        $lb_result  = false;
    }

    // Validar diagnóstico obligatorio
    if ($lb_result == true && empty(trim($ls_diagnostico))) {
        $ls_mensaje = 'Debe ingresar el diagnóstico del tratamiento.';
        $lb_result  = false;
    }

    // ================================= FIN VALIDACIONES SERVIDOR =================================== //


    // ====================================== CRUD REGISTRAR ========================================= //

    if ($lb_result == true) {

        $array_campo = array(
            'N_COD_SEDE',
            'N_COD_PACIENTE',
            'N_COD_ESPECIALISTA',
            'D_FEC_INICIO',
            'N_NUM_SESIONES',
            'V_DIAGNOSTICO',
            'V_OBSERVACION',
            'V_ESTADO_TRATAMIENTO',
            'V_FLAG_ESTADO',
            'V_AUD_USR_REG',
            'D_AUD_FEC_REG'
        );

        $array_valor = array(
            $li_cod_sede,
            $li_cod_paciente,
            $li_cod_especialista,
            $ld_fec_inicio,
            $li_num_sesiones,
            $ls_diagnostico,
            $ls_observacion_trat,
            'ACT',
            '1',
            $_SESSION['usr_conectado'],
            $ldt_fecha_insercion
        );

        // Registrar tratamiento
        $lb_result = $crud->fila_registrar(
                        DEF_TABLA_TRATAMIENTO,
                        $array_campo,
                        $array_valor,
                        '0'
                     );

        if ($lb_result == false) {
            $ls_mensaje = 'No se pudo registrar el tratamiento. Intente nuevamente.';
        }
    }

    // ==================================== FIN CRUD REGISTRAR ======================================= //

    // Redireccionar
    if ($lb_result == true) {
        header("Location: ../vista/mov_tratamiento_lista.php?id_msgRpta=" . urlencode('OK - Tratamiento registrado correctamente.'));
    } else {
        header("Location: ../vista/mov_tratamiento_nuevo.php?id_msgRpta=" . urlencode($ls_mensaje));
    }
    exit;

}

?>
