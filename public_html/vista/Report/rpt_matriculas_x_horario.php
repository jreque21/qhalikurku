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
	$li_cod_sede	= $bd->bd_escapeCadena($_POST['id_cod_sede']);
	$li_cod_horario	= $bd->bd_escapeCadena($_POST['id_cod_horario']);
}

// Datos de Sede
if ($li_cod_sede != '0'){
	$array_campo_pk	= array('N_COD_SEDE');
	$array_valor_pk	= array($li_cod_sede);
	$ls_des_sede   	= $crud->fila_recuperar_campo(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE');
}else{
	$ls_des_sede	= 'Ninguno';
}

// Datos de Horario
if ($li_cod_horario != '0'){
	$array_campo_pk	= array('N_COD_HORARIO');
	$array_valor_pk	= array($li_cod_horario);
	$ls_des_horario	= $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');
	$ls_fecha		= $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'D_FEC_INICIO');
	$ls_hora		= $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'D_HORA_INICIO');
}else{
	$ls_des_horario	= 'Ninguno';
}

// Almacenar resultados
$array_lista = $crud->fila_rpt_matxhorario($li_cod_horario);

// =========================================================================================================== //
// R E P O R T E
// =========================================================================================================== //

// HEADER 
// =========================================================
// Instanciar Clase
$pdf = new PDF('P','mm', 'A4'); 		// (P[Vetical],L[Horizontal]), milimetros, A4  
$pdf->AddPage();				 		// Añadir página en Blanco
$pdf->SetTitle("Lista de Matriculados");	// Titulo

// BODY 
// =========================================================
// Titulo
$pdf->SetFont('Courier','B',16);
$pdf->Cell(0,0,'LISTA DE MATRICULADOS',0, 0,'C',false);
$pdf->Ln(8);

// Resumen
$pdf->SetFont('Courier','',10);
$pdf->Cell(30,0,utf8_decode('Sede : '),0, 0,'L',false);
$pdf->Cell(80,0,utf8_decode($ls_des_sede),0, 0,'L',false);
$pdf->Cell(30,0,utf8_decode('Horario : '),0, 0,'L',false);
$pdf->Cell(0,0,utf8_decode($ls_des_horario),0, 0,'L',false);
$pdf->Ln(6);
$pdf->Cell(30,0,utf8_decode('Fecha Inicio : '),0, 0,'L',false);
$pdf->Cell(80,0,utf8_decode($ls_fecha),0, 0,'L',false);
$pdf->Cell(30,0,utf8_decode('Hora : '),0, 0,'L',false);
$pdf->Cell(0,0,substr(utf8_decode($ls_hora),0,5),0, 0,'L',false);
$pdf->Ln(10);

// Grilla
$pdf->SetLineWidth(0);
$pdf->SetDrawColor(255, 255, 255);			// Borde Color Celda
$pdf->SetFillColor(206, 226, 255);			// Fondo Color Celda
$pdf->SetTextColor(0, 0, 0);				// Color R,G,B
$pdf->Cell(8,8,utf8_decode('Nº'), 1, 0,'C',true);
$pdf->Cell(70,8,'Estudiante', 1, 0,'C',true);
$pdf->Cell(11,8,'Edad', 1, 0,'C',true);
$pdf->Cell(14,8,utf8_decode('Género'), 1, 0,'C',true);
$pdf->Cell(57,8,utf8_decode('Cinturón Actual'), 1, 0,'C',true);
$pdf->Cell(34,8,utf8_decode('Fecha Matrícula'), 1, 0,'C',true);
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
	$ls_est_cinturon	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');

	// Espacio
	$pdf->Ln(0.6);
	
	// Filas con contenido
	$pdf->Cell(8,8, $li_x, 'B', 0,'C', true);
	$pdf->Cell(70,8, utf8_decode($row_padron['V_APE_PATERNO']).' '.utf8_decode($row_padron['V_APE_MATERNO']).' '.utf8_decode($row_padron['V_NOMBRES']), 'B', 0,'L',true);
	$pdf->Cell(11,8, f_get_edad($row_padron['D_FEC_NACIMIENTO']), 'B', 0,'C',true);
	$pdf->Cell(14,8, $row_padron["V_FG_SEXO"], 'B', 0,'C',true);
	$pdf->Cell(57,8, utf8_decode($ls_est_cinturon), 'B', 0,'C',true);
	$pdf->Cell(34,8, utf8_decode($row['D_FEC_MATRICULA']), 'B', 1,'C',true);

}

// Ver reporte en pantalla
$pdf->Output('rpt_matricula_x_horario.pdf','i');

?>