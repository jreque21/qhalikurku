<?php
@session_start();

// Importar funcionalidades
require_once("../config/global.php");
require_once(DEF_PATH_ADMIN);

// Controlar sesion activa
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    http_response_code(401);
    exit;
}

// Instanciar clase de la B.D
$bd   = new baseDatos();
$crud = new crud();

// Capturar departamento recibido
$li_cod_departamento = isset($_POST['id_cod_departamento'])
                        ? $bd->bd_escapeCadena($_POST['id_cod_departamento'])
                        : '';

// Opción por defecto
echo "<option value=''>Seleccione opción</option>";

if (!empty($li_cod_departamento)) {

    // Definir estructura
    $array_campo_pk = array('N_COD_DEPARTAMENTO', 'V_FLAG_ESTADO');
    $array_valor_pk = array($li_cod_departamento, '1');

    // Listar provincias del departamento
    $array_provincia = $crud->fila_listar(
                            'MAE_PROVINCIA',
                            $array_campo_pk,
                            $array_valor_pk,
                            'V_DES_CORTA',
                            'A',
                            0,
                            999
                        );

    if ($array_provincia) {
        while ($row = mysqli_fetch_assoc($array_provincia)) {
            echo "<option value=\"" . $row['N_COD_PROVINCIA'] . "\">" . $row['V_DES_CORTA'] . "</option>";
        }
    }
}

?>
