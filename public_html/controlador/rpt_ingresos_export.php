<?php
@session_start();

require_once(__DIR__ . "/../config/global.php");
require_once(DEF_PATH_ADMIN);

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ' . DEF_URL_LOGIN);
    exit;
}

$crud = new crud();
$bd   = new baseDatos();

$ls_fec_ini = isset($_GET['id_fec_ini']) && !empty($_GET['id_fec_ini']) ? $bd->bd_escapeCadena($_GET['id_fec_ini']) : date('Y-m-01');
$ls_fec_fin = isset($_GET['id_fec_fin']) && !empty($_GET['id_fec_fin']) ? $bd->bd_escapeCadena($_GET['id_fec_fin']) : date('Y-m-d');
$ls_formato = isset($_GET['formato']) ? $_GET['formato'] : 'csv';

// ===================== OBTENER DATOS (misma lógica que rpt_ingresos_html.php) ===================== //

$ls_cond = "D_FEC_PAGO BETWEEN '$ls_fec_ini' AND '$ls_fec_fin' AND V_FLAG_ESTADO = '1'";
$array_pagos = $crud->fila_listar_solocondicion(DEF_TABLA_PAGO . " WHERE $ls_cond", 'D_FEC_PAGO', 'A', 0, 5000);

$array_filas = array();
$ln_total = 0;

if ($array_pagos) {
    while ($row = mysqli_fetch_assoc($array_pagos)) {

        $array_campo_pk = array('N_COD_CITA');
        $array_valor_pk = array($row["N_COD_CITA"]);
        $li_cod_paciente = $crud->fila_recuperar_campo(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk, 'N_COD_PACIENTE');

        $array_campo_pk = array('N_COD_PACIENTE');
        $array_valor_pk = array($li_cod_paciente);
        $ls_pac = $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO') . ' ' .
                  $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');

        $array_campo_pk = array('N_COD_MEDIOPAGO');
        $array_valor_pk = array($row["N_COD_MEDIOPAGO"]);
        $ls_mediopago = $crud->fila_recuperar_campo(DEF_TABLA_MEDIOPAGO, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');

        $array_filas[] = array(
            date('d/m/Y', strtotime($row["D_FEC_PAGO"])),
            $ls_pac,
            $row["V_COMPROBANTE"] ? $row["V_COMPROBANTE"] : '-',
            $ls_mediopago,
            number_format($row["N_MONTO"], 2)
        );

        $ln_total += floatval($row["N_MONTO"]);
    }
}

$array_encabezados = array('Fecha', 'Paciente', 'Comprobante', 'Medio de Pago', 'Monto (S/)');

// ===================== EXPORTAR ===================== //

if ($ls_formato == 'pdf') {

    require_once(__DIR__ . "/../config/class_pdf_tabla.php");

    $pdf = new TablaPDF('L', 'mm', 'A4');
    $pdf->titulo_reporte = 'Reporte de Ingresos por Periodo';
    $pdf->subtitulo_reporte = 'Del ' . date('d/m/Y', strtotime($ls_fec_ini)) . ' al ' . date('d/m/Y', strtotime($ls_fec_fin));
    $pdf->AliasNbPages();
    $pdf->AddPage();

    $pdf->TablaDatos($array_encabezados, array(30, 70, 40, 45, 40), $array_filas);

    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, 'TOTAL: S/ ' . number_format($ln_total, 2), 0, 1, 'R');

    $pdf->Output('I', 'ingresos_' . $ls_fec_ini . '_' . $ls_fec_fin . '.pdf');

} else {

    // CSV (se abre directamente en Excel)
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="ingresos_' . $ls_fec_ini . '_' . $ls_fec_fin . '.csv"');

    $out = fopen('php://output', 'w');
    fputs($out, "\xEF\xBB\xBF"); // BOM para que Excel reconozca UTF-8 y las tildes

    fputcsv($out, $array_encabezados);
    foreach ($array_filas as $fila) {
        fputcsv($out, $fila);
    }
    fputcsv($out, array());
    fputcsv($out, array('', '', '', 'TOTAL', number_format($ln_total, 2)));

    fclose($out);
}

exit;

?>
