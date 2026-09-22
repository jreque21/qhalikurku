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
$array_campo_pk	= array('N_COD_SEDE', 'V_FLAG_ESTADO');
$array_valor_pk	= array($li_codigo, '1');

// Invocar CRUD
$li_contador = $crud->fila_contar(DEF_TABLA_SEDEESPECIALISTA, $array_campo_pk, $array_valor_pk);
if ( $li_contador > 0 ){
	$ls_mensaje	= 'Sede se encuentra referenciado en Especialistas por Sede.';
}

// Controlar Error
if ( $li_contador > 0 ) {
	// Redireccionar
	if($lb_form == true) {
		header("Location: ../vista/mae_sede_editar.php?id_codigo=".$li_codigo."&id_msgRpta=".$ls_mensaje);
	} else {
		header("Location: ../vista/mae_sede_lista.php?id_msgRpta=".$ls_mensaje);
	}
	return;
}
// ======================================= FIN PRE ELIMINAR =========================================== //

// ======================================== CRUD ELIMINAR ============================================= //

// Definir estructura
$array_campo_pk	= array('N_COD_SEDE');
$array_valor_pk	= array($li_codigo);

// Recupera imagen a eliminar
$ls_imagen_delete = $crud->fila_recuperar_campo(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_FOTO');

// Invocar CRUD
$lb_result = $crud->fila_eliminar(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk);

// ======================================= FIN CRUD ELIMINAR =========================================== //

// =======================================   UPLOAD    =========================================== //
// Borrar imagen fisicamente
if (!empty($ls_imagen_delete)) {
	
	// Preparar directorio
	$ls_imagen_delete = "../../upload/".DEF_UPLOAD_SEDE_DIR."/".$ls_imagen_delete;
	
	// Eliminar archivo
	if (file_exists($ls_imagen_delete)){
		unlink($ls_imagen_delete);
	}
			
}
// =======================================   FIN UPLOAD    =========================================== //

// Redireccionar
if($lb_result == true) {
	header("Location: ../vista/mae_sede_lista.php");
}
else {
	header("Location: ../vista/mae_sede_editar.php?id_codigo=".$li_codigo);
}

?>