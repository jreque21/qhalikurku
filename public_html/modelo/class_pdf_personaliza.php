<?php
@session_start();

// Importar funcionalidades
require_once("../../config/global.php"); 

// Clase FPDF
class PDF extends FPDF
{
	// Header de Página
	function Header()
	{

		// Letra
		$this->SetFont('Courier','',12);	 						// Tipo de Letra
		$this->AliasNbPages();
		$this->setAuthor(SOPORTE_AUTOR);	 						// Autor
		$this->SetAutoPageBreak(true, 20);							// Cambio de Página
		$this->SetMargins(8, 8, 8);									// Margenes

		// Logo
		$this->Image('../../../upload/empresa/Logo.png',10,2,30,30); 	// Archivo, PosX, PosY, Width, Height
		
		// Header Titulo
		$this->SetFont('Courier','B',16);
		$this->Cell(0, 0,'JM TALENT GROUP',0, 0,'C',false);	// Width, Height, Texto, Border, Ln, Align, Fill, Link
		$this->Ln(7);

		// Header Subtitulo
		/*$this->SetFont('Courier','B',22);
		$this->Cell(0,0,'JM TALENT GROUP',0, 0,'C',false);
		$this->Ln(7);*/

		// Header Lema 
		$this->SetFont('Courier','',10);
		$this->Cell(0,0,utf8_decode('INSCRITO EN LOS REGISTROS PÚBLICOS PARTIDA Nº 11046590'),0, 0,'C',false);
		$this->Ln(4);
		$this->Cell(0,0,utf8_decode('AFILIADO A LA FEDERACIÓN DEPORTIVA PERUANA DE TAEKWONDO'),0, 0,'C',false);
		$this->Ln(4);
		$this->Cell(0,0,utf8_decode('INSCRITO EN EL REGISTRO NACIONAL DEL DEPOTE (IPD)'),0, 0,'C',false);
		$this->Ln(7);

		// Header Línea
		$this->SetLineWidth(1);
		$this->SetDrawColor(236,50,55);
		$this->Cell(0,0,'',1, 0,'C',true);
		$this->Ln(10);

	}
	
	// Footer de Página
	function Footer()
	{

		// Capturar Fecha
		$ldt_fecha_consulta = date('Y-m-d h:i:s');

		// Posicionar
		$this->setY(-15);

		// Header Línea
		$this->SetLineWidth(1);
		$this->SetDrawColor(0,0,0);
		$this->Cell(0,0,'',1, 0,'C',true);
		$this->Ln(2);
		
		$this->SetFont('Courier','I',8);
		$this->Cell(60,4,'Consultado por : '.$_SESSION['usr_conectado'],0,0,'L');
		$this->Cell(0,4,utf8_decode('Página : ').$this->PageNo().'/{nb}',0,0,'R');
		$this->Ln(4);

		$this->Cell(60,4,'Fecha y Hora : '.$ldt_fecha_consulta,0,0,'L');	

	}

}

// ==========================================================================================
// AYUDA
// ==========================================================================================
//$pdf->SetFontSize(11);					// Tamaño
//$pdf->Ln(8); 								// Espacio
//$pdf->setXY(10, 20); 						// PosX, PosY
//$pdf->setX(8); 							// PosX
//$pdf->SetDrawColor(236,50,55);			// Borde Color Celda
//$pdf->SetFillColor(236,50,55);			// Fondo Color Celda
//$pdf->SetTextColor(0, 0, 255);			// Color R,G,B
//$pdf->Rect(3.175, 3.175, 300, 2, 'DF');	// X, Y, W , H , STYLE(D, F, DF)

?>