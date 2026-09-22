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
	$ls_codigo		= $bd->bd_escapeCadena($_POST['id_codigo']);
	$ls_nombres		= $bd->bd_escapeCadena($_POST['id_nombres']);
	$ls_email		= $bd->bd_escapeCadena($_POST['id_email']);
	$ls_clave		= $bd->bd_escapeCadena($_POST['id_clave']);
	$ls_imagen		= $bd->bd_escapeCadena($_FILES['archivo']['name']);
	$ld_fec_alta	= $bd->bd_escapeCadena($_POST['id_fec_alta']);
	$ld_fec_baja	= $bd->bd_escapeCadena($_POST['id_fec_baja']);	
	$ls_cod_tipo	= $bd->bd_escapeCadena($_POST['id_cod_tipo']);
	//$li_cod_referencia= $bd->bd_escapeCadena($_POST['id_cod_referencia']);	
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
	$ldt_fecha_actualizacion = date('Y-m-d h:i:s');
	
	// =======================================   UPLOAD    =========================================== //
	// Gestionar upload de archivos
	if (!empty($ls_imagen)){
				
		// Definir estructura
		$array_campo_pk	= array('V_COD_USER');
		$array_valor_pk	= array($ls_codigo);
	
		// Recupera imagen a eliminar
		$ls_imagen_delete = $crud->fila_recuperar_campo(DEF_TABLA_USUARIO, $array_campo_pk, $array_valor_pk, 'V_FOTO');
		
		// Borrar archivo fisicamente
		if (!empty($ls_imagen_delete)) {
			
			// Preparar directorio
			$ls_imagen_delete = "../../upload/".DEF_UPLOAD_USUARIO_DIR."/".$ls_imagen_delete;
			
			// Eliminar archivo
			if (file_exists($ls_imagen_delete)){
				unlink($ls_imagen_delete);
			}
			
		}	
		
		// Upload de archivo
		$ls_imagen = f_upload_archivo($_FILES["archivo"], DEF_UPLOAD_USUARIO_DIR, "C", DEF_UPLOAD_USUARIO_W, DEF_UPLOAD_USUARIO_H);
		
	}else{

	    // Definir estructura
		$array_campo_pk	= array('V_COD_USER');
		$array_valor_pk	= array($ls_codigo);

	    // Recupera imagen original
		$ls_imagen = $crud->fila_recuperar_campo(DEF_TABLA_USUARIO, $array_campo_pk, $array_valor_pk, 'V_FOTO');

	}

	// =======================================   FIN UPLOAD    =========================================== //

	// ======================================= CRUD ACTUALIZAR =========================================== //
	
	// Definir estructura
	$array_campo_pk	= array('V_COD_USER');
	$array_valor_pk	= array($ls_codigo);
	$array_campo	= array('V_NOMBRES', 'V_EMAIL', 'V_CLAVE', 'V_FOTO', 'D_FEC_ALTA', 'D_FEC_BAJA', 'V_COD_TIPO', 'V_OBSERVACION', 'V_FLAG_ESTADO', 'V_AUD_USR_MOD', 'D_AUD_FEC_MOD');
	$array_valor	= array($ls_nombres, $ls_email, $ls_clave, $ls_imagen, $ld_fec_alta, $ld_fec_baja, $ls_cod_tipo, $ls_observacion, $ls_flag_estado, $_SESSION['usr_conectado'], $ldt_fecha_actualizacion);
	
	// Invocar Actualización
	$lb_result = $crud->fila_actualizar(DEF_TABLA_USUARIO, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);
	
	// ===================================== FIN CRUD ACTUALIZAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		header("Location: ../vista/mae_usuario_lista.php");
	}
	else {
		header("Location: ../vista/mae_usuario_editar.php?id_codigo=".$_POST["id_codigo"]);
	}
	
}

?>
