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
	$ldt_fecha_insercion = date('Y-m-d h:i:s');
	
	// ==================================== VALIDACIONES SERVIDOR ======================================== //
	// Validar ingreso de datos correctos
	$array_campo_pk	= array('V_NOMBRE');
	$array_valor_pk	= array($ls_nombre);
	$li_cuenta = $crud->fila_contar(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk);
	
	// Código repetido
	if ($li_cuenta > 0) {
		$ls_mensaje = 'Sede '.$ls_des_larga.' se encuentra ocupado.';
		$lb_result  = false;
	}
	// =================================== FIN VALIDACIONES SERVIDOR ====================================== //
	
	// =======================================   UPLOAD    =========================================== //
	if ($lb_result == true && !empty($ls_imagen)) {
		// Upload de archivo
		$ls_imagen = f_upload_archivo($_FILES["archivo"], DEF_UPLOAD_SEDE_DIR, "C", DEF_UPLOAD_SEDE_W, DEF_UPLOAD_SEDE_H);
	}
	// =======================================   FIN UPLOAD    =========================================== //

	// ======================================= CRUD REGISTRAR =========================================== //
	if ($lb_result == true) {

		// Definir estructura
		$array_campo	= array('V_NOMBRE', 'V_DIRECCION', 'V_REFERENCIA', 'V_EMAIL', 'V_FONO', 'V_MOVIL', 'V_FOTO', 'V_FLAG_ESTADO', 'V_AUD_USR_REG', 'D_AUD_FEC_REG');
		$array_valor	= array($ls_nombre, $ls_direccion, $ls_referencia, $ls_email, $ls_fono, $ls_movil, $ls_imagen, $ls_flag_estado, $_SESSION['usr_conectado'], $ldt_fecha_insercion);
		
		// Invocar inserción
		$lb_result = $crud->fila_registrar(DEF_TABLA_SEDE, $array_campo, $array_valor, '0');

	}
	// ===================================== FIN CRUD REGISTRAR ========================================= //
	
	// Redireccionar
	if($lb_result == true) {
		header("Location: ../vista/mae_sede_lista.php");
	}
	else {
		header("Location: ../vista/mae_sede_nuevo.php?id_respuesta=".$ls_mensaje);
	}
					
}

?>