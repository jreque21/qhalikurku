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
	$li_codigo_padre	= $_POST['id_codigo_padre'];
	$li_codigo			= $_POST['id_codigo'];
	$ls_marca			= $_POST['id_marca'];

	// ======================================= CRUD ACTUALIZAR =========================================== //

	// Definir estructura
	$array_campo_pk	= array('N_COD_PROMOCION', 'N_COD_ESTUDIANTE');
	$array_valor_pk	= array($li_codigo_padre, $li_codigo);
	$array_campo	= array('V_FLAG_APROB', 'V_AUD_USR_MOD', 'D_AUD_FEC_MOD');
	$array_valor	= array($ls_marca, $_SESSION['usr_conectado'], $ldt_fecha_actualizacion);
	
	// Recuperar nuevo cinturón
	$li_cod_cinturon= $crud->fila_recuperar_campo(DEF_TABLA_PROMOCIONPROG, $array_campo_pk, $array_valor_pk, 'N_COD_CINTURON_NUEVO');

	// Invocar Actualización
	$lb_result = $crud->fila_actualizar(DEF_TABLA_PROMOCIONPROG, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);

	if($lb_result == true) {

		// Poblar Histórico de Cinturón
		// Capturar User
		$ls_user = $_SESSION['usr_conectado'];
		
		// Invocar SP para poblar tabla detalle
		$lb_result = $crud->ejecutar_sp("call sp_generar_hist_cinturon('$li_codigo', '$li_cod_cinturon', '$ls_marca', '$ls_user')");

	}
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
