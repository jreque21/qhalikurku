<?php
ob_start();
session_start();
require_once("../modelo/class_usuario.php");
$user_obj = new usuario();
$data = $user_obj->f_usuario_logout();

// Redirección absoluta: garantiza volver al login sin importar si esta página
// fue alcanzada desde /vista/panel_admin.php o desde la raíz (index.php).
header('Location: /vista/login_admin.php');
exit;
?>
