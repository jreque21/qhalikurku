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
	$array_item			= $_POST['id_item'];
	$array_nota			= $_POST['id_nota'];
	$array_observacion	= $_POST['id_observacion'];

	// Capturar Código de Horario
	$array_campo_pk = array('N_COD_EVALUACION');
	$array_valor_pk = array($li_codigo_padre);
	$li_cod_horario	= $crud->fila_recuperar_campo(DEF_TABLA_EVALUACION, $array_campo_pk, $array_valor_pk, 'N_COD_HORARIO');
	$li_cod_examen	= $crud->fila_recuperar_campo(DEF_TABLA_EVALUACION, $array_campo_pk, $array_valor_pk, 'N_COD_EXAMEN');

	// ======================================= CRUD ACTUALIZAR =========================================== //
	$li_total = 0;
	$li_peso_total  = 0;

	// Bucle de registros
	for ($li_x = 0; $li_x < count($array_item); $li_x++){

		// Capturar Valores
		$li_item 	= $array_item[$li_x];
		$li_nota    = $array_nota[$li_x];
		$ls_obs     = $array_observacion[$li_x];
		
		// Capturar Datos de Exámen
		$array_campo_pk = array('N_COD_EXAMEN', 'N_ITEM');
		$array_valor_pk = array($li_cod_examen, $li_item);
		$li_peso		= $crud->fila_recuperar_campo(DEF_TABLA_EXAMENDET, $array_campo_pk, $array_valor_pk, 'N_PESO');

		// Calcular SubTotal
		$li_peso_total 	= $li_peso_total + $li_peso;
		$li_subtotal 	= $li_nota * $li_peso;
		$li_total		= $li_total + $li_subtotal;

		// Definir estructura
		$array_campo_pk	= array('N_COD_EVALUACION', 'N_ITEM');
		$array_valor_pk	= array($li_codigo_padre, $li_item);
		$array_campo	= array('N_NOTA', 'V_OBSERVACION', 'V_AUD_USR_MOD', 'D_AUD_FEC_MOD');
		$array_valor	= array($li_nota, $ls_obs , $_SESSION['usr_conectado'], $ldt_fecha_actualizacion);
		
		// Invocar Actualización
		$lb_result = $crud->fila_actualizar(DEF_TABLA_EVALUACIONDET, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);
		
		// Salir de bucle si hubo error
		if ($lb_result == false){
			break;
		}

	}
	
	// Total
	$li_total = $li_total / $li_peso_total;

	// Definir estructura
	$array_campo_pk	= array('N_COD_EVALUACION');
	$array_valor_pk	= array($li_codigo_padre);
	$array_campo	= array('N_NOTA', 'V_AUD_USR_MOD', 'D_AUD_FEC_MOD');
	$array_valor	= array($li_total, $_SESSION['usr_conectado'], $ldt_fecha_actualizacion);
	
	// Invocar Actualización
	$lb_result = $crud->fila_actualizar(DEF_TABLA_EVALUACION, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);

	// ===================================== FIN CRUD ACTUALIZAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		header("Location: ../vista/mov_evaluacion_lista.php?id_codigo_padre=".$li_cod_horario);
	}
	else {
		header("Location: ../vista/mov_evaluaciondet_lista.php?id_codigo=".$_POST["id_codigo_padre"]);
	}
	
}

?>
