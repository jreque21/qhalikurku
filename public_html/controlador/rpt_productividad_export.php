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

$array_campo_pk = array('V_FLAG_ESTADO');
$array_valor_pk = array('1');
$array_esp = $crud->fila_listar(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO, V_APE_MATERNO', 'A', 0, 999);

$array_filas = array();

if ($array_esp) {
    while ($this_esp = mysqli_fetch_assoc($array_esp)) {

        $li_cod_esp = $this_esp['N_COD_ESPECIALISTA'];
        $ls_cond_base = "N_COD_ESPECIALISTA = '$li_cod_esp' AND D_FEC_CITA BETWEEN '$ls_fec_ini' AND '$ls_fec_fin' AND V_FLAG_ESTADO = '1'";

        $lr = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . " WHERE $ls_cond_base", '', '', -1, 0);
        $li_total = $lr ? $lr->num_rows : 0;
        if ($li_total == 0) continue;

        $lr = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . " WHERE $ls_cond_base AND V_ESTADO_CITA = 'ATE'", '', '', -1, 0);
        $li_atendidas = $lr ? $lr->num_rows : 0;

        $lr = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . " WHERE $ls_cond_base AND V_ESTADO_CITA = 'CAN'", '', '', -1, 0);
        $li_canceladas = $lr ? $lr->num_rows : 0;

        $lr = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . " WHERE $ls_cond_base AND V_ESTADO_CITA = 'NOA'", '', '', -1, 0);
        $li_noasistio = $lr ? $lr->num_rows : 0;

        $ls_cond_ingresos = "p.V_FLAG_ESTADO = '1' AND p.D_FEC_PAGO BETWEEN '$ls_fec_ini' AND '$ls_fec_fin'
                                AND EXISTS (SELECT 1 FROM " . DEF_TABLA_CITA . " c WHERE c.N_COD_CITA = p.N_COD_CITA AND c.N_COD_ESPECIALISTA = '$li_cod_esp')";
        $lr = $crud->fila_listar_solocondicion("(SELECT COALESCE(SUM(p.N_MONTO),0) AS TOTAL FROM " . DEF_TABLA_PAGO . " p WHERE $ls_cond_ingresos) t", '', '', -1, 0);
        $row_tmp = $lr ? mysqli_fetch_assoc($lr) : null;
        $ln_ingresos = $row_tmp ? floatval($row_tmp['TOTAL']) : 0;

        $ln_pct = $li_total > 0 ? round(($li_atendidas / $li_total) * 100, 1) : 0;

        $array_filas[] = array(
            $this_esp['V_APE_PATERNO'] . ' ' . $this_esp['V_APE_MATERNO'],
            $li_total,
            $li_atendidas,
            $li_canceladas,
            $li_noasistio,
            $ln_pct . '%',
            number_format($ln_ingresos, 2)
        );
    }
}

$array_encabezados = array('Especialista', 'Total Citas', 'Atendidas', 'Canceladas', 'No Asistió', '% Asistencia', 'Ingresos (S/)');

if ($ls_formato == 'pdf') {

    require_once(__DIR__ . "/../config/class_pdf_tabla.php");

    $pdf = new TablaPDF('L', 'mm', 'A4');
    $pdf->titulo_reporte = 'Productividad por Especialista';
    $pdf->subtitulo_reporte = 'Del ' . date('d/m/Y', strtotime($ls_fec_ini)) . ' al ' . date('d/m/Y', strtotime($ls_fec_fin));
    $pdf->AliasNbPages();
    $pdf->AddPage();

    $pdf->TablaDatos($array_encabezados, array(60, 25, 25, 25, 25, 30, 35), $array_filas);

    $pdf->Output('I', 'productividad_' . $ls_fec_ini . '_' . $ls_fec_fin . '.pdf');

} else {

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="productividad_' . $ls_fec_ini . '_' . $ls_fec_fin . '.csv"');

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
