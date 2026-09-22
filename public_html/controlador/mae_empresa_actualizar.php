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
	$ls_descripcion	= $bd->bd_escapeCadena($_POST['id_descripcion']);
	$ls_responsable	= $bd->bd_escapeCadena($_POST['id_responsable']);
	$ls_direccion	= $bd->bd_escapeCadena($_POST['id_direccion']);
	$ls_referencia	= $bd->bd_escapeCadena($_POST['id_referencia']);
	$ls_lema		= $bd->bd_escapeCadena($_POST['id_lema']);
	$ls_email		= $bd->bd_escapeCadena($_POST['id_email']);
	$ls_fono		= $bd->bd_escapeCadena($_POST['id_fono']);
	$ls_movil		= $bd->bd_escapeCadena($_POST['id_movil']);
	$ls_url_web		= $bd->bd_escapeCadena($_POST['id_url_web']);
	$ls_url_fb		= $bd->bd_escapeCadena($_POST['id_url_fb']);
	$ls_url_yt		= $bd->bd_escapeCadena($_POST['id_url_yt']);
	$ls_url_tw		= $bd->bd_escapeCadena($_POST['id_url_tw']);
	$ls_url_ig		= $bd->bd_escapeCadena($_POST['id_url_ig']);
	$ls_url_tk		= $bd->bd_escapeCadena($_POST['id_url_tk']);
	$ls_nombre_soporte	= $bd->bd_escapeCadena($_POST['id_nombre_soporte']);
	$ls_fono_soporte= $bd->bd_escapeCadena($_POST['id_fono_soporte']);
	$ls_email_soporte = $bd->bd_escapeCadena($_POST['id_email_soporte']);
	$li_horas		= $bd->bd_escapeCadena($_POST['id_horas']);
	$li_clases		= $bd->bd_escapeCadena($_POST['id_clases']);
	$li_min			= $bd->bd_escapeCadena($_POST['id_estudiantes_min']);
	$li_max			= $bd->bd_escapeCadena($_POST['id_estudiantes_max']);
	$ls_bienvenida	= $bd->bd_escapeCadena($_POST['id_bienvenida']);
	$ls_somos		= $bd->bd_escapeCadena($_POST['id_somos']);
	$ls_mision		= $bd->bd_escapeCadena($_POST['id_mision']);
	$ls_vision		= $bd->bd_escapeCadena($_POST['id_vision']);
	$ls_imagen		= $bd->bd_escapeCadena($_FILES['archivo']['name']);
		
	// Obtener datos iniciales
	$ldt_fecha_actualizacion = date('Y-m-d h:i:s');
	
	// =======================================   UPLOAD    =========================================== //
	// Gestionar upload de archivos
	if (!empty($ls_imagen)){
				
		// Definir estructura
		$array_campo_pk	= array('V_ID');
		$array_valor_pk	= array($li_codigo);
	
		// Recupera imagen a eliminar
		$ls_imagen_delete = $crud->fila_recuperar_campo(DEF_TABLA_EMPRESA, $array_campo_pk, $array_valor_pk, 'V_FOTO');
		
		// Borrar archivo fisicamente
		if (!empty($ls_imagen_delete)) {
			
			// Preparar directorio
			$ls_imagen_delete = "../../upload/".DEF_UPLOAD_EMPRESA_DIR."/".$ls_imagen_delete;
			
			// Eliminar archivo
			if (file_exists($ls_imagen_delete)){
				unlink($ls_imagen_delete);
			}
			
		}	
		
		// Upload de archivo
		$ls_imagen = f_upload_archivo($_FILES["archivo"], DEF_UPLOAD_EMPRESA_DIR, "C", DEF_UPLOAD_EMPRESA_W, DEF_UPLOAD_EMPRESA_H);
		
	}else{

		// Definir estructura
		$array_campo_pk	= array('V_ID');
		$array_valor_pk	= array($ls_codigo);
 
		// Recupera imagen original
		$ls_imagen = $crud->fila_recuperar_campo(DEF_TABLA_EMPRESA, $array_campo_pk, $array_valor_pk, 'V_FOTO');
 
	 }
	// =======================================   FIN UPLOAD    =========================================== //
	

	// ======================================= CRUD ACTUALIZAR =========================================== //
	
	// Definir estructura
	$array_campo_pk	= array('V_ID');
	$array_valor_pk	= array($ls_codigo);
	$array_campo	= array('V_DESCRIPCION', 'V_RESPONSABLE', 'V_DIRECCION', 'V_REFERENCIA', 'V_LEMA', 'V_EMAIL', 
							'V_FONO', 'V_MOVIL', 'V_URL_WEB', 'V_URL_FB', 'V_URL_YT', 'V_URL_TW', 'V_URL_IG','V_URL_TK',
							'V_NOMBRE_SOPORTE', 'V_FONO_SOPORTE', 'V_EMAIL_SOPORTE', 'N_HORASXCLASE', 'N_CLASESXHORARIO', 'N_MIN_ESTUDIANTES','N_MAX_ESTUDIANTES',
							'V_BIENVENIDA', 'V_SOMOS', 'V_MISION', 'V_VISION',
							'V_FOTO', 'V_AUD_USR_MOD', 'D_AUD_FEC_MOD');
	$array_valor	= array($ls_descripcion, $ls_responsable, $ls_direccion, $ls_referencia, $ls_lema, $ls_email,
							$ls_fono, $ls_movil, $ls_url_web, $ls_url_fb, $ls_url_yt, $ls_url_tw, $ls_url_ig, $ls_url_tk,
							$ls_nombre_soporte, $ls_fono_soporte, $ls_email_soporte, $li_horas, $li_clases, $li_min, $li_max,
							$ls_bienvenida, $ls_somos, $ls_mision, $ls_vision, 
							$ls_imagen, $_SESSION['usr_conectado'], $ldt_fecha_actualizacion);
	
	// Invocar Actualización
	$lb_result = $crud->fila_actualizar(DEF_TABLA_EMPRESA, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);
	
	// ===================================== FIN CRUD ACTUALIZAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		header("Location: ../vista/".DEF_URL_LOGIN);
	}
	else {
		header("Location: ../vista/mae_empresa_editar.php?id_codigo=".$_POST["id_codigo"]);
	}
	
}

?>
