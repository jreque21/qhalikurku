<?php
/*
Proyecto 	: Sistema web - Backend 
Fecha 		: 2026-04-01
Autor 		: Johnny Reque
Proposito	: Variables Globales
*/

date_default_timezone_set('America/Lima');

// Rutas
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/');
define('CONTROLADOR_PATH', ROOT_PATH.'controlador/');
define('MODELO_PATH', ROOT_PATH.'modelo/');
define('VISTA_PATH', ROOT_PATH.'vista/');
define('DEF_PATH_ADMIN','../config/lib_include_admin.php');

// Constantes
define("TITULO_PANEL", "SISTEMA CENTRO DE TERAPIA");
define("SUBTITULO_PANEL", "Acceso de Usuarios");
define("COPYRIGHT_AUTOR", "Terapia");
define("COPYRIGHT_WEB", "http://www.jreque.com");
define("COPYRIGHT", "2026");
define("VERSION", "1.0");
define("SOPORTE_AUTOR", "Johnny Reque");
define("SOPORTE_EMAIL", "johnnyreque@gmail.com");
define("SOPORTE_MOVIL", "(+51) 977137699");
define("URL_WEB", "http://www.jmtalentgroup.com");
define("DEF_URL_LOGIN", "login_admin.php");
define("DEF_URL_LOGIN_INTRANET", "login_intranet.php");
define("TITULO_INTRANET", "INTRANET CENTRO DE TERAPIA");
define("DEF_URL_LOGIN_SUBTITULO", "AFILIADO A LA FEDERACIÓN DEPORTIVA PERUANA DE TAEKWONDO");											  

// Textos de Mantenimiento
define('DEF_MSG_FORM_NUEVO', 'NUEVO');
define('DEF_MSG_FORM_EDICION', 'EDICION');
define('DEF_MSG_FORM_LISTADO', 'LISTADO');
define('DEF_MSG_FORM_SELECCION', 'SELECCION MULTIPLE');
define('DEF_MSG_FORM_REPORTE', 'FILTROS DEL REPORTE');
define('DEF_MSG_SIN_REGISTROS', 'Aviso del Sistema, no existen registros disponibles.');
define('DEF_MSG_FORM_AVISO', 'Aviso del Sistema, Upps! acción no permitida, ');
define('DEF_MSG_FORM_AVISO_OK', 'Aviso del Sistema, Muy Bien! ');
define('DEF_TIPOUSER_PAC', 'USER_PAC');
//define('DEF_TIPOUSER_ESTEXT', 'EST_EXT');
define('DEF_TIPOUSER_ESP', 'USER_ESP');
//define('DEF_EDAD_NIN_MIN', '3');
//define('DEF_EDAD_NIN_MAX', '11');
//define('DEF_EDAD_ADO_MIN', '12');
//define('DEF_EDAD_ADO_MAX', '17');
//define('DEF_EDAD_JOV_MIN', '18');
//define('DEF_EDAD_JOV_MAX', '40');

// ++++++++++++++++++++++++++++++++++++ //
// Rutas HTML
// ++++++++++++++++++++++++++++++++++++ //
define('DEF_PATH_HTML_EMPRESA','mae_empresa_html.php');
define('DEF_PATH_HTML_TIPO_DOC','mae_tipodoc_html.php');
define('DEF_PATH_HTML_MONEDA','mae_moneda_html.php');
define('DEF_PATH_HTML_FORMAPAGO','mae_formapago_html.php');
define('DEF_PATH_HTML_MEDIOPAGO','mae_mediopago_html.php');
define('DEF_PATH_HTML_ESPECIALIDAD','mae_especialidad_html.php');
define('DEF_PATH_HTML_TIPOTERAPIA','mae_tipoterapia_html.php');
define('DEF_PATH_HTML_TARIFA','mae_tarifa_html.php');
define('DEF_PATH_HTML_SEDE','mae_sede_html.php');
define('DEF_PATH_HTML_ESPECIALISTA','mae_especialista_html.php');
define('DEF_PATH_HTML_PACIENTE','mae_paciente_html.php');
define('DEF_PATH_HTML_CITA','mov_cita_html.php');
define('DEF_PATH_HTML_TRATAMIENTO','mov_tratamiento_html.php');
define('DEF_PATH_HTML_SESION','mov_sesion_html.php');
define('DEF_PATH_HTML_SEDEESPECIALISTA','mae_sedeespecialista_html.php');

// ++++++++++++++++++++++++++++++++++++ //
define('DEF_PATH_HTML_PAGO','mov_pago_html.php');
define('DEF_PATH_HTML_COMPROBANTE','mov_comprobante_html.php');
define('DEF_PATH_HTML_RPT_REPORTES','rpt_reportes_html.php');
define('DEF_PATH_HTML_RPT_INGRESOS','rpt_ingresos_html.php');
define('DEF_PATH_HTML_RPT_CITAS','rpt_citas_html.php');
define('DEF_PATH_HTML_RPT_PRODUCTIVIDAD','rpt_productividad_html.php');
define('DEF_PATH_HTML_HISTORIA','mov_historia_html.php');

//define('DEF_PATH_HTML_HORARIO','mov_horario_html.php');
//define('DEF_PATH_HTML_HORARIOPROG','mov_horarioclase_html.php');
//define('DEF_PATH_HTML_ACADEMIA','mae_academia_html.php');

// ++++++++++++++++++++++++++++++++++++ //
//define('DEF_PATH_HTML_RPT_EXTXSEDE','rpt_estudiante_x_sede_html.php');

