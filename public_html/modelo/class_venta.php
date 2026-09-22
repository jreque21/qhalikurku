<?php
@session_start();

// Importar funcionalidades
require_once("../config/lib_include_admin.php");

// Inicializar
$_SESSION['SESION_MSG'] = "";

// Capturar operación
$operacion = $_REQUEST['operacion'];
if (!isset($operacion)){
	$id_producto = $_REQUEST['id_producto'];
	retirarProducto($id_producto);
	RETURN;
}

// Según caso
switch($operacion){
	Case 'Buscar Cliente': buscarCliente();
		break;
	Case 'Agregar Producto': agregarProducto();
		break;
	Case 'Registrar': registrarVenta();
		break;
	Case 'Actualizar': actualizarVenta();
		break;
	Case 'Cancelar': cancelarVenta();
		break;
}

// Buscar Cliente
function buscarCliente(){
	
	// Instanciar clase de la B.D
	$bd 	= new baseDatos();
	$crud 	= new crud();
	
	// Capturar número documento
	$num_documento 		= $_REQUEST['search_documento'];
	$tipo_pago	   		= $_REQUEST['tipo_pago'];	
	$imp_pagado			= $_REQUEST['imp_pagado'];
	$id_operacion_ref	= $_REQUEST['id_operacion_ref'];
	$imp_amortizacion	= $_REQUEST['imp_amortizacion'];
	
	// Preparar estructura
	$array_campo_pk	= array(NUM_DOCUMENTO, FLAG_ESTADO);
	$array_valor_pk	= array($num_documento, '1');
	
	// Recuperar cliente
	$arrayCliente = $crud->fila_recuperar('MAE_CLIENTE', $array_campo_pk, $array_valor_pk);
	
	// Según caso
	if (is_array($arrayCliente)){
		
		// Registrar Sesión de Cliente
		$_SESSION['SESION_CLIENTE'] = $arrayCliente;
				
		// Actualizar sesión de venta, si existe
		if(isset($_SESSION['SESION_CLIENTE'])){
			$_SESSION['SESION_VENTA']['ID_CLIENTE'] 		= $_SESSION['SESION_CLIENTE']['ID_CLIENTE'];
			$_SESSION['SESION_VENTA']['TIPO_PAGO']  		= $tipo_pago;
			$_SESSION['SESION_VENTA']['IMP_PAGADO'] 		= $imp_pagado;
			$_SESSION['SESION_VENTA']['ID_OPERACION_REF']  	= $id_operacion_ref;
			$_SESSION['SESION_VENTA']['IMP_AMORTIZACION']  	= $imp_amortizacion;
		}
		
	}else{
		
		// Liberar sesión cliente
		unset($_SESSION['SESION_CLIENTE']);
		
		// Actualizar sesión de venta
		$_SESSION['SESION_VENTA']['ID_CLIENTE'] = "";	
		$_SESSION['SESION_VENTA']['ID_OPERACION_REF']  	= "";
		$_SESSION['SESION_VENTA']['IMP_AMORTIZACION']  	= "0.00";
		
		// Mensaje en pantalla
		$_SESSION['SESION_MSG'] = 'Código ingresado no existe o está inactivo. verifique por favor !';
		
	}

	// Redireccionar ventana
	header("location:../vista/venta_nuevo.php");
	
}

