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

$lb_result  = true;
$ls_mensaje = '';

if (isset($_POST) && !empty($_POST)) {

    $li_cod_paciente  = $bd->bd_escapeCadena($_POST['id_cod_paciente']);
    $ld_fec_cita      = $bd->bd_escapeCadena($_POST['id_fec_cita']);
    $ls_diagnostico   = $bd->bd_escapeCadena($_POST['id_diagnostico']);
    $ls_observacion   = $bd->bd_escapeCadena($_POST['id_observacion']);
    $ls_antecedentes  = $bd->bd_escapeCadena($_POST['id_antecedentes']);
    $ls_alergias      = $bd->bd_escapeCadena($_POST['id_alergias']);
    $ls_enfermedades  = $bd->bd_escapeCadena($_POST['id_enfermedades']);

    $ldt_fecha_insercion = date('Y-m-d H:i:s');

    $array_campo = array(
        'N_COD_PACIENTE',
        'D_FEC_CITA',
        'V_DIAGNOSTICO',
        'V_OBSERVACION',
        'V_ANTECEDENTES',
        'V_ALERGIAS',
        'V_ENFERMEDADES',
        'V_FLAG_ESTADO',
        'V_AUD_USR_REG',
        'D_AUD_FEC_REG'
    );

    $array_valor = array(
        $li_cod_paciente,
        $ld_fec_cita,
        $ls_diagnostico,
        $ls_observacion,
        $ls_antecedentes,
        $ls_alergias,
        $ls_enfermedades,
        '1',
        $_SESSION['usr_conectado'],
        $ldt_fecha_insercion
    );

    $lb_result = $crud->fila_registrar(DEF_TABLA_HISTORIA_CLINICA, $array_campo, $array_valor, '0');

    if ($lb_result == false) {
        $ls_mensaje = 'No se pudo registrar la entrada de historia clínica.';
    }

    if ($lb_result == true) {
        header("Location: ../vista/mov_historia_lista.php?id_cod_paciente_filtro=" . $li_cod_paciente . "&id_msgRpta=" . urlencode('OK - Entrada registrada correctamente.'));
    } else {
        header("Location: ../vista/mov_historia_nuevo.php?id_cod_paciente=" . $li_cod_paciente . "&id_msgRpta=" . urlencode($ls_mensaje));
    }
    exit;

}

?>
