<?php
ob_start();
session_start();
require_once("../modelo/class_usuario.php");
require_once("../config/global.php");
?>
<?php
	$error = '';
	$success = '';
	if( !empty( $_POST )){
		try {
			$user_obj = new usuario();
			$user_obj->f_usuario_restauraclave( $_POST );
			$success = RECUPERAR_OK;
		} catch (Exception $e) {
			$error = $e->getMessage();
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
  <head>

	<!-- Base href: necesario porque esta vista también se sirve desde la raíz (index.php) -->
	<base href="/vista/">

	<!-- Metas Bootstrap -->
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

	<!-- Meta String -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<!-- Título -->
    <title>:: <?php echo TITULO_PANEL?> ::</title>

	<!-- Favicon -->
    <link rel="shortcut icon" href="../recursos/images/favicon.ico" type="image/x-icon" />

	<!-- Bootstrap 3.3.7 -->
	<link rel="stylesheet" href="../recursos/css/bootstrap.min.css">

	<!-- Font Awesome -->
	<link rel="stylesheet" href="../recursos/css/font-awesome.min.css">
	<link href='http://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>

	<!-- Estilo Personalizado -->
    <link rel="stylesheet" href="../recursos/css/style_login_admin.css">

	<!-- jQuery 3 -->
	<script src="../recursos/js/jquery.min.js"></script>

	<!-- Bootstrap 3.3.7 -->
	<script src="../recursos/js/bootstrap.min.js"></script>

  </head>

  <body>
	<div class="container">
		<div class="login-form">
			<div class="form-header">
				<h4>Recuperar Contraseña</h4>
				<small class="text-muted">Centro Terapéutico</small>
			</div>

			<?php if( !empty( $error ) ): ?>
				<div class="alert alert-danger"><?php echo $error; ?></div>
			<?php endif; ?>

			<?php if( !empty( $success ) ): ?>
				<div class="alert alert-success"><?php echo $success; ?></div>
			<?php endif; ?>

			<?php if( empty( $success ) ): ?>
			<form id="forget-password-form" method="post" class="form-signin" role="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off">

				<div class="form-group">
					<label for="id_usuario">Usuario</label>
					<input name="id_usuario" id="id_usuario" type="text" class="form-control" placeholder="Ingrese su usuario" maxlength="10" required autofocus>
				</div>

				<p class="text-muted"><small>Se generará una nueva contraseña y se enviará al correo que tiene registrado en el sistema.</small></p>

				<button class="btn btn-block bt-login" type="submit" id="submit_btn">Enviar nueva contraseña</button>

			</form>
			<?php endif; ?>

			<div class="form-footer">
				<div class="row">
					<div class="col-xs-12 text-center">
						<i class="fa fa-arrow-left"></i>
						<a href="login_admin.php">Volver al inicio de sesión</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /container -->
  </body>

</html>
<?php ob_end_flush(); ?>
