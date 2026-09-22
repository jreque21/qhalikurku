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

if (isset($_POST) && !empty($_POST)) {

    $li_cod_historia  = $bd->bd_escapeCadena($_POST['id_cod_historia_hide']);
    $li_cod_paciente  = $bd->bd_escapeCadena($_POST['id_cod_paciente_hide']);
    $ld_fec_cita      = $bd->bd_escapeCadena($_POST['id_fec_cita']);
    $ls_diagnostico   = $bd->bd_escapeCadena($_POST['id_diagnostico']);
    $ls_observacion   = $bd->bd_escapeCadena($_POST['id_observacion']);
    $ls_antecedentes  = $bd->bd_escapeCadena($_POST['id_antecedentes']);
    $ls_alergias      = $bd->bd_escapeCadena($_POST['id_alergias']);
    $ls_enfermedades  = $bd->bd_escapeCadena($_POST['id_enfermedades']);

    $array_campo_pk = array('N_COD_HISTORIA');
    $array_valor_pk = array($li_cod_historia);

    $array_campo = array(
        'D_FEC_CITA',
        'V_DIAGNOSTICO',
        'V_OBSERVACION',
        'V_ANTECEDENTES',
        'V_ALERGIAS',
        'V_ENFERMEDADES',
        'V_AUD_USR_MOD',
        'D_AUD_FEC_MOD'
    );

    $array_valor = array(
        $ld_fec_cita,
        $ls_diagnostico,
        $ls_observacion,
        $ls_antecedentes,
        $ls_alergias,
        $ls_enfermedades,
        $_SESSION['usr_conectado'],
        date('Y-m-d H:i:s')
    );

    $lb_result = $crud->fila_actualizar(DEF_TABLA_HISTORIA_CLINICA, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);

    if ($lb_result == true) {
        header("Location: ../vista/mov_historia_lista.php?id_cod_paciente_filtro=" . $li_cod_paciente . "&id_msgRpta=" . urlencode('OK - Entrada actualizada correctamente.'));
    } else {
        header("Location: ../vista/mov_historia_editar.php?id_codigo=" . $li_cod_historia . "&id_msgRpta=" . urlencode('No se pudo actualizar.'));
    }
    exit;

}

?>
