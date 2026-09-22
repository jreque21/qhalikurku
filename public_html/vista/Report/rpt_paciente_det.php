<?php
@session_start();

// Importar funcionalidades
require_once("../../config/global.php");
require_once("../../config/funciones.php");
require_once("../../config/class_baseDatos.php");
require_once("../../config/class_crud.php");
include('../../recursos/fpdf185/fpdf.php');

// Controlar sesion activa
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ' . DEF_URL_LOGIN);
    exit;
}

// Almacena ID
$li_codigo = isset($_GET["id_codigo"]) ? intval($_GET["id_codigo"]) : 0;

if ($li_codigo <= 0) {
    die('Paciente no especificado.');
}

$crud = new crud();

// Datos del paciente
$array_campo_pk = array('N_COD_PACIENTE');
$array_valor_pk = array($li_codigo);
$row = $crud->fila_recuperar(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk);

if ($row === null) {
    die('El paciente indicado no existe.');
}

// Catálogos relacionados
$array_campo_pk = array('N_COD_TIPODOC');
$array_valor_pk = array($row['N_COD_TIPODOC']);
$ls_tipodoc = $crud->fila_recuperar_campo(DEF_TABLA_TIPO_DOC, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');

$array_campo_pk = array('N_COD_PAIS');
$array_valor_pk = array($row['N_COD_PAIS']);
$ls_pais = $crud->fila_recuperar_campo(DEF_TABLA_PAIS, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');

$ls_departamento = '';
if (!empty($row['N_COD_DEPARTAMENTO'])) {
    $array_campo_pk = array('N_COD_DEPARTAMENTO');
    $array_valor_pk = array($row['N_COD_DEPARTAMENTO']);
    $ls_departamento = $crud->fila_recuperar_campo('MAE_DEPARTAMENTO', $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
}

$ls_provincia = '';
if (!empty($row['N_COD_PROVINCIA'])) {
    $array_campo_pk = array('N_COD_PROVINCIA');
    $array_valor_pk = array($row['N_COD_PROVINCIA']);
    $ls_provincia = $crud->fila_recuperar_campo('MAE_PROVINCIA', $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
}

$ls_distrito = '';
if (!empty($row['N_COD_DISTRITO'])) {
    $array_campo_pk = array('N_COD_DISTRITO');
    $array_valor_pk = array($row['N_COD_DISTRITO']);
    $ls_distrito = $crud->fila_recuperar_campo('MAE_DISTRITO', $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
}

$ls_sexo = ($row['V_FG_SEXO'] == 'M') ? 'Masculino' : (($row['V_FG_SEXO'] == 'F') ? 'Femenino' : '');
$li_edad = !empty($row['D_FEC_NACIMIENTO']) ? f_get_edad($row['D_FEC_NACIMIENTO']) : '';

// Datos del centro
$array_campo_pk = array('V_ID');
$array_valor_pk = array('1');
$array_empresa = $crud->fila_recuperar('MAE_EMPRESA', $array_campo_pk, $array_valor_pk);

// ===================================== GENERAR PDF ===================================== //

class FichaPacientePDF extends FPDF {
    public $empresa_nombre = '';
    public $empresa_logo = '';
    function Header() {
        if (!empty($this->empresa_logo) && file_exists($this->empresa_logo)) {
            $this->Image($this->empresa_logo, 15, 10, 18, 18);
        }
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, utf8_decode($this->empresa_nombre), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 7, 'FICHA DE PACIENTE', 0, 1, 'C');
        $this->Ln(3);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 10, 'Generado el ' . date('d/m/Y H:i'), 0, 0, 'C');
    }
}

// Logo de la empresa (el mismo del login). Si no tiene logo propio, se usa el institucional.
$ls_ruta_logo_empresa = '';
if ($array_empresa && !empty($array_empresa['V_FOTO'])) {
    $ls_ruta_candidata = __DIR__ . '/../../../upload/' . DEF_UPLOAD_EMPRESA_DIR . '/' . $array_empresa['V_FOTO'];
    if (file_exists($ls_ruta_candidata)) {
        $ls_ruta_logo_empresa = $ls_ruta_candidata;
    }
}
if (empty($ls_ruta_logo_empresa)) {
    $ls_ruta_candidata = __DIR__ . '/../../../website/recursos/images/Logo.png';
    if (file_exists($ls_ruta_candidata)) {
        $ls_ruta_logo_empresa = $ls_ruta_candidata;
    }
}

$pdf = new FichaPacientePDF('P', 'mm', 'A4');
$pdf->empresa_nombre = $array_empresa ? $array_empresa['V_DESCRIPCION'] : '';
$pdf->empresa_logo = $ls_ruta_logo_empresa;
$pdf->AddPage();
$pdf->SetMargins(15, 15, 15);

// Foto (si tiene)
$ln_x_inicio = 15;
if (!empty($row['V_FOTO'])) {
    $ls_ruta_foto = __DIR__ . '/../../../upload/' . DEF_UPLOAD_PACIENTE_DIR . '/' . $row['V_FOTO'];
    if (file_exists($ls_ruta_foto)) {
        $pdf->Image($ls_ruta_foto, 160, 30, 30, 30);
    }
}

function fila_dato($pdf, $etiqueta, $valor, $ancho_etq = 55) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($ancho_etq, 7, $etiqueta, 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 7, utf8_decode((string) $valor), 0, 1);
}

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, utf8_decode($row['V_APE_PATERNO'].' '.$row['V_APE_MATERNO'].', '.$row['V_NOMBRES']), 0, 1);
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(80, 80, 80);
$pdf->Cell(0, 6, 'DATOS PERSONALES', 0, 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(200, 200, 200);
$pdf->Line(15, $pdf->GetY(), 150, $pdf->GetY());
$pdf->Ln(2);

fila_dato($pdf, 'Documento:', $ls_tipodoc . ' ' . $row['V_NRO_DOC']);
fila_dato($pdf, 'Fecha de Nacimiento:', !empty($row['D_FEC_NACIMIENTO']) ? date('d/m/Y', strtotime($row['D_FEC_NACIMIENTO'])) . " ($li_edad años)" : '-');
fila_dato($pdf, 'Género:', $ls_sexo);
fila_dato($pdf, 'Nacionalidad:', $ls_pais);

$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(80, 80, 80);
$pdf->Cell(0, 6, 'CONTACTO Y DIRECCIÓN', 0, 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Line(15, $pdf->GetY(), 150, $pdf->GetY());
$pdf->Ln(2);

fila_dato($pdf, 'Teléfono:', $row['V_FONO'] ? $row['V_FONO'] : '-');
fila_dato($pdf, 'Celular:', $row['V_MOVIL'] ? $row['V_MOVIL'] : '-');
fila_dato($pdf, 'Email:', $row['V_EMAIL'] ? $row['V_EMAIL'] : '-');
fila_dato($pdf, 'Dirección:', $row['V_DIRECCION'] ? $row['V_DIRECCION'] : '-');
fila_dato($pdf, 'Departamento / Provincia / Distrito:', trim($ls_departamento . ' / ' . $ls_provincia . ' / ' . $ls_distrito, ' /'));
if (!empty($row['V_REFERENCIA'])) {
    fila_dato($pdf, 'Referencia:', $row['V_REFERENCIA']);
}

$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(80, 80, 80);
$pdf->Cell(0, 6, 'ESTADO', 0, 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Line(15, $pdf->GetY(), 150, $pdf->GetY());
$pdf->Ln(2);

fila_dato($pdf, 'Fecha de Alta:', !empty($row['D_FEC_ALTA']) ? date('d/m/Y', strtotime($row['D_FEC_ALTA'])) : '-');
fila_dato($pdf, 'Estado:', $row['V_FLAG_ESTADO'] == '1' ? 'Activo' : 'Inactivo');

// Última entrada de historia clínica (alergias/enfermedades vigentes, si existen)
$array_campo_pk = array('N_COD_PACIENTE', 'V_FLAG_ESTADO');
$array_valor_pk = array($li_codigo, '1');
$lr_hc = $crud->fila_listar(DEF_TABLA_HISTORIA_CLINICA, $array_campo_pk, $array_valor_pk, 'N_COD_HISTORIA', 'D', 0, 1);
$array_hc = $lr_hc ? mysqli_fetch_assoc($lr_hc) : null;

if ($array_hc && (!empty($array_hc['V_ALERGIAS']) || !empty($array_hc['V_ENFERMEDADES']))) {
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(180, 0, 0);
    $pdf->Cell(0, 6, 'ALERTAS CLÍNICAS', 0, 1);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Line(15, $pdf->GetY(), 150, $pdf->GetY());
    $pdf->Ln(2);

    if (!empty($array_hc['V_ALERGIAS'])) {
        fila_dato($pdf, 'Alergias:', $array_hc['V_ALERGIAS']);
    }
    if (!empty($array_hc['V_ENFERMEDADES'])) {
        fila_dato($pdf, 'Enfermedades:', $array_hc['V_ENFERMEDADES']);
    }
}

$pdf->Output('I', 'ficha_paciente_' . $li_codigo . '.pdf');

?>
