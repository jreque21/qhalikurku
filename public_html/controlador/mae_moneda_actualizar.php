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
	$li_codigo		= $bd->bd_escapeCadena($_POST['id_codigo']);
	$ls_des_larga	= $bd->bd_escapeCadena($_POST['id_des_larga']);
	$ls_des_corta	= $bd->bd_escapeCadena($_POST['id_des_corta']);
	$ls_simbolo		= $bd->bd_escapeCadena($_POST['id_simbolo']);
	$ls_flag_estado	= $bd->bd_escapeCadena($_POST['id_flag_estado']);
	
	// Gestionar estado
	if ($ls_flag_estado != '1') {
		$ls_flag_estado = '0';
	}
	
	// Obtener datos iniciales
	$ldt_fecha_actualizacion = date('Y-m-d h:i:s');
			
	// ======================================= CRUD ACTUALIZAR =========================================== //
	
	// Definir estructura
	$array_campo_pk	= array('N_COD_MONEDA');
	$array_valor_pk	= array($li_codigo);
	$array_campo	= array('V_DES_LARGA', 'V_DES_CORTA', 'V_SIMBOLO', 'V_FLAG_ESTADO', 'V_AUD_USR_MOD', 'D_AUD_FEC_MOD');
	$array_valor	= array($ls_des_larga, $ls_des_corta, $ls_simbolo, $ls_flag_estado, $_SESSION['usr_conectado'], $ldt_fecha_actualizacion);
	
	// Invocar Actualización
	$lb_result = $crud->fila_actualizar(DEF_TABLA_MONEDA, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);
	
	// ===================================== FIN CRUD ACTUALIZAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		header("Location: ../vista/mae_moneda_lista.php");
	}
	else {
		header("Location: ../vista/mae_moneda_editar.php?id_codigo=".$_POST["id_codigo"]);
	}
	
}

?>
