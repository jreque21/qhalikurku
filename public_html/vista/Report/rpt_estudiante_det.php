<?php
@session_start();

// Importar funcionalidades
require_once("../../config/global.php");  
require_once("../../config/funciones.php"); 
require_once("../../config/class_crud.php");
include('../../recursos/fpdf185/fpdf.php');
include('../../modelo/class_pdf_personaliza.php');

// Controlar sesión activa
/*if(!isset($_SESSION['logged_in']) && !$_SESSION['logged_in']){
	header('Location: '+ DEF_URL_LOGIN);
}*/

// Almacena ID
$li_codigo	=	$_GET["id_codigo"];

// Instanciar clase
$crud = new crud();

// Almacenar Row de Estudiante
$array_campo_pk	= array('N_COD_ESTUDIANTE');
$array_valor_pk	= array($li_codigo);
$row_padron     = $crud->fila_recuperar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk);

// Datos de Sede
$array_campo_pk	= array('N_COD_SEDE');
$array_valor_pk	= array($row_padron['N_COD_SEDE']);
$ls_desc_sede   = $crud->fila_recuperar_campo(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE');

// Datos Tipo de Documento
$array_campo_pk	= array('N_COD_TIPODOC');
$array_valor_pk	= array($row_padron['N_COD_TIPODOC']);
$ls_desc_tipodoc= $crud->fila_recuperar_campo(DEF_TABLA_TIPO_DOC, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');

// Datos Tipo Estudiante
$array_campo_pk	= array('V_COD_TIPO');
$array_valor_pk	= array($row_padron['V_TIPO_EST']);
$ls_desc_tipoest= $crud->fila_recuperar_campo(DEF_TABLA_TIPOUSUARIO, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');

// Datos Nacionalidad
$array_campo_pk	= array('N_COD_PAIS');
$array_valor_pk	= array($row_padron['N_COD_PAIS']);
$ls_desc_pais   = $crud->fila_recuperar_campo(DEF_TABLA_PAIS, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');

// Cinturón Ingreso
$array_campo_pk	= array('N_COD_CINTURON');
$array_valor_pk	= array($row_padron["N_COD_CINTURON_ING"]);
$ls_cinturon_ing= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');

// Cinturón Actual
$li_cinturon_actual = $crud->f_get_cinturonActual($row_padron["N_COD_ESTUDIANTE"]);
$array_campo_pk	= array('N_COD_CINTURON');
$array_valor_pk	= array($li_cinturon_actual);
$ls_cinturon_act= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');

// Género
if ($row_padron["V_FG_SEXO"] =='M') $ls_des_sexo = 'Masculino';
if ($row_padron["V_FG_SEXO"] =='F') $ls_des_sexo = 'Femenino';

// Estado
if ($row_padron["V_FLAG_ESTADO"] =='0') $ls_des_estado = 'INACTIVO';
if ($row_padron["V_FLAG_ESTADO"] =='1') $ls_des_estado = 'ACTIVO';

// Foto Default
if ($row_padron["V_FG_SEXO"] =='M') {
	$ls_foto_default = 'est_masculino.png';
}
if ($row_padron["V_FG_SEXO"] =='F') {
	$ls_foto_default = 'est_femenino.png';
}
if(strlen($row_padron["V_FOTO"])>1){
	$ls_foto = '../../../upload/'.DEF_UPLOAD_ESTUDIANTE_DIR.'/'.$row_padron["V_FOTO"];
}else{
	$ls_foto = '../../../upload/'.DEF_UPLOAD_ESTUDIANTE_DIR.'/'.$ls_foto_default;
}

// =========================================================================================================== //
// R E P O R T E
// =========================================================================================================== //

// HEADER 
// =========================================================
$pdf = new PDF('P','mm', 'A4'); 		// (P[Vetical],L[Horizontal]), milimetros, A4  
$pdf->AddPage();				 		// Añadir página en Blanco
$pdf->SetTitle("Ficha de Estudiante");	// Titulo

// BODY 
// =========================================================
// Titulo
$pdf->SetFont('Courier','B',16);
$pdf->Cell(0,0,'FICHA DE ESTUDIANTE',0, 0,'C',false);
$pdf->Ln(8);

// Tipo de Letra Cuerpo
$pdf->SetFont('Courier','',10);

// Datos Personales
$pdf->SetLineWidth(0);
$pdf->SetDrawColor(255, 255, 255);			// Borde Color Celda
$pdf->SetFillColor(206, 226, 255);			// Fondo Color Celda
$pdf->SetTextColor(0, 0, 0);				// Color R,G,B
$pdf->Cell(0,8,utf8_decode('INFORMACIÓN GENERAL'),'B', 0,'L',true);
$pdf->Ln(11);

$pdf->SetLineWidth(0);
$pdf->SetDrawColor(242, 242, 242);			// Borde Color Celda
$pdf->SetFillColor(255, 255, 255);			// Fondo Color Celda

$pdf->Cell(50,8,'Apellidos y Nombres : ',1, 0,'L',true);
$pdf->Cell(100,8,utf8_decode($row_padron['V_APE_PATERNO']).' '.utf8_decode($row_padron['V_APE_MATERNO']).' '.utf8_decode($row_padron['V_NOMBRES']), 1, 1,'C',true);

$pdf->Image($ls_foto,162,68,35,40); 	// Archivo, PosX, PosY, Width, Height

$pdf->Cell(50,8,utf8_decode('Género : '),1, 0,'L',true);
$pdf->Cell(100,8,$ls_des_sexo, 1, 1,'L',true);

$pdf->Cell(50,8,'Fecha de Nacimiento : ',1, 0,'L',true);
$pdf->Cell(100,8,$row_padron['D_FEC_NACIMIENTO'], 1, 1,'L',true);

$pdf->Cell(50,8,'Edad : ',1, 0,'L',true);
$pdf->Cell(100,8,f_get_edad($row_padron['D_FEC_NACIMIENTO']), 1, 1,'L',true);

$pdf->Cell(50,8,'Nacionalidad : ',1, 0,'L',true);
$pdf->Cell(100,8,utf8_decode($ls_desc_pais), 1, 1,'L',true);

$pdf->Cell(50,8,utf8_decode('Tipo : '),1, 0,'L',true);
$pdf->Cell(100,8,$ls_desc_tipoest, 1, 0,'L',true);
$pdf->Cell(0,8,$ls_des_estado, 1, 1,'C',true);


$pdf->Cell(50,8,utf8_decode('Tipo de Documento : '),1, 0,'L',true);
$pdf->Cell(50,8,$ls_desc_tipodoc, 1, 0,'L',true);
$pdf->Cell(50,8,utf8_decode('Número Documento : '),1, 0,'L',true);
$pdf->Cell(0,8,$row_padron['V_NRO_DOC'], 1, 1,'L',true);

$pdf->Cell(50,8,utf8_decode('Fono : '),1, 0,'L',true);
$pdf->Cell(50,8,$row_padron['V_FONO'], 1, 0,'L',true);
$pdf->Cell(50,8,utf8_decode('Móvil : '),1, 0,'L',true);
$pdf->Cell(0,8,$row_padron['V_MOVIL'], 1, 1,'L',true);

$pdf->Cell(50,8,utf8_decode('Email : '),1, 0,'L',true);
$pdf->Cell(0,8,$row_padron['V_EMAIL'], 1, 1,'L',true);

$pdf->Cell(50,8,utf8_decode('Dirección : '),1, 0,'L',true);
$pdf->Cell(0,8,utf8_decode($row_padron['V_DIRECCION']), 1, 1,'L',true);

$pdf->Cell(50,8,utf8_decode('Referencia : '),1, 0,'L',true);
$pdf->Cell(0,8,utf8_decode($row_padron['V_REFERENCIA']), 1, 1,'L',true);

$pdf->Cell(50,8,'Apoderado : ',1, 0,'L',true);
$pdf->Cell(0,8,$row_padron['V_APODERADO'], 1, 1,'L',true);

$pdf->Cell(50,8,utf8_decode('Fecha Alta : '),1, 0,'L',true);
$pdf->Cell(50,8,$row_padron['D_FEC_ALTA'], 1, 0,'L',true);
$pdf->Cell(50,8,utf8_decode('Fecha Baja : '),1, 0,'L',true);
$pdf->Cell(0,8,$row_padron['D_FEC_BAJA'], 1, 1,'L',true);

$pdf->Ln(11);

// Datos Técnicos
$pdf->SetLineWidth(0);
$pdf->SetDrawColor(255, 255, 255);			// Borde Color Celda
$pdf->SetFillColor(206, 226, 255);			// Fondo Color Celda
$pdf->SetTextColor(0, 0, 0);				// Color R,G,B
$pdf->Cell(0,8,utf8_decode('INFORMACIÓN TÉCNICA'),0, 0,'L',true);
$pdf->Ln(11);

$pdf->SetLineWidth(0);
$pdf->SetDrawColor(242, 242, 242);			// Borde Color Celda
$pdf->SetFillColor(255, 255, 255);			// Fondo Color Celda

$pdf->Cell(50,8,'Sede : ',1, 0,'L',true);
$pdf->Cell(0,8,$ls_desc_sede, 1, 1,'L',true);

$pdf->Cell(50,8,utf8_decode('Cinturón Ingreso : '),1, 0,'L',true);
$pdf->Cell(0,8,$ls_cinturon_ing, 1, 1,'L',true);
$pdf->Cell(50,8,utf8_decode('Cinturón Actual : '),1, 0,'L',true);
$pdf->Cell(0,8,$ls_cinturon_act, 1, 1,'L',true);

$pdf->Cell(50,8,utf8_decode('Talla : '),1, 0,'L',true);
$pdf->Cell(50,8,$row_padron['N_TALLA'], 1, 0,'L',true);
$pdf->Cell(50,8,utf8_decode('Peso: '),1, 0,'L',true);
$pdf->Cell(0,8,$row_padron['N_PESO'], 1, 1,'L',true);

/*
// Grilla
$pdf->SetLineWidth(0);
$pdf->SetDrawColor(255, 255, 255);			// Borde Color Celda
$pdf->SetFillColor(206, 226, 255);			// Fondo Color Celda
$pdf->SetTextColor(0, 0, 0);				// Color R,G,B
$pdf->Cell(10,8,utf8_decode('Nº'), 1, 0,'C',true);
$pdf->Cell(80,8,'Estudiante', 1, 0,'C',true);
$pdf->Cell(12,8,'Edad', 1, 0,'C',true);
$pdf->Cell(60,8,utf8_decode('Cinturón Actual'), 1, 0,'C',true);
$pdf->Cell(30,8,utf8_decode('Situación'), 1, 0,'C',true);
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
	if( $row["V_FLAG_ESTADO"] == '1'){
		$ls_estado_asistencia = 'Asistió';
	}elseif ($row["V_FLAG_ESTADO"] == '-1') {
		$ls_estado_asistencia = 'No Asistió';
	}else{
		$ls_estado_asistencia = 'Pendiente';
	}

	// Espacio
	$pdf->Ln(0.6);
	
	// Filas con contenido
	$pdf->Cell(10,8, $li_x, 'B', 0,'C', true);
	$pdf->Cell(80,8, $row_padron['V_APE_PATERNO'].' '.$row_padron['V_APE_MATERNO'].' '.$row_padron['V_NOMBRES'], 'B', 0,'L',true);
	$pdf->Cell(12,8, f_get_edad($row_padron['D_FEC_NACIMIENTO']), 'B', 0,'C',true);
	$pdf->Cell(60,8, utf8_decode($ls_cinturon), 'B', 0,'C',true);
	$pdf->Cell(30,8, utf8_decode($ls_estado_asistencia), 'B', 1,'C',true);

}*/

// Ver reporte en pantalla
$pdf->Output('rpt_ficha_estudiante.pdf','i');

?>