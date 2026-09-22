<?php
@session_start();

// Importar funcionalidades
require_once("../config/global.php");
require_once(DEF_PATH_ADMIN);
require_once(__DIR__ . "/../recursos/fpdf185/fpdf.php");

// Controlar sesion activa
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ' . DEF_URL_LOGIN);
    exit;
}

$crud = new crud();

// ID del pago a imprimir
$li_codigo = isset($_GET['id_codigo']) ? intval($_GET['id_codigo']) : 0;

if ($li_codigo <= 0) {
    die('Pago no especificado.');
}

// Recuperar el pago
$array_campo_pk = array('N_COD_PAGO');
$array_valor_pk = array($li_codigo);
$array_pago = $crud->fila_recuperar(DEF_TABLA_PAGO, $array_campo_pk, $array_valor_pk);

if ($array_pago === null) {
    die('El pago indicado no existe.');
}

// Comprobante asociado
$array_campo_pk = array('N_COD_COMPROBANTE');
$array_valor_pk = array($array_pago['N_COD_COMPROBANTE']);
$array_comp = $crud->fila_recuperar(DEF_TABLA_COMPROBANTE, $array_campo_pk, $array_valor_pk);

// Cita, paciente y especialista
$array_campo_pk = array('N_COD_CITA');
$array_valor_pk = array($array_pago['N_COD_CITA']);
$array_cita = $crud->fila_recuperar(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk);

$array_campo_pk = array('N_COD_PACIENTE');
$array_valor_pk = array($array_cita['N_COD_PACIENTE']);
$array_pac = $crud->fila_recuperar(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk);

$array_campo_pk = array('N_COD_ESPECIALISTA');
$array_valor_pk = array($array_cita['N_COD_ESPECIALISTA']);
$array_esp = $crud->fila_recuperar(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk);

// Catálogos
$array_campo_pk = array('N_COD_MONEDA');
$array_valor_pk = array($array_pago['N_COD_MONEDA']);
$ls_simbolo = $crud->fila_recuperar_campo(DEF_TABLA_MONEDA, $array_campo_pk, $array_valor_pk, 'V_SIMBOLO');

$array_campo_pk = array('N_COD_MEDIOPAGO');
$array_valor_pk = array($array_pago['N_COD_MEDIOPAGO']);
$ls_mediopago = $crud->fila_recuperar_campo(DEF_TABLA_MEDIOPAGO, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');

$array_campo_pk = array('N_COD_FORMAPAGO');
$array_valor_pk = array($array_comp['N_COD_FORMAPAGO']);
$ls_formapago = $crud->fila_recuperar_campo(DEF_TABLA_FORMAPAGO, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');

// Datos de la empresa/centro
$array_campo_pk = array('V_ID');
$array_valor_pk = array('1');
$array_empresa = $crud->fila_recuperar('MAE_EMPRESA', $array_campo_pk, $array_valor_pk);

// Saldo pendiente (si es crédito)
$lb_es_credito = ($array_comp['N_COD_FORMAPAGO'] == '2');
$ln_saldo = 0;
if ($lb_es_credito) {
    $ls_cond = "N_COD_COMPROBANTE = '" . $array_pago['N_COD_COMPROBANTE'] . "' AND V_FLAG_ESTADO = '1'";
    $lr = $crud->fila_listar_solocondicion("(SELECT COALESCE(SUM(N_MONTO),0) AS TOTAL FROM " . DEF_TABLA_PAGO . " WHERE $ls_cond) t", '', '', -1, 0);
    $row = $lr ? mysqli_fetch_assoc($lr) : null;
    $ln_pagado_total = $row ? floatval($row['TOTAL']) : 0;
    $ln_saldo = round(floatval($array_comp['N_MONTO']) - $ln_pagado_total, 2);
}

// ===================================== GENERAR PDF ===================================== //

class ReciboPDF extends FPDF {
    function Header() {
        // (sin logo por ahora; se puede agregar con Image() si el centro sube uno)
    }
}

$pdf = new ReciboPDF('P', 'mm', 'A5');
$pdf->AddPage();
$pdf->SetMargins(15, 15, 15);

// Encabezado del centro
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 7, utf8_decode($array_empresa['V_DESCRIPCION']), 0, 1, 'C');

$pdf->SetFont('Arial', '', 9);
if (!empty($array_empresa['V_DIRECCION'])) {
    $pdf->Cell(0, 5, utf8_decode($array_empresa['V_DIRECCION']), 0, 1, 'C');
}
$ls_contacto = trim(($array_empresa['V_FONO'] ? 'Tel: ' . $array_empresa['V_FONO'] : '') . ($array_empresa['V_EMAIL'] ? '  |  ' . $array_empresa['V_EMAIL'] : ''));
if (!empty($ls_contacto)) {
    $pdf->Cell(0, 5, utf8_decode($ls_contacto), 0, 1, 'C');
}

$pdf->Ln(3);
$pdf->SetDrawColor(150, 150, 150);
$pdf->Line(15, $pdf->GetY(), 133, $pdf->GetY());
$pdf->Ln(4);

// Título
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'RECIBO DE PAGO N° ' . str_pad($li_codigo, 6, '0', STR_PAD_LEFT), 0, 1, 'C');
$pdf->Ln(2);

