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
	$li_cod_sede = $bd->bd_escapeCadena($_POST['id_cod_sede']);
	$ldt_fini	 = $bd->bd_escapeCadena($_POST['id_fini']);
	$ldt_ffin	 = $bd->bd_escapeCadena($_POST['id_ffin']);
}

// Datos de Sede
if ($li_cod_sede != '0'){
	$array_campo_pk	= array('N_COD_SEDE');
	$array_valor_pk	= array($li_cod_sede);
	$ls_des_sede   	= $crud->fila_recuperar_campo(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE');
}else{
	$ls_des_sede	= 'Ninguno';
}

// Periodo - Rango
$ls_periodo = $ldt_fini.' al '.$ldt_ffin;

// Almacenar resultados
$array_lista = $crud->fila_rpt_ingresosxperiodo($li_cod_sede, $ldt_fini, $ldt_ffin);

// =========================================================================================================== //
// R E P O R T E
// =========================================================================================================== //

// HEADER 
// =========================================================
// Instanciar Clase
$pdf = new PDF('P','mm', 'A4'); 		// (P[Vetical],L[Horizontal]), milimetros, A4  
$pdf->AddPage();				 		// Añadir página en Blanco
$pdf->SetTitle("Lista de Ingresos");	// Titulo

// BODY 
// =========================================================
// Titulo
$pdf->SetFont('Courier','B',16);
$pdf->Cell(0,0,'LISTA DE INGRESOS POR PERIODO',0, 0,'C',false);
$pdf->Ln(8);

// Resumen
$pdf->SetFont('Courier','',10);
$pdf->Cell(30,0,utf8_decode('Sede : '),0, 0,'L',false);
$pdf->Cell(80,0,utf8_decode($ls_des_sede),0, 0,'L',false);
$pdf->Ln(6);
$pdf->Cell(30,0,utf8_decode('Periodo : '),0, 0,'L',false);
$pdf->Cell(0,0,utf8_decode($ls_periodo),0, 0,'L',false);
$pdf->Ln(10);

// Grilla
$pdf->SetLineWidth(0);
$pdf->SetDrawColor(255, 255, 255);			// Borde Color Celda
$pdf->SetFillColor(206, 226, 255);			// Fondo Color Celda
$pdf->SetTextColor(0, 0, 0);				// Color R,G,B
$pdf->Cell(25,8,'Fecha', 1, 0,'C',true);
$pdf->Cell(40,8,utf8_decode('Pago en Efectivo'), 1, 0,'C',true);
$pdf->Cell(50,8,utf8_decode('Pago por Transferencia'), 1, 0,'C',true);
$pdf->Cell(40,8,utf8_decode('Pago con Tarjeta'), 1, 0,'C',true);
$pdf->Cell(40,8,utf8_decode('Pago Móvil'), 1, 0,'C',true);
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

	// Espacio
	$pdf->Ln(0.6);
	
	// Filas con contenido
	$pdf->Cell(25,8, utf8_decode($row['FECHA']), 'B', 0,'C',true);
	$pdf->Cell(40,8, utf8_decode($row['MONTO_EFECTIVO']), 'B', 0,'R',true);
	$pdf->Cell(50,8, utf8_decode($row['MONTO_TRANSFERENCIA']), 'B', 0,'R',true);
	$pdf->Cell(40,8, utf8_decode($row['MONTO_TARJETA']), 'B', 0,'R',true);
	$pdf->Cell(40,8, utf8_decode($row['MONTO_PAGOMOVIL']), 'B', 1,'R',true);
	

}

// Ver reporte en pantalla
$pdf->Output('rpt_ingresos_x_periodo.pdf','i');

?>