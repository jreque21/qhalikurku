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
	$li_codigo		= $bd->bd_escapeCadena($_POST['id_codigo']);
	$ls_nombre		= $bd->bd_escapeCadena($_POST['id_nombre']);
	$ls_direccion	= $bd->bd_escapeCadena($_POST['id_direccion']);
	$ls_referencia	= $bd->bd_escapeCadena($_POST['id_referencia']);
	$ls_email		= $bd->bd_escapeCadena($_POST['id_email']);
	$ls_fono		= $bd->bd_escapeCadena($_POST['id_fono']);
	$ls_movil		= $bd->bd_escapeCadena($_POST['id_movil']);
	$ls_imagen		= $bd->bd_escapeCadena($_FILES['archivo']['name']);
	$ls_flag_estado	= $bd->bd_escapeCadena($_POST['id_flag_estado']);
	
	// Gestionar estado
	if ($ls_flag_estado != '1') {
		$ls_flag_estado = '0';
	}
	
	// Obtener datos iniciales
	$ldt_fecha_actualizacion = date('Y-m-d h:i:s');
	
	// =======================================   UPLOAD    =========================================== //
	// Gestionar upload de archivos
	if (!empty($ls_imagen)){
				
		// Definir estructura
		$array_campo_pk	= array(N_COD_SEDE);
		$array_valor_pk	= array($li_codigo);
	
		// Recupera imagen a eliminar
		$ls_imagen_delete = $crud->fila_recuperar_campo(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_FOTO');
		
		// Borrar archivo fisicamente
		if (!empty($ls_imagen_delete)) {
			
			// Preparar directorio
			$ls_imagen_delete = "../../upload/".DEF_UPLOAD_SEDE_DIR."/".$ls_imagen_delete;
			
			// Eliminar archivo
			if (file_exists($ls_imagen_delete)){
				unlink($ls_imagen_delete);
			}
			
		}	
		
		// Upload de archivo
		$ls_imagen = f_upload_archivo($_FILES["archivo"], DEF_UPLOAD_SEDE_DIR, "C", DEF_UPLOAD_SEDE_W, DEF_UPLOAD_SEDE_H);
		
	}else{

	    // Definir estructura
		$array_campo_pk	= array('N_COD_SEDE');
		$array_valor_pk	= array($li_codigo);

	    // Recupera imagen original
		$ls_imagen = $crud->fila_recuperar_campo(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_FOTO');

	}
	// =======================================   FIN UPLOAD    =========================================== //

	// ======================================= CRUD ACTUALIZAR =========================================== //
	
	// Definir estructura
	$array_campo_pk	= array('N_COD_SEDE');
	$array_valor_pk	= array($li_codigo);
	$array_campo	= array('V_NOMBRE', 'V_DIRECCION', 'V_REFERENCIA', 'V_EMAIL', 'V_FONO', 'V_MOVIL', 'V_FOTO', 'V_FLAG_ESTADO', 'V_AUD_USR_MOD', 'D_AUD_FEC_MOD');
	$array_valor	= array($ls_nombre, $ls_direccion, $ls_referencia, $ls_email, $ls_fono, $ls_movil, $ls_imagen, $ls_flag_estado, $_SESSION['usr_conectado'], $ldt_fecha_actualizacion);
	
	// Invocar Actualización
	$lb_result = $crud->fila_actualizar(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);
	
	// ===================================== FIN CRUD ACTUALIZAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		header("Location: ../vista/mae_sede_lista.php");
	}
	else {
		header("Location: ../vista/mae_sede_editar.php?id_codigo=".$_POST["id_codigo"]);
	}
	
}

?>
