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

// Según caso
if (isset($_GET['id_codigo'])) {      // Invocado desde lista
    $li_codigo = $bd->bd_escapeCadena($_GET['id_codigo']);
    $lb_form   = false;
} else {                              // Invocado desde formulario
    $li_codigo = $bd->bd_escapeCadena($_POST['id_codigo']);
    $lb_form   = true;
}

// ======================================== PRE ELIMINAR ============================================= //

$lb_result  = true;
$ls_mensaje = '';

// Verificar si el tratamiento tiene sesiones registradas
$array_campo_pk = array('N_COD_TRATAMIENTO', 'V_FLAG_ESTADO');
$array_valor_pk = array($li_codigo, '1');

$li_contador = $crud->fila_contar(DEF_TABLA_SESION, $array_campo_pk, $array_valor_pk);

if ($li_contador > 0) {
    $ls_mensaje = 'El tratamiento tiene sesiones registradas y no puede eliminarse. Márquelo como Suspendido o Finalizado en su lugar.';
    $lb_result  = false;
}

// Verificar si el tratamiento tiene citas asociadas
if ($lb_result == true) {

    $array_campo_pk = array('N_COD_TRATAMIENTO', 'V_FLAG_ESTADO');
    $array_valor_pk = array($li_codigo, '1');

    $li_contador = $crud->fila_contar(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk);

    if ($li_contador > 0) {
        $ls_mensaje = 'El tratamiento tiene citas asociadas y no puede eliminarse. Márquelo como Suspendido o Finalizado en su lugar.';
        $lb_result  = false;
    }
}

// Controlar error
if ($lb_result == false) {

    if ($lb_form == true) {
        header("Location: ../vista/mov_tratamiento_editar.php?id_codigo=" . $li_codigo . "&id_msgRpta=" . urlencode($ls_mensaje));
    } else {
        header("Location: ../vista/mov_tratamiento_lista.php?id_msgRpta=" . urlencode($ls_mensaje));
    }
    exit;
}

// ======================================= FIN PRE ELIMINAR ========================================== //


// ======================================== CRUD ELIMINAR =========================================== //

$array_campo_pk = array('N_COD_TRATAMIENTO');
$array_valor_pk = array($li_codigo);

$lb_result = $crud->fila_eliminar(DEF_TABLA_TRATAMIENTO, $array_campo_pk, $array_valor_pk);

// ======================================= FIN CRUD ELIMINAR ======================================== //

// Redireccionar
if ($lb_result == true) {
    header("Location: ../vista/mov_tratamiento_lista.php?id_msgRpta=" . urlencode('OK - Tratamiento eliminado correctamente.'));
} else {
    header("Location: ../vista/mov_tratamiento_lista.php?id_msgRpta=" . urlencode('No se pudo eliminar el tratamiento.'));
}
exit;

?>
