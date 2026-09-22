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
if(isset($_GET['id_codigo_padre'])){

	// Almacenar contenido con escape
	$li_codigo = $_GET["id_codigo_padre"];

	// Definir estructura
	$array_campo_pk	= array('N_COD_HORARIO', 'V_FLAG_ESTADO');
	$array_valor_pk	= array($li_codigo, '1');

	// Invocar CRUD
	$li_contador = $crud->fila_contar(DEF_TABLA_HORARIOPROG, $array_campo_pk, $array_valor_pk);
	if ( $li_contador > 0 ) {
		$ls_mensaje	= 'Horario ya tiene registrado clases.';
		header("Location: ../vista/mov_horarioprog_lista.php?id_msgRpta=".$ls_mensaje);
		return;
	}

	// ======================================= CRUD PROCESAR =========================================== //

	// Capturar User
	$ls_user = $_SESSION['usr_conectado'];

	// Invocar SP para poblar tabla detalle
	$lb_result = $crud->ejecutar_sp("call sp_generar_progclases('$li_codigo','$ls_user')");

	// ===================================== FIN CRUD PROCESAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		$ls_mensaje	= 'OK-Programación de Clases generada de forma satisfactoria.';
		header("Location: ../vista/mov_horarioprog_lista.php?id_msgRpta=".$ls_mensaje);
	}
	else {
		$ls_mensaje	= 'Inconvenientes al generar programación de clases.';
		header("Location: ../vista/mov_horarioprog_lista.php?id_msgRpta=".$ls_mensaje);
	}
	
}

?>
