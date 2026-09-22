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
	$li_tipo_grado	= $bd->bd_escapeCadena($_POST['id_tipo_grado']);
	$ls_des_larga	= $bd->bd_escapeCadena($_POST['id_des_larga']);
	$ls_des_corta	= $bd->bd_escapeCadena($_POST['id_des_corta']);
	$ls_color		= $bd->bd_escapeCadena($_POST['id_color']);
	$ls_texto		= $bd->bd_escapeCadena($_POST['id_texto']);
	$li_orden		= $bd->bd_escapeCadena($_POST['id_orden']);
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
		$array_campo_pk	= array('N_COD_CINTURON');
		$array_valor_pk	= array($li_codigo);
	
		// Recupera imagen a eliminar
		$ls_imagen_delete = $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_FOTO');
		
		// Borrar archivo fisicamente
		if (!empty($ls_imagen_delete)) {
			
			// Preparar directorio
			$ls_imagen_delete = "../../upload/".DEF_UPLOAD_CINTURON_DIR."/".$ls_imagen_delete;
			
			// Eliminar archivo
			if (file_exists($ls_imagen_delete)){
				unlink($ls_imagen_delete);
			}
			
		}	
		
		// Upload de archivo
		$ls_imagen = f_upload_archivo($_FILES["archivo"], DEF_UPLOAD_CINTURON_DIR, "C", DEF_UPLOAD_CINTURON_W, DEF_UPLOAD_CINTURON_H);
		
	}else{

		// Definir estructura
		$array_campo_pk	= array('N_COD_CINTURON');
		$array_valor_pk	= array($li_codigo);
 
		// Recupera imagen original
		$ls_imagen = $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_FOTO');
 
	 }
	// =======================================   FIN UPLOAD    =========================================== //
	

	// ======================================= CRUD ACTUALIZAR =========================================== //
	
	// Definir estructura
	$array_campo_pk	= array('N_COD_CINTURON');
	$array_valor_pk	= array($li_codigo);
	$array_campo	= array('V_DES_LARGA', 'V_DES_CORTA', 'N_COD_TIPOGRADO', 'V_COD_COLOR', 'V_TEXTO', 'N_ORDEN', 'V_FOTO', 'V_FLAG_ESTADO', 'V_AUD_USR_MOD', 'D_AUD_FEC_MOD');
	$array_valor	= array($ls_des_larga, $ls_des_corta, $li_tipo_grado, $ls_color, $ls_texto, $li_orden, $ls_imagen, $ls_flag_estado, $_SESSION['usr_conectado'], $ldt_fecha_actualizacion);
	
	// Invocar Actualización
	$lb_result = $crud->fila_actualizar(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);
	
	// ===================================== FIN CRUD ACTUALIZAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		header("Location: ../vista/mae_cinturon_lista.php");
	}
	else {
		header("Location: ../vista/mae_cinturon_editar.php?id_codigo=".$_POST["id_codigo"]);
	}
	
}

?>
