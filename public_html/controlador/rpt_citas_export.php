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
$ls_cod_especialista = isset($_GET['id_cod_especialista']) ? $_GET['id_cod_especialista'] : '';
$ls_formato = isset($_GET['formato']) ? $_GET['formato'] : 'csv';

$array_estados = array(
    'PRO' => 'Programada', 'CON' => 'Confirmada', 'ATE' => 'Atendida',
    'REP' => 'Reprogramada', 'CAN' => 'Cancelada', 'NOA' => 'No asistió',
);

$ls_cond = "D_FEC_CITA BETWEEN '$ls_fec_ini' AND '$ls_fec_fin' AND V_FLAG_ESTADO = '1'";
if (!empty($ls_cod_especialista)) {
    $ls_cond .= " AND N_COD_ESPECIALISTA = '" . $bd->bd_escapeCadena($ls_cod_especialista) . "'";
}

$array_citas = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . " WHERE $ls_cond", 'D_FEC_CITA', 'A', 0, 5000);

$array_filas = array();

if ($array_citas) {
    while ($row = mysqli_fetch_assoc($array_citas)) {

        $array_campo_pk = array('N_COD_PACIENTE');
        $array_valor_pk = array($row["N_COD_PACIENTE"]);
        $ls_pac = $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO') . ' ' .
                  $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');

        $array_campo_pk = array('N_COD_ESPECIALISTA');
        $array_valor_pk = array($row["N_COD_ESPECIALISTA"]);
        $ls_esp = $crud->fila_recuperar_campo(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO') . ' ' .
                  $crud->fila_recuperar_campo(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');

        $array_filas[] = array(
            date('d/m/Y', strtotime($row["D_FEC_CITA"])),
            substr($row["D_HORA_INICIO"],0,5),
            $ls_pac,
            $ls_esp,
            isset($array_estados[$row["V_ESTADO_CITA"]]) ? $array_estados[$row["V_ESTADO_CITA"]] : $row["V_ESTADO_CITA"]
        );
    }
}

$array_encabezados = array('Fecha', 'Hora', 'Paciente', 'Especialista', 'Estado');

if ($ls_formato == 'pdf') {

    require_once(__DIR__ . "/../config/class_pdf_tabla.php");

    $pdf = new TablaPDF('L', 'mm', 'A4');
    $pdf->titulo_reporte = 'Reporte de Citas por Periodo';
    $pdf->subtitulo_reporte = 'Del ' . date('d/m/Y', strtotime($ls_fec_ini)) . ' al ' . date('d/m/Y', strtotime($ls_fec_fin));
    $pdf->AliasNbPages();
    $pdf->AddPage();

    $pdf->TablaDatos($array_encabezados, array(30, 25, 70, 70, 30), $array_filas);

    $pdf->Output('I', 'citas_' . $ls_fec_ini . '_' . $ls_fec_fin . '.pdf');

} else {

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="citas_' . $ls_fec_ini . '_' . $ls_fec_fin . '.csv"');

    $out = fopen('php://output', 'w');
    fputs($out, "\xEF\xBB\xBF");

    fputcsv($out, $array_encabezados);
    foreach ($array_filas as $fila) {
        fputcsv($out, $fila);
    }

    fclose($out);
}

exit;

?>
