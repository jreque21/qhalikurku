<?php
/**
 * cron_recordatorios_whatsapp.php
 *
 * Busca citas próximas (dentro de las N horas de anticipación configuradas)
 * que todavía no tengan un recordatorio enviado, y les manda un WhatsApp
 * al paciente usando la plantilla configurada.
 *
 * CÓMO PROGRAMARLO (ejecútalo cada hora):
 *   0 * * * * /usr/bin/php /ruta/completa/a/BackEndCentro/controlador/cron_recordatorios_whatsapp.php >> /ruta/logs/recordatorios.log 2>&1
 *
 * Pídele a tu proveedor de hosting acceso a "Cron Jobs" (cPanel, Plesk, etc.)
 * si no sabes cómo agregarlo. Sin esto, los recordatorios NO se envían solos.
 *
 * SEGURIDAD: este archivo también puede ejecutarse por navegador, pero solo
 * si se indica la clave secreta configurada abajo (evita que cualquiera lo
 * dispare repetidamente desde afuera). Lo ideal es correrlo solo por CLI (cron).
 */

// Cambia esta clave por una propia si vas a permitir la ejecución vía navegador/URL
define('CLAVE_SECRETA_CRON', 'CAMBIA_ESTA_CLAVE_2026');

$es_cli = (php_sapi_name() === 'cli');

if (!$es_cli) {
    if (!isset($_GET['clave']) || $_GET['clave'] !== CLAVE_SECRETA_CRON) {
        http_response_code(403);
        die('Acceso no autorizado.');
    }
}

require_once(__DIR__ . "/../config/global.php");
require_once(__DIR__ . "/../config/class_baseDatos.php");
require_once(__DIR__ . "/../config/class_crud.php");
require_once(__DIR__ . "/../config/class_whatsapp.php");

$crud = new crud();

function log_msg($msg) {
    echo '[' . date('Y-m-d H:i:s') . '] ' . $msg . "\n";
}

// ===================== LEER CONFIGURACIÓN ===================== //

$array_campo_pk = array('V_ID');
$array_valor_pk = array('1');
$array_config = $crud->fila_recuperar(DEF_TABLA_CONFIG_WHATSAPP, $array_campo_pk, $array_valor_pk);

if ($array_config === null || $array_config['V_FLAG_ACTIVO'] != '1') {
    log_msg('Recordatorios desactivados o sin configurar. No se hace nada.');
    exit;
}

$whatsapp = new WhatsAppSender(
    $array_config['V_PHONE_NUMBER_ID'],
    $array_config['V_ACCESS_TOKEN'],
    $array_config['V_NOMBRE_TEMPLATE'],
    $array_config['V_IDIOMA_TEMPLATE']
);

$li_horas_anticipacion = intval($array_config['N_HORAS_ANTICIPACION']);

// ===================== BUSCAR CITAS A RECORDAR ===================== //

// Ventana de tiempo: desde ahora hasta (ahora + horas de anticipación).
// Se usa un margen de 1 hora hacia atrás para no perder citas si el cron
// no corrió exactamente a tiempo.
$ls_desde = date('Y-m-d H:i:s', strtotime('-1 hour'));
$ls_hasta = date('Y-m-d H:i:s', strtotime("+$li_horas_anticipacion hours"));

$ls_cond = "V_FLAG_ESTADO = '1'
            AND V_ESTADO_CITA IN ('PRO','CON')
            AND CONCAT(D_FEC_CITA, ' ', D_HORA_INICIO) BETWEEN '$ls_desde' AND '$ls_hasta'
            AND NOT EXISTS (
                SELECT 1 FROM " . DEF_TABLA_RECORDATORIO . " r
                WHERE r.N_COD_CITA = " . DEF_TABLA_CITA . ".N_COD_CITA
                  AND r.V_TIPO = 'PACIENTE'
                  AND r.V_ESTADO_ENVIO = 'OK'
            )";

$array_citas = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . " WHERE $ls_cond", 'D_FEC_CITA, D_HORA_INICIO', 'A', 0, 500);

if (!$array_citas || $array_citas->num_rows == 0) {
    log_msg('No hay citas pendientes de recordatorio en este momento.');
    exit;
}

log_msg('Citas encontradas para recordar: ' . $array_citas->num_rows);

while ($row = mysqli_fetch_assoc($array_citas)) {

    // Datos del paciente
    $array_campo_pk = array('N_COD_PACIENTE');
    $array_valor_pk = array($row['N_COD_PACIENTE']);
    $ls_pac_nombre = $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_NOMBRES');
    $ls_pac_movil  = $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_MOVIL');

    // Datos del especialista
    $array_campo_pk = array('N_COD_ESPECIALISTA');
    $array_valor_pk = array($row['N_COD_ESPECIALISTA']);
    $ls_esp_nombre = $crud->fila_recuperar_campo(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO') . ' ' .
                     $crud->fila_recuperar_campo(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');

    if (empty($ls_pac_movil)) {
        log_msg("Cita {$row['N_COD_CITA']}: paciente sin celular registrado, se omite.");
        continue;
    }

    // Parámetros para la plantilla: {{1}}=nombre, {{2}}=fecha, {{3}}=hora, {{4}}=especialista
    $array_parametros = array(
        $ls_pac_nombre,
        date('d/m/Y', strtotime($row['D_FEC_CITA'])),
        substr($row['D_HORA_INICIO'], 0, 5),
        $ls_esp_nombre
    );

    $resultado = $whatsapp->enviarPlantilla($ls_pac_movil, $array_parametros);

    // Registrar en el log (evita reenviar, y deja rastro de éxito/error)
    $array_campo = array('N_COD_CITA', 'V_TIPO', 'D_FEC_ENVIO', 'V_ESTADO_ENVIO', 'V_RESPUESTA');
    $array_valor = array(
        $row['N_COD_CITA'],
        'PACIENTE',
        date('Y-m-d H:i:s'),
        $resultado['exito'] ? 'OK' : 'ERROR',
        substr($resultado['respuesta'], 0, 2000)
    );
    $crud->fila_registrar(DEF_TABLA_RECORDATORIO, $array_campo, $array_valor, '0');

    if ($resultado['exito']) {
        log_msg("Cita {$row['N_COD_CITA']}: recordatorio enviado a $ls_pac_nombre ($ls_pac_movil).");
    } else {
        log_msg("Cita {$row['N_COD_CITA']}: FALLÓ el envío a $ls_pac_nombre. Detalle: " . $resultado['respuesta']);
    }
}

log_msg('Proceso de recordatorios finalizado.');

?>
