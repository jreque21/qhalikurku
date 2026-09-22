<?php
@session_start();

// Importar funcionalidades
require_once("../config/lib_include_admin.php");

// Capturar operaci¨®n
$operacion = $_REQUEST['operacion'];

// SegÃºn caso
switch($operacion){
	Case 'Ejecutar Consulta': filtrar();
		break;
}

// Buscar Cliente
function filtrar(){
	
	// Instanciar clase de la B.D
	$bd 	= new baseDatos();
	$crud 	= new crud();
	
	// Capturar datos
	$_SESSION['SESION_CONSULTA']['FEC_INI'] 	= $_REQUEST['fec_ini'];
	$_SESSION['SESION_CONSULTA']['FEC_FIN'] 	= $_REQUEST['fec_fin'];
	$_SESSION['SESION_CONSULTA']['TIPO_PAGO'] 	= $_REQUEST['tipo_pago'];
	$_SESSION['SESION_CONSULTA']['ID_CLIENTE'] 	= $_REQUEST['id_cliente'];
	
	// Redireccionar ventana
	header("location:../vista/ventas_reporte.php");

}

?>