// Agregar Producto
function agregarProducto(){
	
	// Instanciar clase de la B.D
	$bd = new baseDatos();
	$crud = new crud();
	
	// Inicializar
	$item			= 0;
	$existe			= 0;
	$array_detventa = array();
	
	// CAPTURAR DATOS
	// ======================================================
	// Formulario principal
	$tipo_pago	   		= $_REQUEST['modal_tipo_pago'];	
	$imp_pagado			= $_REQUEST['modal_imp_pagado'];
	$id_operacion_ref	= $_REQUEST['modal_id_operacion_ref'];
	$imp_amortizacion	= $_REQUEST['modal_imp_amortizacion'];

	// Formulario Modal
	$id_producto 		= $_REQUEST['modal_producto'];
	$cantidad 	 		= $_REQUEST['modal_cantidad'];
	$peso	 			= $_REQUEST['modal_peso'];
	$precio	 			= $_REQUEST['modal_precio'];
	
	// VALIDACIONES
	// ======================================================
	// Selección de producto
	if ($id_producto == ""){
		$_SESSION['SESION_MSG'] = 'seleccione producto por favor.';
		header("location:../vista/venta_nuevo.php");
		RETURN;
	}

	// GESTIONAR REPETIDO
	// ======================================================
	// Obtener detalle
	if(isset($_SESSION['SESION_DETVENTA'])){
		$array_detventa	= $_SESSION['SESION_DETVENTA'];
	}
		
	// Buscar repetido
	foreach ($array_detventa as $prod){
		// Buscarlo
		if($id_producto.'-'.$peso == $prod['ID_PRODUCTO'].'-'.$prod['PESO']){
			$_SESSION['SESION_DETVENTA'][$item]['CANTIDAD'] = $_SESSION['SESION_DETVENTA'][$item]['CANTIDAD'] + $cantidad;
			$existe = 1;
			continue;
		}
		// Incrementar contador
		$item = $item + 1;
	}
	
	// SESION - PRODUCTO
	// ======================================================
	// No existe producto en sesión
	if ($existe == 0){
		
		// Calcular subtotal
		$subtotal			= $peso*$precio;
		
		// Preparar estructura
		$array_campo_pk	= array(ID_PRODUCTO, FLAG_ESTADO);
		$array_valor_pk	= array($id_producto, '1');
	
		// Recuperar producto
		$arrayProducto = $crud->fila_recuperar('MAE_PRODUCTO', $array_campo_pk, $array_valor_pk);
		$arrayProducto['DESCRIPCION'] 		= '';
		$arrayProducto['CANTIDAD'] 			= $cantidad;
		$arrayProducto['PESO'] 				= $peso;
		$arrayProducto['PRECIO_UNITARIO'] 	= $precio;
		$arrayProducto['SUBTOTAL'] 			= $subtotal;
		
		// Registrar Sesión de Cliente
		$_SESSION['SESION_DETVENTA'][] 		= $arrayProducto;	
		
	}
	
	// SESION - VENTA
	// ======================================================
	$_SESSION['SESION_VENTA']['TIPO_PAGO'] 	 		= $tipo_pago;
	$_SESSION['SESION_VENTA']['ID_OPERACION_REF'] 	= $id_operacion_ref;
	$_SESSION['SESION_VENTA']['IMP_AMORTIZACION'] 	= $imp_amortizacion;
	$_SESSION['SESION_VENTA']['IMP_TOTAL'] 	 		= $_SESSION['SESION_VENTA']['IMP_TOTAL'] + $subtotal;
	$_SESSION['SESION_VENTA']['IMP_PAGADO']  		= $_SESSION['SESION_VENTA']['IMP_TOTAL'];
	
	// Redireccionar
	header("location:../vista/venta_nuevo.php");
	
}

// Retirar Producto de la venta
function retirarProducto($id){
	
	
	// Capturar datos de formulario
	$item			= 0;
	$array_detventa = array();
		
	// Validar
	if(isset($_SESSION['SESION_DETVENTA'])){
		$array_detventa	= $_SESSION['SESION_DETVENTA'];
	}
		
	// Validar repetido
	foreach ($array_detventa as $det){
		// Buscarlo
		if($id == $det['ID_PRODUCTO'].'-'.$det['PESO']){
			$_SESSION['SESION_DETVENTA'][$item]['CANTIDAD'] = 0;
			$_SESSION['SESION_VENTA']['IMP_TOTAL'] 	 = $_SESSION['SESION_VENTA']['IMP_TOTAL'] - ($det['PESO']*$det['PRECIO_UNITARIO']);
			$_SESSION['SESION_VENTA']['IMP_PAGADO']  = $_SESSION['SESION_VENTA']['IMP_TOTAL'];
			continue;
		}
		// Incrementar contador
		$item = $item + 1;
	}
	
	// Redireccionar
	header("location:../vista/venta_nuevo.php");
	
}

