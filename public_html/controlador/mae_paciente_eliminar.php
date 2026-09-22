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

// Según Caso
if (isset($_GET['id_codigo'])) {      // Invocado desde lista
    $li_codigo = $_GET["id_codigo"];
    $lb_form   = false;
} else {                             // Invocado desde formulario
    $li_codigo = $bd->bd_escapeCadena($_POST['id_codigo']);
    $lb_form   = true;
}

// ======================================== PRE ELIMINAR ============================================= //

// Verificar si el paciente tiene citas registradas
$array_campo_pk = array('N_COD_PACIENTE', 'V_FLAG_ESTADO');
$array_valor_pk = array($li_codigo, '1');

$li_contador = $crud->fila_contar(
                    DEF_TABLA_CITA,
                    $array_campo_pk,
                    $array_valor_pk
                );

if ($li_contador > 0) {
    $ls_mensaje = 'Paciente se encuentra referenciado en registros de citas.';
}

// Verificar si tiene historia clínica
if ($li_contador == 0) {

    $li_contador = $crud->fila_contar(
                        DEF_TABLA_HISTORIA_CLINICA,
                        $array_campo_pk,
                        $array_valor_pk
                    );

    if ($li_contador > 0) {
        $ls_mensaje = 'Paciente se encuentra referenciado en historias clínicas.';
    }
}

// Controlar error
if ($li_contador > 0) {

    if ($lb_form == true) {
        header("Location: ../vista/mae_paciente_editar.php?id_codigo=" .
               $li_codigo .
               "&id_msgRpta=" .
               urlencode($ls_mensaje));
    } else {
        header("Location: ../vista/mae_paciente_lista.php?id_msgRpta=" .
               urlencode($ls_mensaje));
    }

    return;
}

// ======================================= FIN PRE ELIMINAR ========================================== //


// ======================================== CRUD ELIMINAR =========================================== //

// Definir estructura
$array_campo_pk = array('N_COD_PACIENTE');
$array_valor_pk = array($li_codigo);

// Recuperar foto del paciente
$ls_imagen_delete = $crud->fila_recuperar_campo(
                        DEF_TABLA_PACIENTE,
                        $array_campo_pk,
                        $array_valor_pk,
                        'V_FOTO'
                    );

// Eliminar registro
$lb_result = $crud->fila_eliminar(
                    DEF_TABLA_PACIENTE,
                    $array_campo_pk,
                    $array_valor_pk
                );

// ======================================= FIN CRUD ELIMINAR ======================================== //


// ======================================= ELIMINAR FOTO ============================================ //

if (!empty($ls_imagen_delete)) {

    $ls_imagen_delete =
        "../../upload/" .
        DEF_UPLOAD_PACIENTE_DIR .
        "/" .
        $ls_imagen_delete;

    if (file_exists($ls_imagen_delete)) {
        unlink($ls_imagen_delete);
    }
}

// ======================================= FIN ELIMINAR FOTO ======================================== //


// Redireccionar
if ($lb_result == true) {

    header("Location: ../vista/mae_paciente_lista.php");

} else {

    header("Location: ../vista/mae_paciente_editar.php?id_codigo=" .
           $li_codigo);
}

?>