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

// Capturar país recibido
$li_cod_pais = isset($_POST['id_cod_pais'])
                ? $bd->bd_escapeCadena($_POST['id_cod_pais'])
                : '';

// Opción por defecto
echo "<option value=''>Seleccione opción</option>";

if (!empty($li_cod_pais)) {

    // Definir estructura
    $array_campo_pk = array('N_COD_PAIS', 'V_FLAG_ESTADO');
    $array_valor_pk = array($li_cod_pais, '1');

    // Listar departamentos del país
    $array_departamento = $crud->fila_listar(
                                'MAE_DEPARTAMENTO',
                                $array_campo_pk,
                                $array_valor_pk,
                                'V_DES_CORTA',
                                'A',
                                0,
                                999
                            );

    if ($array_departamento) {
        while ($row = mysqli_fetch_assoc($array_departamento)) {
            echo "<option value=\"" . $row['N_COD_DEPARTAMENTO'] . "\">" . $row['V_DES_CORTA'] . "</option>";
        }
    }
}

?>