// ++++++++++++++++++++++++++++++++++++ //
define('DEF_PATH_HTML_ROL','mae_rol_html.php');
define('DEF_PATH_HTML_USUARIO','mae_usuario_html.php');
define('DEF_PATH_HTML_ROLUSUARIO','mae_rolusuario_html.php');
define('DEF_PATH_HTML_ROLOPCION','mae_rolopcion_html.php');

// ++++++++++++++++++++++++++++++++++++ //
// Tablas
// ++++++++++++++++++++++++++++++++++++ //
define('DEF_TABLA_EMPRESA', 'MAE_EMPRESA');
define('DEF_TABLA_PANEL', 'PANEL_ADMIN');
define('DEF_TABLA_TIPO_DOC', 'MAE_TIPO_DOC');
define('DEF_TABLA_MONEDA', 'MAE_MONEDA');
define('DEF_TABLA_FORMAPAGO', 'MAE_FORMA_PAGO');
define('DEF_TABLA_MEDIOPAGO', 'MAE_MEDIO_PAGO');
define('DEF_TABLA_ESPECIALIDAD', 'MAE_ESPECIALIDAD');
define('DEF_TABLA_TIPOTERAPIA', 'MAE_TIPO_TERAPIA');
define('DEF_TABLA_PAIS', 'MAE_PAIS');
define('DEF_TABLA_SEDE', 'MAE_SEDE');
define('DEF_TABLA_ESPECIALISTA', 'MAE_ESPECIALISTA');
define('DEF_TABLA_SEDEESPECIALISTA', 'MAE_SEDE_ESPECIALISTA');
define('DEF_TABLA_PACIENTE', 'MAE_PACIENTE');

// ++++++++++++++++++++++++++++++++++++ //
define('DEF_TABLA_CITA', 'MOV_CITA');
define('DEF_TABLA_TRATAMIENTO', 'MOV_TRATAMIENTO');
define('DEF_TABLA_SESION', 'MOV_SESION');
define('DEF_TABLA_HISTORIA_CLINICA', 'MOV_HISTORIA_CLINICA');
define('DEF_TABLA_PAGO', 'MOV_PAGO');
define('DEF_TABLA_COMPROBANTE', 'MOV_COMPROBANTE');
define('DEF_TABLA_RPT_REPORTES', 'RPT_REPORTES');
define('DEF_TABLA_RPT_INGRESOS', 'RPT_INGRESOS');
define('DEF_TABLA_RPT_CITAS', 'RPT_CITAS');
define('DEF_TABLA_RPT_PRODUCTIVIDAD', 'RPT_PRODUCTIVIDAD');
define('DEF_TABLA_CONFIG_WHATSAPP', 'MAE_CONFIG_WHATSAPP');
define('DEF_TABLA_RECORDATORIO', 'MOV_RECORDATORIO');
define('DEF_TABLA_ACADEMIA', 'MAE_ACADEMIA');

// ++++++++++++++++++++++++++++++++++++ //
define('DEF_TABLA_RPT_ESTXSEDE', 'RPT_EST_X_SEDE');

// ++++++++++++++++++++++++++++++++++++ //
define('DEF_TABLA_MENU_NIVEL', 'MAE_MENU_NIVEL');
define('DEF_TABLA_MENU_OPC', 'MAE_MENU_OPCION');
define('DEF_TABLA_ROL', 'MAE_ROL');
define('DEF_TABLA_USUARIO', 'MAE_USUARIO');
define('DEF_TABLA_TIPOUSUARIO', 'MAE_TIPO_USUARIO');
define('DEF_TABLA_ROLUSUARIO', 'MAE_ROL_USUARIO');
define('DEF_TABLA_ROLOPCION', 'MAE_ROL_OPCION');

// ++++++++++++++++++++++++++++++++++++ //
// Tamaño dimensiones para fotos
// ++++++++++++++++++++++++++++++++++++ //
define('DEF_UPLOAD_SEDE_DIR', 'sede');
define("DEF_UPLOAD_SEDE_W", 400);
define("DEF_UPLOAD_SEDE_H", 400);
// ++++++++++++++++++++++++++++++++++++ //
define('DEF_UPLOAD_ESPECIALISTA_DIR', 'especialista');
define("DEF_UPLOAD_ESPECIALISTA_W", 160);
define("DEF_UPLOAD_ESPECIALISTA_H", 160);
// ++++++++++++++++++++++++++++++++++++ //
define('DEF_UPLOAD_PACIENTE_DIR', 'paciente');
define("DEF_UPLOAD_PACIENTE_W", 160);
define("DEF_UPLOAD_PACIENTE_H", 160);
// ++++++++++++++++++++++++++++++++++++ //
define('DEF_UPLOAD_USUARIO_DIR', 'usuario');
define("DEF_UPLOAD_USUARIO_W", 160);
define("DEF_UPLOAD_USUARIO_H", 160);
// ++++++++++++++++++++++++++++++++++++ //
define('DEF_UPLOAD_EMPRESA_DIR', 'images');
define("DEF_UPLOAD_EMPRESA_W", 160);
define("DEF_UPLOAD_EMPRESA_H", 160);
// ++++++++++++++++++++++++++++++++++++ //

define('DIR_UPLOAD_INFO', 'info');
define("IMG_LOGO_ANCHO", 128);
define("IMG_LOGO_ALTO", 128);

// ++++++++++++++++++++++++++++++++++++ //



?>