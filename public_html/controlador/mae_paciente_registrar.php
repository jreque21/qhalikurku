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
$lb_result = true;

// Validar ingreso de datos
if (isset($_POST) && !empty($_POST)) {

    // ======================================= CAPTURA DE DATOS ======================================= //

    $ls_nombres          = $bd->bd_escapeCadena($_POST['id_nombres']);
    $ls_ape_paterno      = $bd->bd_escapeCadena($_POST['id_ape_paterno']);
    $ls_ape_materno      = $bd->bd_escapeCadena($_POST['id_ape_materno']);
    $ls_fg_sexo          = $bd->bd_escapeCadena($_POST['id_fg_sexo']);
    $ld_fec_nacimiento   = $bd->bd_escapeCadena($_POST['id_fec_nacimiento']);

    $li_tipo_doc         = $bd->bd_escapeCadena($_POST['id_tipo_doc']);
    $ls_nro_doc          = $bd->bd_escapeCadena($_POST['id_nro_doc']);

    $li_cod_pais         = $bd->bd_escapeCadena($_POST['id_cod_pais']);
    $li_cod_departamento = $bd->bd_escapeCadena($_POST['id_cod_departamento']);
    $li_cod_provincia    = $bd->bd_escapeCadena($_POST['id_cod_provincia']);
    $li_cod_distrito     = $bd->bd_escapeCadena($_POST['id_cod_distrito']);

    $ls_direccion        = $bd->bd_escapeCadena($_POST['id_direccion']);
    $ls_referencia       = $bd->bd_escapeCadena($_POST['id_referencia']);

    $ls_email            = $bd->bd_escapeCadena($_POST['id_email']);
    $ls_fono             = $bd->bd_escapeCadena($_POST['id_fono']);
    $ls_movil            = $bd->bd_escapeCadena($_POST['id_movil']);

    $ld_fec_alta         = $bd->bd_escapeCadena($_POST['id_fec_alta']);
    $ld_fec_baja         = $bd->bd_escapeCadena($_POST['id_fec_baja']);
    $ls_motivo_baja      = $bd->bd_escapeCadena($_POST['id_motivo_baja']);

    $ls_imagen           = isset($_FILES['archivo']['name']) ? $_FILES['archivo']['name'] : '';

    $ls_flag_estado      = $bd->bd_escapeCadena($_POST['id_flag_estado']);

    // Gestionar estado
    if ($ls_flag_estado != '1') {
        $ls_flag_estado = '0';
    }

    // Fecha de registro
    $ldt_fecha_insercion = date('Y-m-d H:i:s');

    // ==================================== VALIDACIONES SERVIDOR ==================================== //

    // Validar documento duplicado
    $array_campo_pk = array(
        'N_COD_TIPODOC',
        'V_NRO_DOC'
    );

    $array_valor_pk = array(
        $li_tipo_doc,
        $ls_nro_doc
    );

    $li_cuenta = $crud->fila_contar(
                    DEF_TABLA_PACIENTE,
                    $array_campo_pk,
                    $array_valor_pk
                 );

    if ($li_cuenta > 0) {

        $ls_mensaje = 'Paciente con Documento '.$ls_nro_doc.' ya existe.';
        $lb_result  = false;
    }

    // ================================= FIN VALIDACIONES SERVIDOR =================================== //


    // ======================================= UPLOAD FOTO =========================================== //

    if ($lb_result == true && !empty($ls_imagen)) {

        $ls_imagen = f_upload_archivo(
                        $_FILES["archivo"],
                        DEF_UPLOAD_PACIENTE_DIR,
                        "C",
                        DEF_UPLOAD_PACIENTE_W,
                        DEF_UPLOAD_PACIENTE_H
                     );
    }

    // ===================================== FIN UPLOAD FOTO ========================================= //


    // ====================================== CRUD REGISTRAR ========================================= //

    if ($lb_result == true) {

        $array_campo = array(
            'V_NOMBRES',
            'V_APE_PATERNO',
            'V_APE_MATERNO',
            'V_FG_SEXO',
            'D_FEC_NACIMIENTO',
            'N_COD_TIPODOC',
            'V_NRO_DOC',
            'N_COD_PAIS',
            'N_COD_DEPARTAMENTO',
            'N_COD_PROVINCIA',
            'N_COD_DISTRITO',
            'V_DIRECCION',
            'V_REFERENCIA',
            'V_EMAIL',
            'V_FONO',
            'V_MOVIL',
            'V_FOTO',
            'D_FEC_ALTA',
            'D_FEC_BAJA',
            'V_MOTIVO_BAJA',
            'V_FLAG_ESTADO',
            'V_AUD_USR_REG',
            'D_AUD_FEC_REG'
        );

        $array_valor = array(
            $ls_nombres,
            $ls_ape_paterno,
            $ls_ape_materno,
            $ls_fg_sexo,
            $ld_fec_nacimiento,
            $li_tipo_doc,
            $ls_nro_doc,
            $li_cod_pais,
            $li_cod_departamento,
            $li_cod_provincia,
            $li_cod_distrito,
            $ls_direccion,
            $ls_referencia,
            $ls_email,
            $ls_fono,
            $ls_movil,
            $ls_imagen,
            $ld_fec_alta,
            $ld_fec_baja,
            $ls_motivo_baja,
            $ls_flag_estado,
            $_SESSION['usr_conectado'],
            $ldt_fecha_insercion
        );

        // Registrar paciente
        $lb_result = $crud->fila_registrar(
                        DEF_TABLA_PACIENTE,
                        $array_campo,
                        $array_valor,
                        '0'
                     );

        // Obtener ID generado
        if ($lb_result == true) {

            $li_codigo = $crud->fila_recuperar_lastId(
                            DEF_TABLA_PACIENTE,
                            'N_COD_PACIENTE'
                         );
        }
    }

    // ==================================== FIN CRUD REGISTRAR ======================================= //


    // ======================================= REDIRECCION =========================================== //

    if ($lb_result == true) {

        header("Location: ../vista/mae_paciente_lista.php");

    } else {

        header("Location: ../vista/mae_paciente_nuevo.php?id_respuesta=".urlencode($ls_mensaje));
    }

}
?>