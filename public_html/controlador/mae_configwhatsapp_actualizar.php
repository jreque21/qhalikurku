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

    $ls_phone_id     = $bd->bd_escapeCadena($_POST['id_phone_number_id']);
    $ls_token        = $bd->bd_escapeCadena($_POST['id_access_token']);
    $ls_template     = $bd->bd_escapeCadena($_POST['id_nombre_template']);
    $ls_idioma       = $bd->bd_escapeCadena($_POST['id_idioma_template']);
    $li_horas        = $bd->bd_escapeCadena($_POST['id_horas_anticipacion']);
    $ls_flag_activo  = $bd->bd_escapeCadena($_POST['id_flag_activo']);

    $array_campo_pk = array('V_ID');
    $array_valor_pk = array('1');

    $array_campo = array(
        'V_PHONE_NUMBER_ID',
        'V_ACCESS_TOKEN',
        'V_NOMBRE_TEMPLATE',
        'V_IDIOMA_TEMPLATE',
        'N_HORAS_ANTICIPACION',
        'V_FLAG_ACTIVO',
        'V_AUD_USR_MOD',
        'D_AUD_FEC_MOD'
    );

    $array_valor = array(
        $ls_phone_id,
        $ls_token,
        $ls_template,
        $ls_idioma,
        $li_horas,
        $ls_flag_activo,
        $_SESSION['usr_conectado'],
        date('Y-m-d H:i:s')
    );

    $lb_result = $crud->fila_actualizar(DEF_TABLA_CONFIG_WHATSAPP, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);

    if ($lb_result == true) {
        header("Location: ../vista/mae_configwhatsapp_editar.php?id_msgRpta=" . urlencode('OK - Configuración guardada correctamente.'));
    } else {
        header("Location: ../vista/mae_configwhatsapp_editar.php?id_msgRpta=" . urlencode('No se pudo guardar la configuración.'));
    }
    exit;

}

?>
