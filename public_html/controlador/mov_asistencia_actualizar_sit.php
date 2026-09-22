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
if(isset($_POST) && !empty($_POST)){

	// Almacenar contenido con escape
	$li_codigo_horario		= $_POST['id_codigo_horario'];
	$li_codigo_clase		= $_POST['id_codigo_clase'];
	$li_codigo_estudiante	= $_POST['id_codigo_estudiante'];
	$ls_tipoasist			= $_POST['id_tipoasist'];

	// ======================================= CRUD ACTUALIZAR =========================================== //

	// Definir estructura
	$array_campo_pk	= array('N_COD_HORARIO', 'N_CLASE', 'N_COD_ESTUDIANTE');
	$array_valor_pk	= array($li_codigo_horario, $li_codigo_clase, $li_codigo_estudiante);
	$array_campo	= array('N_COD_TIPOASIST', 'V_AUD_USR_MOD', 'D_AUD_FEC_MOD');
	$array_valor	= array($ls_tipoasist, $_SESSION['usr_conectado'], $ldt_fecha_actualizacion);
	
	// Invocar Actualización
	$lb_result = $crud->fila_actualizar(DEF_TABLA_ASISTENCIA, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);

	// ===================================== FIN CRUD ACTUALIZAR ========================================= //
	
	// Retornar
	if($lb_result == true) {
		echo '1';
	}
	else {
		echo '0';
	}
	
}

?>
