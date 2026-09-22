<?php
@session_start();

// Importar funcionalidades
require_once("../config/global.php");  
require_once(DEF_PATH_ADMIN);

// Instanciar clase de la B.D
$bd = new baseDatos();
$crud = new crud();

// Inicializar variable
$lb_result  = true;

// Validar ingreso de datos
if(isset($_POST) && !empty($_POST)){
	
	// Almacenar contenido con escape
	$ls_descripcion	= $bd->bd_escapeCadena($_POST['id_descripcion']);
	$ls_lugar		= $bd->bd_escapeCadena($_POST['id_lugar']);
	$ls_referencia	= $bd->bd_escapeCadena($_POST['id_referencia']);
	$ld_fecha		= $bd->bd_escapeCadena($_POST['id_fecha']);
	$ld_hora		= $bd->bd_escapeCadena($_POST['id_hora']);
	$ls_observacion	= $bd->bd_escapeCadena($_POST['id_observacion']);
	$ls_flag_estado	= $bd->bd_escapeCadena($_POST['id_flag_estado']);
	
	// Gestionar estado
	if ($ls_flag_estado != '1') {
		$ls_flag_estado = '0';
	}
	
	// Obtener datos iniciales
	$ldt_fecha_insercion = date('Y-m-d h:i:s');
	
	// ==================================== VALIDACIONES SERVIDOR ======================================== //
	// Validar ingreso de datos correctos
	$array_campo_pk	= array('V_DESCRIPCION');
	$array_valor_pk	= array($ls_descripcion);
	$li_cuenta = $crud->fila_contar(DEF_TABLA_EVENTO, $array_campo_pk, $array_valor_pk);
	
	// Código repetido
	if ($li_cuenta > 0) {
		$ls_mensaje = 'Evento '.$ls_descripcion.' se encuentra ocupado.';
		$lb_result  = false;
	}
	// =================================== FIN VALIDACIONES SERVIDOR ====================================== //
	
	// ======================================= CRUD REGISTRAR =========================================== //
	if ($lb_result == true) {

		// Definir estructura
		$array_campo	= array('V_DESCRIPCION', 'V_LUGAR', 'V_REFERENCIA', 'D_FECHA', 'D_HORA', 'V_OBSERVACION', 'V_FLAG_ESTADO', 'V_AUD_USR_REG', 'D_AUD_FEC_REG');
		$array_valor	= array($ls_descripcion, $ls_lugar, $ls_referencia, $ld_fecha, $ld_hora, $ls_observacion, $ls_flag_estado, $_SESSION['usr_conectado'], $ldt_fecha_insercion);
		
		// Invocar inserción
		$lb_result = $crud->fila_registrar(DEF_TABLA_EVENTO, $array_campo, $array_valor, '0');

	}
	// ===================================== FIN CRUD REGISTRAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		header("Location: ../vista/mov_evento_lista.php");
	}
	else {
		header("Location: ../vista/mov_evento_nuevo.php?id_respuesta=".$ls_mensaje);
	}
					
}

?>