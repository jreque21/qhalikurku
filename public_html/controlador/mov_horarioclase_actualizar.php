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
	$li_cod_horario 	= $bd->bd_escapeCadena($_POST['id_cod_horario_hide']);
	$li_clase			= $bd->bd_escapeCadena($_POST['id_clase_hide']);
	$li_cod_instructor	= $bd->bd_escapeCadena($_POST['id_cod_instructor']);
	$li_fec_prog		= $bd->bd_escapeCadena($_POST['id_fec_prog']);
	$li_hora_prog		= $bd->bd_escapeCadena($_POST['id_hora_prog']);
	$li_horas			= $bd->bd_escapeCadena($_POST['id_horas']);
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
	$array_campo_pk	= array('N_COD_HORARIO', 'N_CLASE');
	$array_valor_pk	= array($li_cod_horario, $li_clase);
	$array_campo	= array('N_COD_INSTRUCTOR', 'D_FEC_PROG', 'D_HORA_PROG', 
							'N_HORAS', 'V_OBSERVACION', 'V_FLAG_ESTADO', 'V_AUD_USR_MOD', 'D_AUD_FEC_MOD');
	$array_valor	= array($li_cod_instructor, $li_fec_prog, $li_hora_prog,
							$li_horas, $ls_observacion, $ls_flag_estado, $_SESSION['usr_conectado'], $ldt_fecha_actualizacion);
	
	// Invocar Actualización
	$lb_result = $crud->fila_actualizar(DEF_TABLA_HORARIOPROG, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);

	// ===================================== FIN CRUD ACTUALIZAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		header("Location: ../vista/mov_horarioclase_lista.php?id_codigo_padre=".$li_cod_horario); 
	}
	else {
		header("Location: ../vista/mov_horarioclase_editar.php?id_codigo=".$_POST["id_codigo"]);
	}
	
}

?>
