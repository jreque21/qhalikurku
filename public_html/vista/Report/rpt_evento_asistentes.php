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

// Almacena ID
$li_codigo_padre   = $_GET["id_codigo"];

// Instanciar clase
$crud = new crud();

// Almacenar Row de Horario
$array_campo_pk	= array('N_COD_EVENTO');
$array_valor_pk	= array($li_codigo_padre);
$row_padre      = $crud->fila_recuperar(DEF_TABLA_EVENTO, $array_campo_pk, $array_valor_pk);

// Almacenar Arreglo de Invitados
$array_campo_pk	= array('N_COD_EVENTO', 'V_FLAG_PROG');
$array_valor_pk	= array($li_codigo_padre, '1');
$array_lista 	= $crud->fila_listar(DEF_TABLA_EVENTOPROG, $array_campo_pk, $array_valor_pk, 'N_COD_ESTUDIANTE', 'A', 0, 100);

// =========================================================================================================== //
// R E P O R T E
// =========================================================================================================== //

// HEADER 
// =========================================================
// Instanciar Clase
$pdf = new PDF('P','mm', 'A4'); 		// (P[Vetical],L[Horizontal]), milimetros, A4  
$pdf->AddPage();				 		// Añadir página en Blanco
$pdf->SetTitle("Lista de Asistencia");	// Titulo

// BODY 
// =========================================================
// Titulo
$pdf->SetFont('Courier','B',16);
$pdf->Cell(0,0,'LISTA DE ASISTENCIA',0, 0,'C',false);
$pdf->Ln(8);

// Resumen
$pdf->SetFont('Courier','',10);
$pdf->Cell(30,0,'Evento : ',0, 0,'L',false);
$pdf->Cell(80,0,$row_padre['V_DESCRIPCION'],0, 0,'L',false);
$pdf->Cell(30,0,'Fecha : ',0, 0,'L',false);
$pdf->Cell(0,0,$row_padre['D_FECHA'],0, 0,'L',false);
$pdf->Ln(5);
$pdf->Cell(30,0,'Lugar : ',0, 0,'L',false);
$pdf->Cell(80,0,$row_padre['V_LUGAR'],0, 0,'L',false);
$pdf->Cell(30,0,'Hora : ',0, 0,'L',false);
$pdf->Cell(0,0,substr($row_padre['D_HORA'],0,5),0, 0,'L',false);
$pdf->Ln(10);

// Grilla
$pdf->SetLineWidth(0);
$pdf->SetDrawColor(255, 255, 255);			// Borde Color Celda
$pdf->SetFillColor(206, 226, 255);			// Fondo Color Celda
$pdf->SetTextColor(0, 0, 0);				// Color R,G,B
$pdf->Cell(10,8,utf8_decode('Nº'), 1, 0,'C',true);
$pdf->Cell(80,8,'Estudiante', 1, 0,'C',true);
$pdf->Cell(12,8,'Edad', 1, 0,'C',true);
$pdf->Cell(60,8,utf8_decode('Cinturón Actual'), 1, 0,'C',true);
$pdf->Cell(30,8,utf8_decode('Asistencia'), 1, 0,'C',true);
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
	
	// Almacenar Cinturón 
	$li_cinturon_actual = $crud->f_get_cinturonActual($row["N_COD_ESTUDIANTE"]);
	$array_campo_pk	= array('N_COD_CINTURON');
	$array_valor_pk	= array($li_cinturon_actual);
	$ls_cinturon	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');

	// Almacenar Asistencia
	if( $row["V_FLAG_EJEC"] == '1'){
		$ls_estado_asistencia = 'Si';
	}else{
		$ls_estado_asistencia = 'No';
	}

	// Espacio
	$pdf->Ln(0.6);
	
	// Filas con contenido
	$pdf->Cell(10,8, $li_x, 'B', 0,'C', true);
	$pdf->Cell(80,8, utf8_decode($row_padron['V_APE_PATERNO']).' '.utf8_decode($row_padron['V_APE_MATERNO']).' '.utf8_decode($row_padron['V_NOMBRES']), 'B', 0,'L',true);
	$pdf->Cell(12,8, f_get_edad($row_padron['D_FEC_NACIMIENTO']), 'B', 0,'C',true);
	$pdf->Cell(60,8, utf8_decode($ls_cinturon), 'B', 0,'C',true);
	$pdf->Cell(30,8, utf8_decode($ls_estado_asistencia), 'B', 1,'C',true);

}

// Ver reporte en pantalla
$pdf->Output('rpt_evento_asistentes.pdf','i');

?>