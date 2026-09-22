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
	$ls_codigo		= $bd->bd_escapeCadena($_POST['id_codigo']);
	$ls_nombres		= $bd->bd_escapeCadena($_POST['id_nombres']);
	$ls_email		= $bd->bd_escapeCadena($_POST['id_email']);
	$ls_clave		= $bd->bd_escapeCadena($_POST['id_clave']);
	$ls_imagen		= $bd->bd_escapeCadena($_FILES['archivo']['name']);
	$ld_fec_alta	= $bd->bd_escapeCadena($_POST['id_fec_alta']);
	$ld_fec_baja	= $bd->bd_escapeCadena($_POST['id_fec_baja']);
	$ls_cod_tipo	= $bd->bd_escapeCadena($_POST['id_cod_tipo']);
	$li_cod_referencia= $bd->bd_escapeCadena($_POST['id_cod_referencia']);	
	$ls_observacion	= $bd->bd_escapeCadena($_POST['id_observacion']);
	$ls_flag_estado	= $bd->bd_escapeCadena($_POST['id_flag_estado']);
	
	// Gestionar estado
	if ($ls_flag_estado != '1') {
		$ls_flag_estado = '0';
	}
	
	// Cifrar clave
	if(!empty($ls_clave)){
		$ls_clave = md5( $ls_clave );
	}

	// Obtener datos iniciales
	$ldt_fecha_insercion = date('Y-m-d h:i:s');
	
	// ==================================== VALIDACIONES SERVIDOR ======================================== //
	// Validar ingreso de datos correctos
	$array_campo_pk	= array('V_COD_USER');
	$array_valor_pk	= array($ls_codigo);
	$li_cuenta = $crud->fila_contar(DEF_TABLA_USUARIO, $array_campo_pk, $array_valor_pk);
	
	// Código repetido
	if ($li_cuenta > 0) {
		$ls_mensaje = 'Usuario con código '.$ls_codigo.' se encuentra ocupado.';
		$lb_result  = false;
	}
	// =================================== FIN VALIDACIONES SERVIDOR ====================================== //
	
	// =======================================   UPLOAD    =========================================== //
	if ($lb_result == true && !empty($ls_imagen)) {
		// Upload de archivo
		$ls_imagen = f_upload_archivo($_FILES["archivo"], DEF_UPLOAD_USUARIO_DIR, "C", DEF_UPLOAD_USUARIO_W, DEF_UPLOAD_USUARIO_H);
	}
	// =======================================   FIN UPLOAD    =========================================== //

	// ======================================= CRUD REGISTRAR =========================================== //
	if ($lb_result == true) {
		// Definir estructura
		$array_campo	= array('V_COD_USER', 'V_NOMBRES', 'V_EMAIL', 'V_CLAVE', 'V_FOTO', 'D_FEC_ALTA', 'D_FEC_BAJA', 'V_COD_TIPO', 'N_COD_REFERENCIA', 'V_OBSERVACION', 'V_FLAG_ESTADO', 'V_AUD_USR_REG', 'D_AUD_FEC_REG');
		$array_valor	= array($ls_codigo, $ls_nombres, $ls_email, $ls_clave, $ls_imagen, $ld_fec_alta, $ld_fec_baja, $ls_cod_tipo, $li_cod_referencia, $ls_observacion,  $ls_flag_estado, $_SESSION['usr_conectado'], $ldt_fecha_insercion);
		
		// Invocar inserción
		$lb_result = $crud->fila_registrar(DEF_TABLA_USUARIO, $array_campo, $array_valor, '0');

	}
	// ===================================== FIN CRUD REGISTRAR ========================================= //

	// Redireccionar
	if($lb_result == true) {
		header("Location: ../vista/mae_usuario_lista.php");
	}
	else {
		header("Location: ../vista/mae_usuario_nuevo.php?id_respuesta=".$ls_mensaje);
	}
					
}

?>