// Registrar Venta
function registrarVenta(){
	
	// Instanciar clase de la B.D
	$bd 	= new baseDatos();
	$crud 	= new crud();	
	
	// Inicializar
	$item	=	0;
	$fecha_insercion = date('Y-m-d h:i:s');
	
	// CAPTURAR DATOS
	// ======================================================
	// Formulario principal
	$id_cliente			= $_SESSION['SESION_VENTA']['ID_CLIENTE'];
	$tipo_pago			= $_REQUEST['tipo_pago'];	
	$fec_venta			= date('Y-m-d');
	$imp_total			= $_SESSION['SESION_VENTA']['IMP_TOTAL'];
	$imp_pagado			= $_REQUEST['imp_pagado'];
	$id_operacion_ref	= $_REQUEST['id_operacion_ref'];
	$imp_amortizacion	= $_REQUEST['imp_amortizacion'];
	$imp_porpagar		= number_format(round(($imp_total - $imp_pagado),2),2);
	
	// VALIDACIONES
	// ======================================================
	// Documento del Cliente
	if ($id_cliente  == "" ){
		$_SESSION['SESION_MSG'] = ' aún no ingresaste cliente.';
		header("location:../vista/venta_nuevo.php");
		RETURN;
	}
	// Productos en lista 
	if ($imp_total <= 0){
		$_SESSION['SESION_MSG'] = ' aún no tienes productos agregados a tu lista.';
		header("location:../vista/venta_nuevo.php");
		RETURN;
	}
	// Importe pagado no mayor a total
	if ($imp_total < $imp_pagado){
		$_SESSION['SESION_MSG'] = ' el importe a pagar no debe ser mayor al importe total.';
		header("location:../vista/venta_nuevo.php");
		RETURN;
	}
	// Importes
	if ($tipo_pago == 'E' ){ // Contado
		if ($imp_total <> $imp_pagado){
			$_SESSION['SESION_MSG'] = ' el importe a pagar debe ser igual al total cuando tipo de pago es al contado.';
			header("location:../vista/venta_nuevo.php");
			RETURN;
		}
	}else{ // Crédito
		if ($imp_total == $imp_pagado){ 
			$_SESSION['SESION_MSG'] = ' el importe a pagar debe ser menor al total cuando tipo de pago es al crédito.';
			header("location:../vista/venta_nuevo.php");
			RETURN;
		}
	}
	// Si selecciono Referencia N.V, ingresar amortización
	if ($id_operacion_ref <> "" ){
		// Validar ingreso de datos
		if ($imp_amortizacion <= 0 ){
			$_SESSION['SESION_MSG'] = ' aún no ingresaste importe de amortización para referencia de Nota de Venta seleccionada.';
			header("location:../vista/venta_nuevo.php");
			RETURN;
		}
		// Validar Saldos
		$array_campo_operef_pk	= array(ID_OPERACION);
		$array_valor_operef_pk	= array($id_operacion_ref);
		$operef_pendiente		= $crud->fila_recuperar_campo('SAL_CTA_COBRAR', $array_campo_operef_pk, $array_valor_operef_pk, 'IMP_PENDIENTE');
		if ($imp_amortizacion > $operef_pendiente ){
			$_SESSION['SESION_MSG'] = ' importe de amortización ingresado supera el saldo del monto pendiente por cobrar.';
			header("location:../vista/venta_nuevo.php");
			RETURN;
		}		
	}else{
		if ($imp_amortizacion > 0 ){
			$_SESSION['SESION_MSG'] = ' aún no seleccionaste una referencia de Nota de Venta para el importe de amortización ingresado.';
			header("location:../vista/venta_nuevo.php");
			RETURN;
		}
	}
		
	// SESION - VENTA
	// ======================================================
	$_SESSION['SESION_VENTA']['SERIE'] 		 		= $serie;
	$_SESSION['SESION_VENTA']['CORRELATIVO'] 		= $correlativo;
	$_SESSION['SESION_VENTA']['TIPO_PAGO'] 	 		= $tipo_pago;
	$_SESSION['SESION_VENTA']['FEC_VENTA'] 	 		= $fec_venta;
	$_SESSION['SESION_VENTA']['IMP_TOTAL'] 	 		= $imp_total;
	$_SESSION['SESION_VENTA']['IMP_PAGADO']  		= $imp_pagado;
	$_SESSION['SESION_VENTA']['ID_OPERACION_REF']  	= $id_operacion_ref;
	$_SESSION['SESION_VENTA']['IMP_AMORTIZACION']  	= $imp_amortizacion;
	
	// ARREGLOS
	// ======================================================
	$array_venta	= $_SESSION['SESION_VENTA'];
	$array_detventa	= $_SESSION['SESION_DETVENTA'];
	
	// NUMERADOR
	// ======================================================
	// Obtener Máxima Serie
	$venta_serie 			= 	$crud->fila_recuperar_lastId('MAE_NUMERADOR_DOC', 'SERIE');
	$venta_serie			=	str_pad($venta_serie, 4, '0', STR_PAD_LEFT);

	// Obtener Máximo Numerador de Serie
	$array_campo_num_pk 	= 	array(ID_DOCUMENTO, SERIE);
	$array_valor_num_pk 	= 	array( DOC_NV, $venta_serie);
	$venta_correlativo		= 	$crud->fila_recuperar_lastIdPar('MAE_NUMERADOR_DOC', $array_campo_num_pk, $array_valor_num_pk, 'CORRELATIVO');
	$venta_correlativo		=	str_pad($venta_correlativo, 4, '0', STR_PAD_LEFT);
	
	// Validar numerador
	$array_campo_num_pk 	= array(ID_DOCUMENTO, SERIE, CORRELATIVO, FLAG_ESTADO);
	$array_valor_num_pk 	= array(DOC_NV, $venta_serie, $venta_correlativo, '1');
	$existe 				= $crud->fila_contar('OPE_VENTA_CAB', $array_campo_num_pk, $array_valor_num_pk);	
	if ($existe > 0){
		$_SESSION['SESION_MSG'] = ' correlativo '.$venta_correlativo.' de serie '.$venta_serie.' está ocupado. Verifique por favor !';
		header("location:../vista/venta_nuevo.php");
		RETURN;
	}
	
	// REGISTRAR VENTA
	// ======================================================
	// Definir estructura
	$array_campo_venta = array(ID_OPERACION, ID_DOCUMENTO, SERIE, CORRELATIVO, ID_CLIENTE, FEC_VENTA, TIPO_PAGO, IMP_TOTAL, IMP_PAGADO, IMP_PORPAGAR, ID_OPERACION_REF, IMP_AMORTIZACION, FLAG_ESTADO, AUD_USR_REG, AUD_FEC_REG);
	$array_valor_venta = array(NULL, DOC_NV, $venta_serie, $venta_correlativo, $array_venta['ID_CLIENTE'], $array_venta['FEC_VENTA'], $array_venta['TIPO_PAGO'], $array_venta['IMP_TOTAL'], $array_venta['IMP_PAGADO'], $imp_porpagar, $array_venta['ID_OPERACION_REF'], $array_venta['IMP_AMORTIZACION'], '1', $_SESSION['usr_conectado'], $fecha_insercion);
		
	// Invocar inserción
	$result = $crud->fila_registrar('OPE_VENTA_CAB', $array_campo_venta, $array_valor_venta, '0');

	// Validar
	if ($result == false){
		$_SESSION['SESION_MSG'] = ' error al registrar venta, intentalo luego por favor.';
		header("location:../vista/venta_nuevo.php");
		RETURN;
	}
	
	// REGISTRAR DETALLE VENTA
	// ======================================================
	// Obtener último ID registrado
	$array_campo_id_pk	= array(ID_DOCUMENTO, SERIE, CORRELATIVO, FLAG_ESTADO);
	$array_valor_id_pk	= array(DOC_NV, $venta_serie, $venta_correlativo, '1');
	$id_operacion		= $crud->fila_recuperar_campo('OPE_VENTA_CAB', $array_campo_id_pk, $array_valor_id_pk, 'ID_OPERACION');

	// Bucle detalle
	foreach ($array_detventa as $prod){
		
		// Saltar ítems retirados
		if($prod['CANTIDAD']==0){
			continue;
		}	
		
		// Incrementar contador
		$item = $item + 1;
		
		// Definir estructura
		$array_campo_detventa = array(ID_OPERACION, ID_ITEM, ID_PRODUCTO, CANTIDAD, PESO, PRECIO_UNITARIO, IMP_SUBTOTAL, FLAG_ESTADO, AUD_USR_REG, AUD_FEC_REG);
		$array_valor_detventa = array($id_operacion, $item, $prod['ID_PRODUCTO'], $prod['CANTIDAD'], $prod['PESO'], $prod['PRECIO_UNITARIO'], $prod['SUBTOTAL'], '1', $_SESSION['usr_conectado'], $fecha_insercion);
		
		// Invocar inserción
		$result = $crud->fila_registrar('OPE_VENTA_DET', $array_campo_detventa, $array_valor_detventa, '0');
	
	}
	
	// ACTUALIZAR SALDOS DE CUENTAS POR COBRAR - CRÉDITO
	// ======================================================
	if ($tipo_pago == 'C' ){ // Crédito
		// Definir estructura
		$array_campo_cxc = array(ID_OPERACION, ID_CLIENTE, IMP_PENDIENTE, FLAG_ESTADO, AUD_USR_REG, AUD_FEC_REG);
		$array_valor_cxc = array($id_operacion, $array_venta['ID_CLIENTE'], $imp_porpagar, '1', $_SESSION['usr_conectado'], $fecha_insercion);
		
		// Invocar inserción
		$result = $crud->fila_registrar('SAL_CTA_COBRAR', $array_campo_cxc, $array_valor_cxc, '0');
	}
	
	// ACTUALIZAR SALDOS DE CUENTAS POR COBRAR - AMORTIZACIÓN
	// ======================================================
	if ($imp_amortizacion > 0 ){
		// Definir estructura
		$array_campo_amort_pk = array(ID_OPERACION);
		$array_valor_amort_pk = array($id_operacion_ref);
		$imp_actualizado = $operef_pendiente - $imp_amortizacion;
		// Datos a actualizar
		$array_campo_amort 	= array(IMP_PENDIENTE, AUD_USR_MOD, AUD_FEC_MOD);
		$array_valor_amort 	= array($imp_actualizado, $_SESSION['usr_conectado'], $fecha_insercion);
		
		// Actualizar
		$result		= 	$crud->fila_actualizar('SAL_CTA_COBRAR', $array_campo_amort_pk, $array_valor_amort_pk, $array_campo_amort, $array_valor_amort);
	}
		
	
	// ACTUALIZAR NUMERADOR 
	// ======================================================		
	// Definir estructura
	$array_campo_num_pk = array(ID_DOCUMENTO, SERIE);
	$array_valor_num_pk = array(DOC_NV, $venta_serie);
	
	// Datos a actualizar
	$array_campo_num 	= array(CORRELATIVO, AUD_USR_MOD, AUD_FEC_MOD);
	$array_valor_num 	= array($venta_correlativo+1, $_SESSION['usr_conectado'], $fecha_insercion);
	
	// Actualizar
	$result		= 	$crud->fila_actualizar('MAE_NUMERADOR_DOC', $array_campo_num_pk, $array_valor_num_pk, $array_campo_num, $array_valor_num);
	
	// DISPARAR LIMPIEZA
	// ===========================================
	limpiarVenta();
	
	// SESION TICKET
	// ===========================================
	$_SESSION['SESION_TICKET'] = $id_operacion;
	
	// Redireccionar ventana
	header("location:../vista/venta_lista.php");
	
}

