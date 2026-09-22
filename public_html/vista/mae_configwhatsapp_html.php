<?php

// Incluye Librería BD
require_once("../config/funciones.php");

function f_formulario($as_titulo, $as_icono, $as_msgRpta, $array) {

	$crud = new crud();
	$url_actualizar = "../controlador/mae_configwhatsapp_actualizar.php";

	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo;?>
			</h1>
			<ol class="breadcrumb">
				<li><a href="<?php echo DEF_URL_LOGIN;?>"><i class="fa fa-home"></i> Inicio</a></li>
			</ol>
		</section>

		<section class="content">

			<?php
			if($as_msgRpta) {
				if (substr($as_msgRpta,0,2)=='OK'){
					echo "<div class='alert alert-success'>".DEF_MSG_FORM_AVISO_OK.substr($as_msgRpta,3)."</div>";
				}else{
					echo "<div class='alert alert-danger'>".DEF_MSG_FORM_AVISO.$as_msgRpta."</div>";
				}
			}
			?>

			<div class="alert alert-info">
				<strong><i class="fa fa-info-circle"></i> Antes de activar:</strong> necesitas tu propia cuenta de
				<a href="https://business.facebook.com/" target="_blank">Meta for Developers</a> con WhatsApp Business
				configurado, un número verificado, y una <strong>plantilla de mensaje aprobada</strong> por Meta
				(no se puede mandar texto libre a pacientes que no te escribieron primero). Revisa los comentarios en
				<code>config/class_whatsapp.php</code> para el detalle paso a paso.
			</div>

			<div class="row">
				<section class="col-lg-12 connectedSortable">
					<div class="panel panel-primary">
						<div class="box-header"><i class="fa fa-whatsapp"></i> Configuración de WhatsApp Business API</div>
						<div class="panel-body">

							<form role="form" method="post" action="<?php echo $url_actualizar; ?>" autocomplete="off">

								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label for="id_phone_number_id">Phone Number ID</label>
											<input type="text" class="form-control" id="id_phone_number_id" name="id_phone_number_id"
												placeholder="Ej: 109876543210987"
												value="<?php echo $array["V_PHONE_NUMBER_ID"]; ?>">
											<span class="help-block">Lo obtienes en Meta for Developers &gt; tu app &gt; WhatsApp &gt; API Setup.</span>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label for="id_access_token">Access Token</label>
											<input type="password" class="form-control" id="id_access_token" name="id_access_token"
												placeholder="Token permanente (no el de prueba de 24h)"
												value="<?php echo $array["V_ACCESS_TOKEN"]; ?>">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_nombre_template">Nombre de la Plantilla</label>
											<input type="text" class="form-control" id="id_nombre_template" name="id_nombre_template"
												value="<?php echo $array["V_NOMBRE_TEMPLATE"]; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_idioma_template">Idioma de la Plantilla</label>
											<input type="text" class="form-control" id="id_idioma_template" name="id_idioma_template"
												value="<?php echo $array["V_IDIOMA_TEMPLATE"]; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_horas_anticipacion">Horas de Anticipación</label>
											<input type="number" min="1" max="72" class="form-control" id="id_horas_anticipacion" name="id_horas_anticipacion"
												value="<?php echo $array["N_HORAS_ANTICIPACION"]; ?>">
											<span class="help-block">Cuántas horas antes de la cita se envía el recordatorio.</span>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_flag_activo">Estado</label>
											<select class="form-control" id="id_flag_activo" name="id_flag_activo">
												<option value="1" <?php echo $array["V_FLAG_ACTIVO"]=='1'?'selected':''; ?>>Activo</option>
												<option value="0" <?php echo $array["V_FLAG_ACTIVO"]=='0'?'selected':''; ?>>Inactivo</option>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-12">
										<input class="btn btn-success" type="submit" value="Guardar Configuración">
									</div>
								</div>

							</form>

						</div>
					</div>
				</section>
			</div>

		</section>

	</div>

<?php
}

?>
