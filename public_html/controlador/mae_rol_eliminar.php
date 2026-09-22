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
	$lb_form   = false;
}else{									// Esta siendo invocado desde Formulario
	$li_codigo = $bd->bd_escapeCadena($_POST['id_codigo']);
	$lb_form   = true;	
}

// ======================================== PRE ELIMINAR ============================================= //
// Definir estructura
$array_campo_pk	= array('V_COD_ROL', 'V_FLAG_ESTADO');
$array_valor_pk	= array($li_codigo, '1');

// Invocar CRUD
$li_contador = $crud->fila_contar(DEF_TABLA_ROLUSUARIO, $array_campo_pk, $array_valor_pk);
if ( $li_contador > 0 ) {
	$ls_mensaje	= 'Rol se encuentra referenciado en Usuarios por Rol.';
}
if ($li_contador == 0) {
	$li_contador = $crud->fila_contar(DEF_TABLA_ROLOPCION, $array_campo_pk, $array_valor_pk);
	if ( $li_contador > 0 ){
		$ls_mensaje	= 'Rol se encuentra referenciado en Permisos por Rol.';
	}
}

// Controlar Error
if ( $li_contador > 0 ) {
	// Redireccionar
	if($lb_form == true) {
		header("Location: ../vista/mae_rol_editar.php?id_codigo=".$li_codigo."&id_msgRpta=".$ls_mensaje);
	} else {
		header("Location: ../vista/mae_rol_lista.php?id_msgRpta=".$ls_mensaje);
	}
	return;
}
// ======================================= FIN PRE ELIMINAR =========================================== //

// ======================================== CRUD ELIMINAR ============================================= //

// Definir estructura
$array_campo_pk	= array('V_COD_ROL');
$array_valor_pk	= array($li_codigo);

// Invocar CRUD
$lb_result = $crud->fila_eliminar(DEF_TABLA_ROL, $array_campo_pk, $array_valor_pk);

// ======================================= FIN CRUD ELIMINAR =========================================== //

// Redireccionar
if($lb_result == true) {
	header("Location: ../vista/mae_rol_lista.php");
}
else {
	header("Location: ../vista/mae_rol_editar.php?id_codigo=".$li_codigo);
}

?>