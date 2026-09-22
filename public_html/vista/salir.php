<?php
ob_start();
session_start();

require_once("../modelo/class_usuario.php"); 
$user_obj = new usuario();
$data = $user_obj->f_usuario_logout();

?>