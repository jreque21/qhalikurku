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

if (isset($_GET['id_codigo'])) {
    $li_codigo = $bd->bd_escapeCadena($_GET['id_codigo']);
    $lb_form   = false;
} else {
    $li_codigo = $bd->bd_escapeCadena($_POST['id_codigo']);
    $lb_form   = true;
}

$lb_result  = true;
$ls_mensaje = '';

$array_campo_pk = array('N_COD_HISTORIA');
$array_valor_pk = array($li_codigo);
$array_historia = $crud->fila_recuperar(DEF_TABLA_HISTORIA_CLINICA, $array_campo_pk, $array_valor_pk);

if ($array_historia === null) {
    $ls_mensaje = 'La entrada indicada no existe.';
    $lb_result  = false;
}

// Solo se puede eliminar la última entrada registrada del paciente (evita huecos en el historial)
if ($lb_result == true) {

    $array_campo_pk = array('N_COD_PACIENTE', 'V_FLAG_ESTADO');
    $array_valor_pk = array($array_historia['N_COD_PACIENTE'], '1');

    $lr_ultima = $crud->fila_listar(DEF_TABLA_HISTORIA_CLINICA, $array_campo_pk, $array_valor_pk, 'N_COD_HISTORIA', 'D', 0, 1);
    $array_ultima = $lr_ultima ? mysqli_fetch_assoc($lr_ultima) : null;

    if ($array_ultima === null || $array_ultima['N_COD_HISTORIA'] != $array_historia['N_COD_HISTORIA']) {
        $ls_mensaje = 'Solo se puede eliminar la última entrada registrada del paciente.';
        $lb_result  = false;
    }
}

if ($lb_result == false) {
    if ($lb_form == true) {
        header("Location: ../vista/mov_historia_editar.php?id_codigo=" . $li_codigo . "&id_msgRpta=" . urlencode($ls_mensaje));
    } else {
        header("Location: ../vista/mov_historia_lista.php?id_cod_paciente_filtro=" . (isset($array_historia['N_COD_PACIENTE']) ? $array_historia['N_COD_PACIENTE'] : '') . "&id_msgRpta=" . urlencode($ls_mensaje));
    }
    exit;
}

$li_cod_paciente = $array_historia['N_COD_PACIENTE'];

$array_campo_pk = array('N_COD_HISTORIA');
$array_valor_pk = array($li_codigo);
$lb_result = $crud->fila_eliminar(DEF_TABLA_HISTORIA_CLINICA, $array_campo_pk, $array_valor_pk);

if ($lb_result == true) {
    header("Location: ../vista/mov_historia_lista.php?id_cod_paciente_filtro=" . $li_cod_paciente . "&id_msgRpta=" . urlencode('OK - Entrada eliminada correctamente.'));
} else {
    header("Location: ../vista/mov_historia_lista.php?id_cod_paciente_filtro=" . $li_cod_paciente . "&id_msgRpta=" . urlencode('No se pudo eliminar la entrada.'));
}
exit;

?>
