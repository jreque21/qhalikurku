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
	$li_cod_horario		= $bd->bd_escapeCadena($_POST['id_cod_horario_hide']);
	$li_cod_instructor	= $bd->bd_escapeCadena($_POST['id_cod_instructor_hide']);
	$li_cod_estudiante	= $bd->bd_escapeCadena($_POST['id_cod_estudiante']);
	$li_cod_cinturon	= $bd->bd_escapeCadena($_POST['id_cod_cinturon_hide']);
	$ld_fecha			= $bd->bd_escapeCadena($_POST['id_fecha']);
	$li_cod_examen		= $bd->bd_escapeCadena($_POST['id_cod_examen']);
	$ls_cod_evaluador	= $bd->bd_escapeCadena($_POST['id_cod_evaluador']);
	$ls_observacion		= $bd->bd_escapeCadena($_POST['id_observacion']);
	$ls_flag_estado		= $bd->bd_escapeCadena($_POST['id_flag_estado']);
	
	// Gestionar estado
	if ($ls_flag_estado != '1') {
		$ls_flag_estado = '0';
	}

	// Obtener datos iniciales
	$ldt_fecha_insercion = date('Y-m-d h:i:s');
	
	// ==================================== VALIDACIONES SERVIDOR ======================================== //
	// Validar ingreso de datos correctos
	$array_campo_pk	= array('N_COD_HORARIO', 'N_COD_ESTUDIANTE');
	$array_valor_pk	= array($li_cod_horario, $li_cod_estudiante);
	$li_cuenta = $crud->fila_contar(DEF_TABLA_EVALUACION, $array_campo_pk, $array_valor_pk);
	
	// Código repetido
	if ($li_cuenta > 0) {
		$ls_mensaje = 'Estudiante ya se encuentra registrado para esta Evaluación.';
		$lb_result  = false;
	}
	// =================================== FIN VALIDACIONES SERVIDOR ====================================== //
	
	// ======================================= CRUD REGISTRAR =========================================== //
	if ($lb_result == true) {

		// Definir estructura
		$array_campo	= array('N_COD_EXAMEN', 'N_COD_HORARIO', 'N_COD_ESTUDIANTE', 'N_COD_CINTURON', 'N_COD_INSTRUCTOR', 'D_FECHA', 'V_USR_EVAL',
								'V_OBSERVACION', 'V_FLAG_ESTADO', 'V_AUD_USR_REG', 'D_AUD_FEC_REG');
		$array_valor	= array($li_cod_examen, $li_cod_horario, $li_cod_estudiante, $li_cod_cinturon, $li_cod_instructor,$ld_fecha, $ls_cod_evaluador,
								$ls_observacion, $ls_flag_estado, $_SESSION['usr_conectado'], $ldt_fecha_insercion);
		
		// Invocar inserción
		$lb_result = $crud->fila_registrar(DEF_TABLA_EVALUACION, $array_campo, $array_valor, '0');

		if($lb_result == true) {

			// Poblar Plantilla de Evaluación
			// Capturar ID Máximo
			$li_codigo = $crud->fila_recuperar_lastId(DEF_TABLA_EVALUACION, 'N_COD_EVALUACION');

			// Capturar User
			$ls_user = $_SESSION['usr_conectado'];
			
			// Invocar SP para poblar tabla detalle
			$lb_result = $crud->ejecutar_sp("call sp_generar_evaluacion_det('$li_codigo', '$li_cod_examen', '$ls_user')");
	
		}

	}
	// ===================================== FIN CRUD REGISTRAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		header("Location: ../vista/mov_evaluacion_lista.php?id_codigo_padre=".$li_cod_horario); 
	}
	else {
		header("Location: ../vista/mov_evaluacion_nuevo.php?id_codigo_padre=".$li_cod_horario."&id_respuesta='".$ls_mensaje."'"); 
	}
					
}

?>