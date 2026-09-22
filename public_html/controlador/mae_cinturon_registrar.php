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
	$ldt_fecha_insercion = date('Y-m-d h:i:s');
	
	// ==================================== VALIDACIONES SERVIDOR ======================================== //
	// Validar ingreso de datos correctos
	$array_campo_pk	= array('V_DES_LARGA');
	$array_valor_pk	= array($ls_des_larga);
	$li_cuenta = $crud->fila_contar(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk);
	
	// Código repetido
	if ($li_cuenta > 0) {
		$ls_mensaje = 'Cinturon '.$ls_des_larga.' se encuentra ocupado.';
		$lb_result  = false;
	}
	// =================================== FIN VALIDACIONES SERVIDOR ====================================== //
	
	
	// =======================================   UPLOAD    =========================================== //
	if ($lb_result == true && !empty($ls_imagen)) {
		// Upload de archivo
		$ls_imagen = f_upload_archivo($_FILES["archivo"], DEF_UPLOAD_CINTURON_DIR, "C", DEF_UPLOAD_CINTURON_W, DEF_UPLOAD_CINTURON_H);
	}
	// =======================================   FIN UPLOAD    =========================================== //

	// ======================================= CRUD REGISTRAR =========================================== //
	if ($lb_result == true) {

		// Definir estructura
		$array_campo	= array('V_DES_LARGA', 'V_DES_CORTA', 'N_COD_TIPOGRADO', 'V_COD_COLOR', 'V_TEXTO', 'N_ORDEN', 'V_FOTO', 'V_FLAG_ESTADO', 'V_AUD_USR_REG', 'D_AUD_FEC_REG');
		$array_valor	= array($ls_des_larga, $ls_des_corta, $li_tipo_grado, $ls_color, $ls_texto, $li_orden, $ls_imagen, $ls_flag_estado, $_SESSION['usr_conectado'], $ldt_fecha_insercion);
		
		// Invocar inserción
		$lb_result = $crud->fila_registrar(DEF_TABLA_CINTURON, $array_campo, $array_valor, '0');

	}
	// ===================================== FIN CRUD REGISTRAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		header("Location: ../vista/mae_cinturon_lista.php");
	}
	else {
		header("Location: ../vista/mae_cinturon_nuevo.php?id_respuesta=".$ls_mensaje);
	}
					
}

?>