<?php
@session_start();

// Importar funcionalidades
require_once("../../config/global.php");  
require_once("../../config/funciones.php"); 
require_once("../../config/class_crud.php");
include('../../recursos/fpdf185/fpdf.php');
include('../../modelo/class_pdf_personaliza.php');

// Controlar sesión activa
if(!isset($_SESSION['logged_in']) && !$_SESSION['logged_in']){
	header('Location: '+ DEF_URL_LOGIN);
}

// Instanciar clase
$bd = new baseDatos();
$crud = new crud();

// Almacenar contenido con escape
if(isset($_POST) && !empty($_POST)){
	$li_cod_sede		= $bd->bd_escapeCadena($_POST['id_cod_sede']);
	$li_cod_estudiante	= $bd->bd_escapeCadena($_POST['id_cod_estudiante']);
	$li_cod_estado		= $bd->bd_escapeCadena($_POST['id_cod_estado']);
}

// Datos de Sede
if ($li_cod_sede != '0'){
	$array_campo_pk	= array('N_COD_SEDE');
	$array_valor_pk	= array($li_cod_sede);
	$ls_des_sede   	= $crud->fila_recuperar_campo(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE');
}else{
	$ls_des_sede	= 'Ninguno';
}

// Datos de Estudiante
if ($li_cod_estudiante != '%'){
	$array_campo_pk	= array('N_COD_ESTUDIANTE');
	$array_valor_pk	= array($li_cod_estudiante);
	$row_padron	    = $crud->fila_recuperar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk);
	$ls_des_estudiante = utf8_decode($row_padron['V_APE_PATERNO']).' '.utf8_decode($row_padron['V_APE_MATERNO']).' '.utf8_decode($row_padron['V_NOMBRES']);
}else{
	$ls_des_estudiante	= 'Todos';
}

// Datos de Estado
if ($li_cod_estado == '0'){
	$ls_des_estado	= 'Pendientes';
}elseif($li_cod_estado == '1'){
	$ls_des_estado	= 'Pagado';
}else{
	$ls_des_estado	= 'Todos';
}

// Almacenar resultados
$array_lista = $crud->fila_rpt_creditos($li_cod_sede, $li_cod_estudiante, $li_cod_estado);

// =========================================================================================================== //
// R E P O R T E
// =========================================================================================================== //

// HEADER 
// =========================================================
// Instanciar Clase
$pdf = new PDF('L','mm', 'A4'); 		// (P[Vetical],L[Horizontal]), milimetros, A4  
$pdf->AddPage();				 		// Añadir página en Blanco
$pdf->SetTitle("Lista de Creditos");	// Titulo

// BODY 
// =========================================================
// Titulo
$pdf->SetFont('Courier','B',16);
$pdf->Cell(0,0,utf8_decode('LISTA DE CRÉDITOS'),0, 0,'C',false);
$pdf->Ln(8);

// Resumen
$pdf->SetFont('Courier','',10);
$pdf->Cell(20,0,utf8_decode('Sede : '),0, 0,'L',false);
$pdf->Cell(70,0,utf8_decode($ls_des_sede),0, 0,'L',false);
$pdf->Cell(30,0,utf8_decode('Estudiante : '),0, 0,'L',false);
$pdf->Cell(100,0,utf8_decode($ls_des_estudiante),0, 0,'L',false);
$pdf->Cell(20,0,utf8_decode('Estado : '),0, 0,'L',false);
$pdf->Cell(0,0,utf8_decode($ls_des_estado),0, 0,'L',false);
$pdf->Ln(10);

// Grilla
$pdf->SetLineWidth(0);
$pdf->SetDrawColor(255, 255, 255);			// Borde Color Celda
$pdf->SetFillColor(206, 226, 255);			// Fondo Color Celda
$pdf->SetTextColor(0, 0, 0);				// Color R,G,B
$pdf->Cell(8,8,utf8_decode('Nº'), 1, 0,'C',true);
$pdf->Cell(100,8,'Estudiante', 1, 0,'C',true);
$pdf->Cell(35,8,utf8_decode('Fecha Matrícula'), 1, 0,'C',true);
$pdf->Cell(35,8,utf8_decode('Monto Crédito'), 1, 0,'C',true);
$pdf->Cell(35,8,utf8_decode('Monto Pagado'), 1, 0,'C',true);
$pdf->Cell(35,8,utf8_decode('Monto Pendiente'), 1, 0,'C',true);
$pdf->Cell(30,8,utf8_decode('Estado'), 1, 0,'C',true);

$pdf->Ln(8);

// Grilla Cuerpo
$li_x = 0;
while ($row = mysqli_fetch_assoc($array_lista)) {	

	// Incrementar Contador
	$li_x = $li_x + 1;
	
	// Resto
	if ($li_x%2==0){
		$pdf->SetFillColor(242, 242, 242);
	}else{
		$pdf->SetFillColor(255, 255, 255);
	}

	// Almacenar Row de Estudiante
	$array_campo_pk	= array('N_COD_ESTUDIANTE');
	$array_valor_pk	= array($row['N_COD_ESTUDIANTE']);
	$row_padron	    = $crud->fila_recuperar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk);
	
	// Saldo Pendiente
	$li_monto_pendiente	= $crud->f_get_saldoPendiente($row['N_COD_MATRICULA']);

	// Monto Pagado
	$li_monto_pagado	= $row["N_MONTO_NETO"] - $li_monto_pendiente;

	// Estado Pago
	if ($li_monto_pendiente == 0){
		$ls_estado_pago = 'Pagado';
	}else{
		$ls_estado_pago = 'Pendiente';
	}
	
	// Espacio
	$pdf->Ln(0.6);
	
	// Filas con contenido
	$pdf->Cell(8,8, $li_x, 'B', 0,'C', true);
	$pdf->Cell(100,8, utf8_decode($row_padron['V_APE_PATERNO']).' '.utf8_decode($row_padron['V_APE_MATERNO']).' '.utf8_decode($row_padron['V_NOMBRES']), 'B', 0,'L',true);
	$pdf->Cell(35,8, utf8_decode($row['D_FEC_MATRICULA']), 'B', 0,'C',true);
	$pdf->Cell(35,8, utf8_decode($row['N_MONTO_NETO']), 'B', 0,'C',true);
	$pdf->Cell(35,8, number_format($li_monto_pagado, 2, '.', ' '), 'B', 0,'C',true);
	$pdf->Cell(35,8, number_format($li_monto_pendiente, 2, '.', ' '), 'B', 0,'C',true);	
	$pdf->Cell(35,8, utf8_decode($ls_estado_pago), 'B', 1,'C',true);
}

// Ver reporte en pantalla
$pdf->Output('rpt_creditos_x_estudiante.pdf','i');

?>