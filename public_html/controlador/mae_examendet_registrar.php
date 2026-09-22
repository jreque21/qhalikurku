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
	$li_cod_examen		= $bd->bd_escapeCadena($_POST['id_cod_examen_hide']);
	$li_item			= $bd->bd_escapeCadena($_POST['id_item_hide']);
	$ls_descripcion		= $bd->bd_escapeCadena($_POST['id_descripcion']);
	$li_peso			= $bd->bd_escapeCadena($_POST['id_peso']);
	$li_minimo			= $bd->bd_escapeCadena($_POST['id_minimo']);
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
	$array_campo_pk	= array('N_COD_EXAMEN', 'V_DESCRIPCION');
	$array_valor_pk	= array($li_cod_examen, $ls_descripcion);
	$li_cuenta = $crud->fila_contar(DEF_TABLA_EXAMENDET, $array_campo_pk, $array_valor_pk);
	
	// Código repetido
	if ($li_cuenta > 0) {
		$ls_mensaje = 'Item ya se encuentra registrado para estte Exámen.';
		$lb_result  = false;
	}
	// =================================== FIN VALIDACIONES SERVIDOR ====================================== //
	
	// ======================================= CRUD REGISTRAR =========================================== //
	if ($lb_result == true) {

		// Definir estructura
		$array_campo	= array('N_COD_EXAMEN', 'N_ITEM', 'V_DESCRIPCION', 'N_PESO', 'N_MINIMO', 
								'V_OBSERVACION', 'V_FLAG_ESTADO', 'V_AUD_USR_REG', 'D_AUD_FEC_REG');
		$array_valor	= array($li_cod_examen, $li_item, $ls_descripcion, $li_peso, $li_minimo,
								$ls_observacion, $ls_flag_estado, $_SESSION['usr_conectado'], $ldt_fecha_insercion);
		
		// Invocar inserción
		$lb_result = $crud->fila_registrar(DEF_TABLA_EXAMENDET, $array_campo, $array_valor, '0');

	}
	// ===================================== FIN CRUD REGISTRAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		header("Location: ../vista/mae_examendet_lista.php?id_codigo_padre=".$li_cod_examen); 
	}
	else {
		header("Location: ../vista/mae_examendet_nuevo.php?id_codigo_padre=".$li_cod_examen."&id_respuesta='".$ls_mensaje."'"); 
	}
					
}

?>