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
	$ls_nombres		= $bd->bd_escapeCadena($_POST['id_nombres']);
	$ls_ape_paterno	= $bd->bd_escapeCadena($_POST['id_ape_paterno']);
	$ls_ape_materno	= $bd->bd_escapeCadena($_POST['id_ape_materno']);
	$ls_fg_sexo		= $bd->bd_escapeCadena($_POST['id_fg_sexo']);
	$ld_fec_nacimiento = $bd->bd_escapeCadena($_POST['id_fec_nacimiento']);
	$li_tipo_doc	= $bd->bd_escapeCadena($_POST['id_tipo_doc']);
	$ls_nro_doc		= $bd->bd_escapeCadena($_POST['id_nro_doc']);
	$li_cod_pais	= $bd->bd_escapeCadena($_POST['id_cod_pais']);
	$ls_direccion	= $bd->bd_escapeCadena($_POST['id_direccion']);
	$ls_referencia	= $bd->bd_escapeCadena($_POST['id_referencia']);
	$ls_email		= $bd->bd_escapeCadena($_POST['id_email']);
	$ls_fono		= $bd->bd_escapeCadena($_POST['id_fono']);
	$ls_movil		= $bd->bd_escapeCadena($_POST['id_movil']);
	$ls_imagen		= $bd->bd_escapeCadena($_FILES['archivo']['name']);
	$li_cod_especialidad= $bd->bd_escapeCadena($_POST['id_cod_especialidad']);
	$ls_colegiatura = $bd->bd_escapeCadena($_POST['id_colegiatura']);
	$ld_fec_alta	= $bd->bd_escapeCadena($_POST['id_fec_alta']);
	$ld_fec_baja	= $bd->bd_escapeCadena($_POST['id_fec_baja']);
	$ls_motivo_baja	= $bd->bd_escapeCadena($_POST['id_motivo_baja']);
	$ls_flag_estado	= $bd->bd_escapeCadena($_POST['id_flag_estado']);
	
	// Gestionar estado
	if ($ls_flag_estado != '1') {
		$ls_flag_estado = '0';
	}
	
	// Obtener datos iniciales
	$ldt_fecha_insercion = date('Y-m-d h:i:s');
	
	// ==================================== VALIDACIONES SERVIDOR ======================================== //
	// Validar ingreso de datos correctos
	$array_campo_pk	= array('N_COD_TIPODOC', 'V_NRO_DOC');
	$array_valor_pk	= array($li_tipo_doc, $ls_nro_doc);
	$li_cuenta = $crud->fila_contar(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk);
	
	// Código repetido
	if ($li_cuenta > 0) {
		$ls_mensaje = 'Especialista con Documento '.$ls_nro_doc.' se encuentra ocupado.';
		$lb_result  = false;
	}
	// =================================== FIN VALIDACIONES SERVIDOR ====================================== //
	
	// =======================================   UPLOAD    =========================================== //
	if ($lb_result == true && !empty($ls_imagen)) {
		// Upload de archivo
		$ls_imagen = f_upload_archivo($_FILES["archivo"], DEF_UPLOAD_ESPECIALISTA_DIR, "C", DEF_UPLOAD_ESPECIALISTA_W, DEF_UPLOAD_ESPECIALISTA_H);
	}
	// =======================================   FIN UPLOAD    =========================================== //

	// ======================================= CRUD REGISTRAR =========================================== //
	if ($lb_result == true) {

		// Definir estructura
		$array_campo	= array('V_NOMBRES', 'V_APE_PATERNO', 'V_APE_MATERNO', 'V_FG_SEXO', 'D_FEC_NACIMIENTO', 'N_COD_TIPODOC', 'V_NRO_DOC', 'N_COD_PAIS', 'V_DIRECCION', 'V_REFERENCIA', 'V_EMAIL', 'V_FONO', 'V_MOVIL', 'N_COD_ESPECIALIDAD', 'V_COLEGIATURA', 'D_FEC_ALTA', 'D_FEC_BAJA', 'V_MOTIVO_BAJA', 'V_FOTO', 'V_FLAG_ESTADO', 'V_AUD_USR_REG', 'D_AUD_FEC_REG');
		$array_valor	= array($ls_nombres, $ls_ape_paterno, $ls_ape_materno, $ls_fg_sexo, $ld_fec_nacimiento, $li_tipo_doc, $ls_nro_doc, $li_cod_pais, $ls_direccion, $ls_referencia, $ls_email, $ls_fono, $ls_movil, $li_cod_especialidad, $ls_colegiatura, $ld_fec_alta, $ld_fec_baja, $ls_motivo_baja, $ls_imagen, $ls_flag_estado, $_SESSION['usr_conectado'], $ldt_fecha_insercion);
		
		// Invocar insercion
		$lb_result = $crud->fila_registrar(DEF_TABLA_ESPECIALISTA, $array_campo, $array_valor, '0');

	}
	// ===================================== FIN CRUD REGISTRAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		header("Location: ../vista/mae_especialista_lista.php");
	}
	else {
		header("Location: ../vista/mae_especialista_nuevo.php?id_respuesta=".$ls_mensaje);
	}
					
}

?>