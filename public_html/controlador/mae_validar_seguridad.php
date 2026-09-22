<?php
@session_start();

// Importar funcionalidades
require_once("../config/global.php");  
require_once(DEF_PATH_ADMIN);

// Instanciar clase de la B.D
$bd = new baseDatos();
$crud = new crud();

// Validar ingreso de datos
if(isset($_POST) && !empty($_POST)){
	
	// Almacenar contenido con escape
	$ls_clave	= $bd->bd_escapeCadena($_POST['id_clave']);
	$ls_url		= $bd->bd_escapeCadena($_POST['id_url']);
	
	// Capturar Clave de Seguridad en B.D
	$array_campo_pk	= array('V_ID');
	$array_valor_pk	= array('1');
	$ls_clave_bd    = $crud->fila_recuperar_campo('MAE_EMPRESA', $array_campo_pk, $array_valor_pk, 'V_CLAVE_MASTER');
	
	// Distinto a Usuario Admweb
	if ($_SESSION['usr_conectado'] != 'admweb'){
		// Clave de seguridad correcta
		if ($ls_clave != $ls_clave_bd) {
			header("Location: ../vista/panel_admin.php");
			return;
		}	
	}
	
	// Redireccionar
	header("Location: ../vista/".$ls_url);
	
}

?>
