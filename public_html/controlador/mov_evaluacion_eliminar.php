<?php
@session_start();

// Importar funcionalidades
require_once("../config/global.php");  
require_once(DEF_PATH_ADMIN);

// Instanciar clase de la B.D
$bd = new baseDatos();
$crud = new crud();

// Según Caso
if (isset($_GET['id_codigo'])) { 		// Esta siendo invocado desde Lista
	$li_codigo = $_GET["id_codigo"];
	$li_codigo_padre = $_GET["id_codigo_padre"];
	$lb_form   = false;
}else{									// Esta siendo invocado desde Formulario
	$li_codigo = $bd->bd_escapeCadena($_POST['id_codigo']);
	$li_codigo_padre = $bd->bd_escapeCadena($_POST['id_codigo_padre']);
	$lb_form   = true;	
}

// ======================================== PRE ELIMINAR ============================================= //

// ======================================= FIN PRE ELIMINAR =========================================== //

// ======================================== CRUD ELIMINAR ============================================= //

// Definir estructura
$array_campo_pk	= array('N_COD_EVALUACION');
$array_valor_pk	= array($li_codigo);

// Invocar CRUD
$lb_result = $crud->fila_eliminar(DEF_TABLA_EVALUACION, $array_campo_pk, $array_valor_pk);

// ======================================= FIN CRUD ELIMINAR =========================================== //

// Redireccionar
if($lb_result == true) {
	header("Location: ../vista/mov_evaluacion_lista.php?id_codigo_padre=".$li_codigo_padre);
}
else {
	header("Location: ../vista/mov_evaluacion_editar.php?id_codigo=".$li_codigo);
}

?>