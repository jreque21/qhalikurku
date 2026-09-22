<?php

require_once(__DIR__ . "/../recursos/fpdf185/fpdf.php");

/**
 * Genera un PDF tabular genérico a partir de encabezados y filas.
 * Pensado para reportes: título, subtítulo (ej. rango de fechas), y una tabla.
 */
class TablaPDF extends FPDF {

    public $titulo_reporte = '';
    public $subtitulo_reporte = '';

    function Header() {

        // Logo de la empresa (el mismo configurado en Mantenimiento > Empresa,
        // usado también en el login). Si no tiene logo propio, se usa el
        // logo institucional por defecto.
        $ls_ruta_logo = '';
        if (class_exists('crud')) {
            $crud_logo = new crud();
            $array_campo_pk_logo = array('V_ID');
            $array_valor_pk_logo = array(1);
            $ls_foto_empresa = $crud_logo->fila_recuperar_campo(DEF_TABLA_EMPRESA, $array_campo_pk_logo, $array_valor_pk_logo, 'V_FOTO');

            if (!empty($ls_foto_empresa)) {
                $ls_ruta_candidata = __DIR__ . '/../../upload/' . DEF_UPLOAD_EMPRESA_DIR . '/' . $ls_foto_empresa;
                if (file_exists($ls_ruta_candidata)) {
                    $ls_ruta_logo = $ls_ruta_candidata;
                }
            }
            if (empty($ls_ruta_logo)) {
                $ls_ruta_candidata = __DIR__ . '/../../website/recursos/images/Logo.png';
                if (file_exists($ls_ruta_candidata)) {
                    $ls_ruta_logo = $ls_ruta_candidata;
                }
            }
        }
        if (!empty($ls_ruta_logo)) {
            $this->Image($ls_ruta_logo, 10, 8, 18, 18);
        }

        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, utf8_decode($this->titulo_reporte), 0, 1, 'C');
        if (!empty($this->subtitulo_reporte)) {
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 6, utf8_decode($this->subtitulo_reporte), 0, 1, 'C');
        }
        $this->Ln(2);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}  -  Generado el ' . date('d/m/Y H:i'), 0, 0, 'C');
    }

    /**
     * Dibuja la tabla.
     * @param array $encabezados  ['Col1', 'Col2', ...]
     * @param array $anchos       [30, 50, ...] en mm (debe sumar <= ancho útil de página)
     * @param array $filas        [['valor1','valor2'], ['valor1','valor2'], ...]
     */
    function TablaDatos($encabezados, $anchos, $filas) {

        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(230, 230, 230);
        foreach ($encabezados as $i => $enc) {
            $this->Cell($anchos[$i], 7, utf8_decode($enc), 1, 0, 'C', true);
        }
        $this->Ln();

        $this->SetFont('Arial', '', 8);
        foreach ($filas as $fila) {
            foreach ($fila as $i => $valor) {
                $this->Cell($anchos[$i], 6, utf8_decode((string) $valor), 1, 0, 'L');
            }
            $this->Ln();
        }
    }
}

?>
