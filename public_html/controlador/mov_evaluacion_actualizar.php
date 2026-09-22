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
	$li_codigo			= $bd->bd_escapeCadena($_POST['id_codigo']);
	$li_cod_horario		= $bd->bd_escapeCadena($_POST['id_cod_horario_hide']);
	$ld_fecha			= $bd->bd_escapeCadena($_POST['id_fecha']);
	$ls_cod_evaluador	= $bd->bd_escapeCadena($_POST['id_cod_evaluador']);
	$ls_observacion		= $bd->bd_escapeCadena($_POST['id_observacion']);
	$ls_flag_estado		= $bd->bd_escapeCadena($_POST['id_flag_estado']);

	// Gestionar estado
	if ($ls_flag_estado != '1') {
		$ls_flag_estado = '0';
	}
	
	// Obtener datos iniciales
	$ldt_fecha_actualizacion = date('Y-m-d h:i:s');

	// ======================================= CRUD ACTUALIZAR =========================================== //
	
	// Definir estructura
	$array_campo_pk	= array('N_COD_EVALUACION');
	$array_valor_pk	= array($li_codigo);
	$array_campo	= array('D_FECHA', 'V_USR_EVAL', 'V_OBSERVACION', 'V_FLAG_ESTADO', 'V_AUD_USR_MOD', 'D_AUD_FEC_MOD');
	$array_valor	= array($ld_fecha, $ls_cod_evaluador, $ls_observacion, $ls_flag_estado, $_SESSION['usr_conectado'], $ldt_fecha_actualizacion);
	
	// Invocar Actualización
	$lb_result = $crud->fila_actualizar(DEF_TABLA_EVALUACION, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);
	
	// ===================================== FIN CRUD ACTUALIZAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		header("Location: ../vista/mov_evaluacion_lista.php?id_codigo_padre=".$li_cod_horario); 
	}
	else {
		header("Location: ../vista/mov_evaluacion_editar.php?id_codigo=".$_POST["id_codigo"]);
	}
	
}

?>
