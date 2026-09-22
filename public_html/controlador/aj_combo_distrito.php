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

// Capturar provincia recibida
$li_cod_provincia = isset($_POST['id_cod_provincia'])
                     ? $bd->bd_escapeCadena($_POST['id_cod_provincia'])
                     : '';

// Opción por defecto
echo "<option value=''>Seleccione opción</option>";

if (!empty($li_cod_provincia)) {

    // Definir estructura
    $array_campo_pk = array('N_COD_PROVINCIA', 'V_FLAG_ESTADO');
    $array_valor_pk = array($li_cod_provincia, '1');

    // Listar distritos de la provincia
    $array_distrito = $crud->fila_listar(
                            'MAE_DISTRITO',
                            $array_campo_pk,
                            $array_valor_pk,
                            'V_DES_CORTA',
                            'A',
                            0,
                            999
                        );

    if ($array_distrito) {
        while ($row = mysqli_fetch_assoc($array_distrito)) {
            echo "<option value=\"" . $row['N_COD_DISTRITO'] . "\">" . $row['V_DES_CORTA'] . "</option>";
        }
    }
}

?>