// Datos del pago
$pdf->SetFont('Arial', '', 10);

function fila_dato($pdf, $etiqueta, $valor) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(45, 6, $etiqueta, 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, utf8_decode($valor), 0, 1);
}

fila_dato($pdf, 'Fecha de Pago:', date('d/m/Y', strtotime($array_pago['D_FEC_PAGO'])));
fila_dato($pdf, 'Paciente:', $array_pac['V_APE_PATERNO'] . ' ' . $array_pac['V_APE_MATERNO'] . ', ' . $array_pac['V_NOMBRES']);
fila_dato($pdf, 'Especialista:', $array_esp['V_APE_PATERNO'] . ' ' . $array_esp['V_APE_MATERNO']);
fila_dato($pdf, 'Fecha de Atención:', date('d/m/Y', strtotime($array_cita['D_FEC_CITA'])) . ' ' . substr($array_cita['D_HORA_INICIO'],0,5));
fila_dato($pdf, 'Forma de Pago:', $ls_formapago);
fila_dato($pdf, 'Medio de Pago:', $ls_mediopago);
if (!empty($array_pago['V_COMPROBANTE'])) {
    fila_dato($pdf, 'N° Comprobante:', $array_pago['V_COMPROBANTE']);
}

$pdf->Ln(3);
$pdf->SetDrawColor(150, 150, 150);
$pdf->Line(15, $pdf->GetY(), 133, $pdf->GetY());
$pdf->Ln(4);

// Monto pagado (destacado)
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(70, 8, 'MONTO PAGADO:', 0, 0);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, $ls_simbolo . ' ' . number_format($array_pago['N_MONTO'], 2), 0, 1, 'R');

if ($lb_es_credito) {
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(70, 6, 'Monto Total del Comprobante:', 0, 0);
    $pdf->Cell(0, 6, $ls_simbolo . ' ' . number_format($array_comp['N_MONTO'], 2), 0, 1, 'R');

    $pdf->SetFont('Arial', 'B', 10);
    if ($ln_saldo > 0) {
        $pdf->SetTextColor(200, 0, 0);
        $pdf->Cell(70, 6, 'Saldo Pendiente:', 0, 0);
        $pdf->Cell(0, 6, $ls_simbolo . ' ' . number_format($ln_saldo, 2), 0, 1, 'R');
        $pdf->SetTextColor(0, 0, 0);
    } else {
        $pdf->SetTextColor(0, 130, 0);
        $pdf->Cell(0, 6, 'CREDITO SALDADO EN SU TOTALIDAD', 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
    }
}

if (!empty($array_pago['V_OBSERVACION'])) {
    $pdf->Ln(4);
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->MultiCell(0, 5, utf8_decode('Observación: ' . $array_pago['V_OBSERVACION']));
}

$pdf->Ln(8);
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(120, 120, 120);
$pdf->Cell(0, 5, 'Documento generado el ' . date('d/m/Y H:i'), 0, 1, 'C');

$pdf->Output('I', 'recibo_pago_' . $li_codigo . '.pdf');

?>
