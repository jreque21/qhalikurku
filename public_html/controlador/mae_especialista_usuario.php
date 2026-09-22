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
	$array_campo_pk	= array('V_COD_TIPO', 'N_COD_REFERENCIA', 'V_FLAG_ESTADO');
	$array_valor_pk	= array(DEF_TIPOUSER_ESP, $li_codigo, '1');

	// Invocar CRUD
	$li_contador = $crud->fila_contar(DEF_TABLA_USUARIO, $array_campo_pk, $array_valor_pk);
	if ( $li_contador > 0 ) {
		$ls_mensaje	= 'Instructor ya tiene registrado un usuario.';
		header("Location: ../vista/mae_especialista_lista.php?id_msgRpta=".$ls_mensaje);
		return;
	}

	// Definir estructura
	$array_campo_pk	= array('N_COD_ESPECIALISTA', 'V_FLAG_ESTADO');
	$array_valor_pk	= array($li_codigo, '1');
	$ls_clave		= $crud->fila_recuperar_campo(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_NRO_DOC');

	// Cifrar clave
	if(!empty($ls_clave)){
		$ls_clave = md5( $ls_clave );
	}
	
	// ======================================= CRUD PROCESAR =========================================== //

	// Capturar User
	$ls_user = $_SESSION['usr_conectado'];

	// Invocar SP para poblar tabla detalle
	$lb_result = $crud->ejecutar_sp("call sp_generar_usuario('USER_ESP','$li_codigo','$ls_clave','$ls_user')");
	
	// ===================================== FIN CRUD PROCESAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		$ls_mensaje	= 'OK-Usuario generado de forma satisfactoria.';
		header("Location: ../vista/mae_especialista_lista.php?id_msgRpta=".$ls_mensaje);
	}
	else {
		$ls_mensaje	= 'Inconvenientes al generar usuario de instructor.';
		header("Location: ../vista/mae_especialista_lista.php?id_msgRpta=".$ls_mensaje);
	}
	
}

?>
