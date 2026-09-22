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

// Obtener datos iniciales
$ldt_fecha_actualizacion = date('Y-m-d H:i:s');
$lb_result = true;

// Validar ingreso de datos
if (isset($_GET['id_codigo'])) {

    // Código del paciente
    $li_codigo = $bd->bd_escapeCadena($_GET["id_codigo"]);

    // ================================= VALIDAR USUARIO EXISTENTE ================================ //

    $array_campo_pk = array(
        'V_COD_TIPO',
        'N_COD_REFERENCIA',
        'V_FLAG_ESTADO'
    );

    $array_valor_pk = array(
        DEF_TIPOUSER_PACIENTE,
        $li_codigo,
        '1'
    );

    $li_contador = $crud->fila_contar(
                        DEF_TABLA_USUARIO,
                        $array_campo_pk,
                        $array_valor_pk
                   );

    if ($li_contador > 0) {

        $ls_mensaje = 'Paciente ya tiene registrado un usuario.';

        header(
            "Location: ../vista/mae_paciente_lista.php?id_msgRpta="
            . urlencode($ls_mensaje)
        );

        return;
    }

    // ================================= OBTENER CLAVE INICIAL ==================================== //

    $array_campo_pk = array(
        'N_COD_PACIENTE',
        'V_FLAG_ESTADO'
    );

    $array_valor_pk = array(
        $li_codigo,
        '1'
    );

    // La contraseña inicial será el número de documento
    $ls_clave = $crud->fila_recuperar_campo(
                    DEF_TABLA_PACIENTE,
                    $array_campo_pk,
                    $array_valor_pk,
                    'V_NRO_DOC'
                );

    if (!empty($ls_clave)) {
        $ls_clave = md5($ls_clave);
    }

    // ================================= GENERAR USUARIO =========================================== //

    $ls_user = $_SESSION['usr_conectado'];

    $lb_result = $crud->ejecutar_sp(
        "CALL sp_generar_usuario(
            'PACIENTE',
            '$li_codigo',
            '$ls_clave',
            '$ls_user'
        )"
    );

    // ================================= REDIRECCION =============================================== //

    if ($lb_result == true) {

        $ls_mensaje = 'OK-Usuario generado de forma satisfactoria.';

        header(
            "Location: ../vista/mae_paciente_lista.php?id_msgRpta="
            . urlencode($ls_mensaje)
        );

    } else {

        $ls_mensaje = 'Inconvenientes al generar usuario de paciente.';

        header(
            "Location: ../vista/mae_paciente_lista.php?id_msgRpta="
            . urlencode($ls_mensaje)
        );
    }

    exit;
}
?>