// Actualizar Venta
function actualizarVenta(){
	
	// Instanciar clase de la B.D
	$bd 	= new baseDatos();
	$crud 	= new crud();	
	
	// Inicializar
	$item	=	0;
	$fecha_actualizacion = date('Y-m-d h:i:s');
	
	// CAPTURAR DATOS
	// ======================================================
	// Formulario principal
	$id_operacion		= $_SESSION['SESION_VENTA']['ID_OPERACION'];
	$id_cliente			= $_SESSION['SESION_VENTA']['ID_CLIENTE'];
	$tipo_pago			= $_REQUEST['tipo_pago'];	
	$imp_total			= $_SESSION['SESION_VENTA']['IMP_TOTAL'];
	$imp_pagado			= $_REQUEST['imp_pagado'];
	$imp_porpagar		= number_format(round(($imp_total - $imp_pagado),2),2);	 
	
	// Datos a usar
	$array_campo_id_pk	= array(ID_OPERACION);
	$array_valor_id_pk	= array($id_operacion);
	$imp_porpagar_ant	= $crud->fila_recuperar_campo('OPE_VENTA_CAB', $array_campo_id_pk, $array_valor_id_pk, 'IMP_PORPAGAR');
	
	// VALIDACIONES
	// ======================================================
	// Documento del Cliente
	if ($id_cliente  == "" ){
		$_SESSION['SESION_MSG'] = ' aún no ingresaste cliente.';
		header("location:../vista/venta_nuevo.php");
		RETURN;
	}
	// Productos en lista 
	if ($imp_total <= 0){
		$_SESSION['SESION_MSG'] = ' aún no tienes productos agregados a tu lista.';
		header("location:../vista/venta_nuevo.php");
		RETURN;
	}
	// Importe pagado no mayor a total
	if ($imp_total < $imp_pagado){
		$_SESSION['SESION_MSG'] = ' el importe a pagar no debe ser mayor al importe total.';
		header("location:../vista/venta_nuevo.php");
		RETURN;
	}
	// Importes
	if ($tipo_pago == 'E' ){ // Contado
		if ($imp_total <> $imp_pagado){
			$_SESSION['SESION_MSG'] = ' el importe a pagar debe ser igual al total cuando tipo de pago es al contado.';
			header("location:../vista/venta_nuevo.php");
			RETURN;
		}
	}else{ // Crédito
		if ($imp_total == $imp_pagado){ 
			$_SESSION['SESION_MSG'] = ' el importe a pagar debe ser menor al total cuando tipo de pago es al crédito.';
			header("location:../vista/venta_nuevo.php");
			RETURN;
		}
	}
			
	// SESION - VENTA
	// ======================================================
	$_SESSION['SESION_VENTA']['TIPO_PAGO'] 	 		= $tipo_pago;
	$_SESSION['SESION_VENTA']['IMP_TOTAL'] 	 		= $imp_total;
	$_SESSION['SESION_VENTA']['IMP_PAGADO']  		= $imp_pagado;
	
	// ARREGLOS
	// ======================================================
	$array_venta	= $_SESSION['SESION_VENTA'];
	$array_detventa	= $_SESSION['SESION_DETVENTA'];
	
	// ACTUALIZAR VENTA
	// ======================================================
	// Definir estructura
	$array_campo_venta_pk	= array(ID_OPERACION);
	$array_valor_venta_pk	= array($id_operacion);
	$array_campo_venta 		= array(TIPO_PAGO, IMP_TOTAL, IMP_PAGADO, IMP_PORPAGAR, AUD_USR_MOD, AUD_FEC_MOD);
	$array_valor_venta 		= array($array_venta['TIPO_PAGO'], $array_venta['IMP_TOTAL'], $array_venta['IMP_PAGADO'], $imp_porpagar, $_SESSION['usr_conectado'], $fecha_actualizacion);
		
	// Invocar actualización
	$result = $crud->fila_actualizar('OPE_VENTA_CAB', $array_campo_venta_pk, $array_valor_venta_pk, $array_campo_venta, $array_valor_venta);

	// Validar
	if ($result == false){
		$_SESSION['SESION_MSG'] = ' error al actualizar venta, intentalo luego por favor.';
		header("location:../vista/venta_lista.php");
		RETURN;
	}
	
	// REGISTRAR DETALLE VENTA
	// ======================================================
	// Eliminar Fila
	$result = $crud->fila_eliminar('OPE_VENTA_DET', $array_campo_venta_pk, $array_valor_venta_pk);

	// Bucle detalle, para registrarlo
	foreach ($array_detventa as $prod){
		
		// Saltar ítems retirados
		if($prod['CANTIDAD']==0){
			continue;
		}	
		
		// Incrementar contador
		$item = $item + 1;
		
		// Definir estructura
		$array_campo_detventa = array(ID_OPERACION, ID_ITEM, ID_PRODUCTO, CANTIDAD, PESO, PRECIO_UNITARIO, IMP_SUBTOTAL, FLAG_ESTADO, AUD_USR_REG, AUD_FEC_REG);
		$array_valor_detventa = array($id_operacion, $item, $prod['ID_PRODUCTO'], $prod['CANTIDAD'], $prod['PESO'], $prod['PRECIO_UNITARIO'], $prod['SUBTOTAL'], '1', $_SESSION['usr_conectado'], $fecha_actualizacion);
		
		// Invocar inserción
		$result = $crud->fila_registrar('OPE_VENTA_DET', $array_campo_detventa, $array_valor_detventa, '0');
	
	}
	
	// ACTUALIZAR SALDOS DE CUENTAS POR COBRAR - CRÉDITO
	// ======================================================
	if ($imp_porpagar <> $imp_porpagar_ant ){ // Cambio cuenta por pagar
	
		// Contar
		$existe = $crud->fila_contar('SAL_CTA_COBRAR', $array_campo_venta_pk, $array_valor_venta_pk);
		
		// Según caso
		if ($existe == 0 ){
			// Registra
			$array_campo_cxc = array(ID_OPERACION, ID_CLIENTE, IMP_PENDIENTE, FLAG_ESTADO, AUD_USR_REG, AUD_FEC_REG);
			$array_valor_cxc = array($id_operacion, $array_venta['ID_CLIENTE'], $imp_porpagar, '1', $_SESSION['usr_conectado'], $fecha_actualizacion);
			$result = $crud->fila_registrar('SAL_CTA_COBRAR', $array_campo_cxc, $array_valor_cxc, '0');
		}else{
			// Recalcula
			$imp_pendiente_temp = $imp_porpagar - $imp_porpagar_ant;
			
			// Actualiza
			$array_campo_cxc = array(AUD_USR_MOD, AUD_FEC_MOD);
			$array_valor_cxc = array($_SESSION['usr_conectado'], $fecha_actualizacion);
			$result = $crud->fila_actualizarSaldo('SAL_CTA_COBRAR', $array_campo_venta_pk, $array_valor_venta_pk, $array_campo_cxc, $array_valor_cxc, 'IMP_PENDIENTE', $imp_pendiente_temp);
		}

	}
	
	// DISPARAR LIMPIEZA
	// ===========================================
	limpiarVenta();
	
	// SESION TICKET
	// ===========================================
	$_SESSION['SESION_TICKET'] = $id_operacion;
	
	// Redireccionar ventana
	header("location:../vista/venta_lista.php");
	
}

// Cancelar Venta
function cancelarVenta(){
	
	// Cerrar sesiones
	unset($_SESSION['SESION_CLIENTE']);
	unset($_SESSION['SESION_VENTA']);
	unset($_SESSION['SESION_DETVENTA']);
	unset($_SESSION['SESION_MSG']);
	
	// Redireccionar
	header("location:../vista/venta_lista.php");
	
}

// Limpiar Venta
function limpiarVenta(){
	
	// Cerrar sesiones
	unset($_SESSION['SESION_CLIENTE']);
	unset($_SESSION['SESION_VENTA']);
	unset($_SESSION['SESION_DETVENTA']);
	unset($_SESSION['SESION_MSG']);
		
}

?>