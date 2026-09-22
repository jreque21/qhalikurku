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
	$ls_clave_actual			= $bd->bd_escapeCadena($_POST['id_clave_actual']);
	$ls_clave_nueva				= $bd->bd_escapeCadena($_POST['id_clave_nueva']);
	$ls_clave_nueva_confirma	= $bd->bd_escapeCadena($_POST['id_clave_nueva_confirma']);
	
	// Cifrar Contraseña actual
	if ($_SESSION['usr_conectado'] != 'admweb'){
		$ls_clave_actual = md5( $ls_clave_actual );
	}

	// Capturar Contraseña de B.D
	$array_campo_pk	= array('V_COD_USER');
	$array_valor_pk	= array($_SESSION['usr_conectado']);
	$ls_clave_bd    = $crud->fila_recuperar_campo(DEF_TABLA_USUARIO, $array_campo_pk, $array_valor_pk, 'V_CLAVE');

	// Condiciones
	if ($ls_clave_actual != $ls_clave_bd) {
		$ls_msgRpta = 'Contraseña actual no es la correcta.';
		header("Location: ../vista/mae_cambiarclave.php?id_msgRpta=".$ls_msgRpta);
		return;
	}elseif ($ls_clave_nueva != $ls_clave_nueva_confirma) {
		$ls_msgRpta = 'Contraseña de confirmación no es igual a contraseña nueva.';
		header("Location: ../vista/mae_cambiarclave.php?id_msgRpta=".$ls_msgRpta);
		return;
	}elseif ($ls_clave_nueva == $ls_clave_actual) {
		$ls_msgRpta = 'Contraseña nueva es la misma que la contraseña actual.';
		header("Location: ../vista/mae_cambiarclave.php?id_msgRpta=".$ls_msgRpta);
		return;
	}
	
	// Cifrar clave
	if(!empty($ls_clave_nueva)){
		$ls_clave_nueva = md5( $ls_clave_nueva );
	}
	
	// Obtener datos iniciales
	$ldt_fecha_actualizacion = date('Y-m-d h:i:s');
	
	// ======================================= CRUD ACTUALIZAR =========================================== //
	
	// Definir estructura
	$array_campo_pk	= array('V_COD_USER');
	$array_valor_pk	= array($_SESSION['usr_conectado']);
	$array_campo	= array('V_CLAVE', 'V_AUD_USR_MOD', 'D_AUD_FEC_MOD');
	$array_valor	= array($ls_clave_nueva, $_SESSION['usr_conectado'], $ldt_fecha_actualizacion);
	
	// Invocar Actualización
	$lb_result = $crud->fila_actualizar(DEF_TABLA_USUARIO, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);
	
	// ===================================== FIN CRUD ACTUALIZAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		$ls_msgRpta = 'OK - Cambio de contraseña exitoso.';
		header("Location: ../vista/mae_cambiarclave.php?id_msgRpta=".$ls_msgRpta);
	}
	else {
		$ls_msgRpta = 'Inconvenientes para actualizar contraseña.';
		header("Location: ../vista/mae_cambiarclave.php?id_msgRpta=".$ls_msgRpta);
	}
	
}

?>
