<?php
@session_start();

// Importar funcionalidades
require_once("../config/global.php");  
require_once(DEF_PATH_ADMIN);

// Instanciar clase de la B.D
$bd = new baseDatos();
$crud = new crud();

// Obtener datos iniciales
$ldt_fecha_actualizacion = date('Y-m-d h:i:s');
$lb_result = true;

// Validar ingreso de datos
if(isset($_GET['id_codigo'])){

	// Almacenar contenido con escape
	$li_codigo = $_GET["id_codigo"];

	// Definir estructura
	$array_campo_pk	= array('N_COD_HORARIO', 'V_FLAG_ESTADO');
	$array_valor_pk	= array($li_codigo, '1');

	// Invocar CRUD
	$li_contador = $crud->fila_contar(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk);
	if ( $li_contador > 0 ) {
		$ls_mensaje	= 'Horario no se encuentra aperturado.';
		header("Location: ../vista/mov_horario_lista.php?id_msgRpta=".$ls_mensaje);
		return;
	}

	// Definir estructura
	$array_campo_pk	= array('N_COD_HORARIO', 'V_FLAG_ESTADO');
	$array_valor_pk	= array($li_codigo, '3');

	// Invocar CRUD
	$li_contador = $crud->fila_contar(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk);
	if ( $li_contador > 0 ) {
		$ls_mensaje	= 'Horario ya se encuentra cerrado.';
		header("Location: ../vista/mov_horario_lista.php?id_msgRpta=".$ls_mensaje);
		return;
	}

	// Definir estructura
	$array_campo_pk	= array('N_COD_HORARIO', 'V_FLAG_ESTADO');
	$array_valor_pk	= array($li_codigo, '2');

	// Invocar CRUD
	$li_contador = $crud->fila_contar(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk);
	if ( $li_contador == 0 ) {
		$ls_mensaje	= 'Horario no se encuentra aperturado.';
		header("Location: ../vista/mov_horario_lista.php?id_msgRpta=".$ls_mensaje);
		return;
	}

	// ======================================= CRUD ACTUALIZAR =========================================== //
	
	// Definir estructura
	$array_campo_pk	= array('N_COD_HORARIO');
	$array_valor_pk	= array($li_codigo);
	$array_campo	= array('V_FLAG_ESTADO', 'V_AUD_USR_MOD', 'D_AUD_FEC_MOD');
	$array_valor	= array('3', $_SESSION['usr_conectado'], $ldt_fecha_actualizacion);
	
	// Invocar Actualización
	$lb_result = $crud->fila_actualizar(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);
	
	// ===================================== FIN CRUD ACTUALIZAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		$ls_mensaje	= 'OK-Horario se cerró de forma satisfactoria.';
		header("Location: ../vista/mov_horario_lista.php?id_msgRpta=".$ls_mensaje);
	}
	else {
		$ls_mensaje	= 'Inconvenientes al cerrar horario.';
		header("Location: ../vista/mov_horario_lista.php?id_msgRpta=".$ls_mensaje);
	}
	
}

?>
