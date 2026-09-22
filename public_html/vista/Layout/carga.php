<?php
@session_start();

function f_admin_carga() {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="es">
<head>

    <!-- Metas Bootstrap -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	
	<!-- Meta String -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<!-- Titulo -->
    <title>:: <?php echo TITULO_PANEL?> ::</title>

	<!-- Favicon -->
    <link rel="shortcut icon" href="../../../website/recursos/images/favicon.ico" type="image/x-icon" />
	
	<!-- Bootstrap 3.3.7 -->
	<link rel="stylesheet" href="../recursos/css/bootstrap.min.css">

	<!-- Font Awesome -->
	<link rel="stylesheet" href="../recursos/css/font-awesome.min.css">
	
	<!-- DataTables -->
	<link rel="stylesheet" href="../recursos/css/dataTables.bootstrap.min.css">
	
	<!-- DataTables Select -->
	<link rel="stylesheet" href="../recursos/css/select.dataTables.min.css">
	<!-- <link rel="stylesheet" href="../recursos/css/jquery.dataTables.min.css">	Genera conflictos-->

	<!-- Ionicons -->
	<link rel="stylesheet" href="../recursos/css/ionicons.min.css">

	<!-- Theme style -->
	<link rel="stylesheet" href="../recursos/css/AdminLTE.min.css">
	<link rel="stylesheet" href="../recursos/css/skins/_all-skins.min.css">
	
	<!-- bootstrap datepicker -->
	<link rel="stylesheet" href="../recursos/css/bootstrap-datepicker.min.css">

	<!-- Ckeditor -->
	<script src="../ckeditor/ckeditor.js"></script>

    <!-- Estilo Personalizado -->
    <link rel="stylesheet" href="../recursos/css/style_admin.css">
	
	<!-- Google Font -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
	
</head>
<body class="hold-transition skin-blue sidebar-mini">
<?php
}