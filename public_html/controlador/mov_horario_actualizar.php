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
	$ls_descripcion		= $bd->bd_escapeCadena($_POST['id_descripcion']);
	$li_cod_sede		= $bd->bd_escapeCadena($_POST['id_cod_sede']);
	$li_cod_instructor	= $bd->bd_escapeCadena($_POST['id_cod_instructor']);
	$li_cod_catturno	= $bd->bd_escapeCadena($_POST['id_cod_catturno']);
	$li_cod_tarifa		= $bd->bd_escapeCadena($_POST['id_cod_tarifa']);
	$ld_fec_inicio		= $bd->bd_escapeCadena($_POST['id_fec_inicio']);
	$ldt_hora_inicio	= $bd->bd_escapeCadena($_POST['id_hora_inicio']);
	$li_sesiones		= $bd->bd_escapeCadena($_POST['id_sesiones']);
	$li_horas			= $bd->bd_escapeCadena($_POST['id_horas']);
	$ls_flag_dia_1		= $bd->bd_escapeCadena($_POST['id_flag_dia_1']);
	$ls_flag_dia_2		= $bd->bd_escapeCadena($_POST['id_flag_dia_2']);
	$ls_flag_dia_3		= $bd->bd_escapeCadena($_POST['id_flag_dia_3']);
	$ls_flag_dia_4		= $bd->bd_escapeCadena($_POST['id_flag_dia_4']);
	$ls_flag_dia_5		= $bd->bd_escapeCadena($_POST['id_flag_dia_5']);
	$ls_flag_dia_6		= $bd->bd_escapeCadena($_POST['id_flag_dia_6']);
	$ls_flag_dia_7		= $bd->bd_escapeCadena($_POST['id_flag_dia_7']);
	$li_cod_cinturon	= $bd->bd_escapeCadena($_POST['id_cod_cinturon']);
	$li_cod_catestudiante= $bd->bd_escapeCadena($_POST['id_cod_catestudiante']);
	$li_estudiantes_max	= $bd->bd_escapeCadena($_POST['id_estudiantes_max']);
	$li_estudiantes_min	= $bd->bd_escapeCadena($_POST['id_estudiantes_min']);
	$ls_observacion		= $bd->bd_escapeCadena($_POST['id_observacion']);	
	$ls_flag_estado		= $bd->bd_escapeCadena($_POST['id_flag_estado']);
	
	// Gestionar Checks
	if (!$ls_flag_dia_1){
		$ls_flag_dia_1 = '0';
	}
	if (!$ls_flag_dia_2){
		$ls_flag_dia_2 = '0';
	}
	if (!$ls_flag_dia_3){
		$ls_flag_dia_3 = '0';
	}
	if (!$ls_flag_dia_4){
		$ls_flag_dia_4 = '0';
	}
	if (!$ls_flag_dia_5){
		$ls_flag_dia_5 = '0';
	}
	if (!$ls_flag_dia_6){
		$ls_flag_dia_6 = '0';
	}
	if (!$ls_flag_dia_7){
		$ls_flag_dia_7 = '0';
	}	

	// Obtener datos iniciales
	$ldt_fecha_actualizacion = date('Y-m-d h:i:s');
	
	// ======================================= CRUD ACTUALIZAR =========================================== //
	
	// Definir estructura
	$array_campo_pk	= array('N_COD_HORARIO');
	$array_valor_pk	= array($li_codigo);
	$array_campo	= array('V_DESCRIPCION', 'N_COD_SEDE', 'N_COD_INSTRUCTOR','N_COD_CATURNO', 'N_COD_TARIFA',
							'D_FEC_INICIO', 'D_HORA_INICIO', 'N_SESIONES', 'N_HORAS', 'V_FLAG_DIA_1', 'V_FLAG_DIA_2',
							'V_FLAG_DIA_3', 'V_FLAG_DIA_4', 'V_FLAG_DIA_5', 'V_FLAG_DIA_6', 'V_FLAG_DIA_7',
							'N_COD_CINTURON', 'N_COD_CATESTUDIANTE', 'N_ESTUDIANTES_MIN', 'N_ESTUDIANTES_MAX',
							'V_OBSERVACION', 'V_FLAG_ESTADO', 'V_AUD_USR_MOD', 'D_AUD_FEC_MOD');
	$array_valor	= array($ls_descripcion, $li_cod_sede, $li_cod_instructor, $li_cod_catturno, $li_cod_tarifa, $ld_fec_inicio, $ldt_hora_inicio,
							$li_sesiones, $li_horas, $ls_flag_dia_1, $ls_flag_dia_2, $ls_flag_dia_3, $ls_flag_dia_4, $ls_flag_dia_5, $ls_flag_dia_6, $ls_flag_dia_7,
							$li_cod_cinturon, $li_cod_catestudiante, $li_estudiantes_max, $li_estudiantes_min, $ls_observacion, $ls_flag_estado, $_SESSION['usr_conectado'], $ldt_fecha_actualizacion);
	
	// Invocar Actualización
	$lb_result = $crud->fila_actualizar(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);
	
	// ===================================== FIN CRUD ACTUALIZAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		header("Location: ../vista/mov_horario_lista.php");
	}
	else {
		header("Location: ../vista/mov_horario_editar.php?id_codigo=".$_POST["id_codigo"]);
	}
	
}

?>
