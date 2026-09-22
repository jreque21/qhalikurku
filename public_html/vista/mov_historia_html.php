<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Historia Clínica (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta, $as_cod_paciente_filtro = ''){

	$url_editar	= "mov_historia_editar.php";
	$url_nuevo	= "mov_historia_nuevo.php?id_cod_paciente=" . $as_cod_paciente_filtro;
	$url_pacientes = "mae_paciente_lista.php";

	$crud = new crud();

	$array_paciente = null;
	if (!empty($as_cod_paciente_filtro)) {
		$array_campo_pk = array('N_COD_PACIENTE');
		$array_valor_pk = array($as_cod_paciente_filtro);
		$array_paciente = $crud->fila_recuperar(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk);
	}

	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo;?>
			</h1>
			<ol class="breadcrumb">
				<li><a href="<?php echo DEF_URL_LOGIN;?>"><i class="fa fa-home"></i> Inicio</a></li>
				<li><a href="<?php echo $url_pacientes;?>">Pacientes</a></li>
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

			<?php if ($array_paciente === null) { ?>
				<div class="alert alert-warning">
					Para ver la historia clínica de un paciente, ábrelo desde el listado de
					<a href="<?php echo $url_pacientes;?>">Pacientes</a> y usa el botón "Ver Historia Clínica".
				</div>
			<?php } else { ?>

			<div class="row">
				<section class="col-lg-12 connectedSortable">

					<div class="panel panel-default">
						<div class="panel-body">
							<h4><?php echo $array_paciente["V_APE_PATERNO"].' '.$array_paciente["V_APE_MATERNO"].', '.$array_paciente["V_NOMBRES"]; ?></h4>
							<?php echo f_get_edad($array_paciente["D_FEC_NACIMIENTO"]); ?> años
						</div>
					</div>

					<?php
					// Última entrada = alergias / enfermedades / antecedentes vigentes
					$array_campo_pk = array('N_COD_PACIENTE', 'V_FLAG_ESTADO');
					$array_valor_pk = array($as_cod_paciente_filtro, '1');
					$lr_ultima = $crud->fila_listar(DEF_TABLA_HISTORIA_CLINICA, $array_campo_pk, $array_valor_pk, 'N_COD_HISTORIA', 'D', 0, 1);
					$array_ultima = $lr_ultima ? mysqli_fetch_assoc($lr_ultima) : null;

					if ($array_ultima && (!empty($array_ultima['V_ALERGIAS']) || !empty($array_ultima['V_ENFERMEDADES']))) { ?>
						<div class="alert alert-danger">
							<?php if (!empty($array_ultima['V_ALERGIAS'])) { ?>
								<strong><i class="fa fa-exclamation-triangle"></i> ALERGIAS:</strong> <?php echo $array_ultima['V_ALERGIAS']; ?><br>
							<?php } ?>
							<?php if (!empty($array_ultima['V_ENFERMEDADES'])) { ?>
								<strong><i class="fa fa-heartbeat"></i> ENFERMEDADES / CONDICIONES:</strong> <?php echo $array_ultima['V_ENFERMEDADES']; ?>
							<?php } ?>
						</div>
					<?php } ?>

					<div class="panel panel-primary">
						<div class="box-header"><i class="fa fa-file-text-o"></i> Historial de Entradas</div>
						<div class="panel-body">
							<?php
							$array_campo_pk = array('N_COD_PACIENTE', 'V_FLAG_ESTADO');
							$array_valor_pk = array($as_cod_paciente_filtro, '1');
							$array = $crud->fila_listar(DEF_TABLA_HISTORIA_CLINICA, $array_campo_pk, $array_valor_pk, 'D_FEC_CITA', 'D', 0, 200);

							if (!($array) || $array->num_rows == 0) {
								?>
								<div class="alert alert-warning">Aún no hay entradas de historia clínica para este paciente.</div>
								<?php
							} else {
								while ($row = mysqli_fetch_assoc($array)) {
									$url_editar_fila = $url_editar . "?id_codigo=" . intval($row["N_COD_HISTORIA"]);
									?>
									<div class="panel panel-default">
										<div class="panel-body">
											<strong><?php echo date('d/m/Y', strtotime($row["D_FEC_CITA"])); ?></strong>
											<?php if (!empty($row["N_COD_CITA"])) { ?>
												&nbsp;<span class="label label-primary"><i class="fa fa-calendar"></i> Cita #<?php echo intval($row["N_COD_CITA"]); ?></span>
											<?php } elseif (!empty($row["N_COD_SESION"])) { ?>
												&nbsp;<span class="label label-info"><i class="fa fa-stethoscope"></i> Sesión #<?php echo intval($row["N_COD_SESION"]); ?></span>
											<?php } else { ?>
												&nbsp;<span class="label label-default">Manual</span>
											<?php } ?>
											&nbsp;<a href="<?php echo $url_editar_fila; ?>" class="pull-right"><i class="fa fa-edit"></i> Editar</a>
											<hr style="margin:8px 0;">
											<?php if (!empty($row["V_DIAGNOSTICO"])) { ?>
												<p><strong>Diagnóstico:</strong> <?php echo $row["V_DIAGNOSTICO"]; ?></p>
											<?php } ?>
											<?php if (!empty($row["V_OBSERVACION"])) { ?>
												<p><strong>Observación:</strong> <?php echo $row["V_OBSERVACION"]; ?></p>
											<?php } ?>
											<?php if (!empty($row["V_ANTECEDENTES"])) { ?>
												<p><strong>Antecedentes:</strong> <?php echo $row["V_ANTECEDENTES"]; ?></p>
											<?php } ?>
											<?php if (!empty($row["V_ALERGIAS"])) { ?>
												<p><strong>Alergias:</strong> <?php echo $row["V_ALERGIAS"]; ?></p>
											<?php } ?>
											<?php if (!empty($row["V_ENFERMEDADES"])) { ?>
												<p><strong>Enfermedades:</strong> <?php echo $row["V_ENFERMEDADES"]; ?></p>
											<?php } ?>
										</div>
									</div>
									<?php
								}
							}
							?>
						</div>
					</div>

					<a class="btn btn-primary" href="<?php echo $url_nuevo?>" role="button">
						<span class="glyphicon glyphicon-plus"></span>&nbsp;Nueva Entrada
					</a>

					</br>

				</section>
			</div>

			<?php } ?>

		</section>

	</div>

<?php
}

// Formulario de Registro / Edición
function f_formulario($as_titulo, $as_icono, $as_msgRpta, $array = "", $as_cod_paciente = '') {

	$lb_edit = is_array($array);
	$crud = new crud();

	$li_cod_paciente = $lb_edit ? $array["N_COD_PACIENTE"] : $as_cod_paciente;

	$url_lista      = "mov_historia_lista.php?id_cod_paciente_filtro=" . $li_cod_paciente;
	$url_registrar  = "../controlador/mov_historia_registrar.php";
	$url_actualizar = "../controlador/mov_historia_actualizar.php";
	$url_eliminar   = "../controlador/mov_historia_eliminar.php";

	$ls_modo = $lb_edit ? DEF_MSG_FORM_EDICION : DEF_MSG_FORM_NUEVO;

	// Datos del paciente
	$array_campo_pk = array('N_COD_PACIENTE');
	$array_valor_pk = array($li_cod_paciente);
	$array_paciente = $crud->fila_recuperar(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk);

	// Si es nuevo, precargar antecedentes/alergias/enfermedades de la última entrada (para no reescribir todo cada vez)
	if (!$lb_edit) {
		$array_campo_pk = array('N_COD_PACIENTE', 'V_FLAG_ESTADO');
		$array_valor_pk = array($li_cod_paciente, '1');
		$lr_ultima = $crud->fila_listar(DEF_TABLA_HISTORIA_CLINICA, $array_campo_pk, $array_valor_pk, 'N_COD_HISTORIA', 'D', 0, 1);
		$array_ultima = $lr_ultima ? mysqli_fetch_assoc($lr_ultima) : null;
	}

	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo;?> &nbsp;
				<small><?php echo $array_paciente["V_APE_PATERNO"].' '.$array_paciente["V_APE_MATERNO"].', '.$array_paciente["V_NOMBRES"]; ?></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="<?php echo DEF_URL_LOGIN;?>"><i class="fa fa-home"></i> Inicio</a></li>
				<li class="active"><a href="<?php echo $url_lista?>">Volver a Historia Clínica</a></li>
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

			<div class="row">
				<section class="col-lg-12 connectedSortable">
					<div class="panel panel-primary">
						<div class="box-header"><i class="fa fa-edit"></i> <?php echo strtoupper($ls_modo); ?></div>
						<div class="panel-body">

							<form id="form_mtto" role="form" method="post" action="" autocomplete="off">

								<input type="hidden" name="id_cod_paciente" value="<?php echo $li_cod_paciente; ?>">
								<input type="hidden" name="id_cod_paciente_hide" value="<?php echo $li_cod_paciente; ?>">
								<input type="hidden" name="id_cod_historia_hide" value="<?php echo $lb_edit?$array["N_COD_HISTORIA"]:""; ?>">

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fec_cita">Fecha</label>
											<input type="date" class="form-control" id="id_fec_cita" name="id_fec_cita" required
												value="<?php echo $lb_edit?$array["D_FEC_CITA"]:date('Y-m-d'); ?>">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label for="id_diagnostico_hc">Diagnóstico</label>
											<textarea class="form-control" id="id_diagnostico_hc" name="id_diagnostico" maxlength="300" rows="2"
												placeholder="(Opcional) Diagnóstico de esta consulta"><?php echo $lb_edit?$array["V_DIAGNOSTICO"]:""; ?></textarea>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label for="id_observacion">Observación de esta Consulta</label>
											<textarea class="form-control" id="id_observacion" name="id_observacion" maxlength="300" rows="3"
												placeholder="(*) Ejemplo: Paciente refiere dolor en zona lumbar desde hace 2 semanas"><?php echo $lb_edit?$array["V_OBSERVACION"]:""; ?></textarea>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_antecedentes">Antecedentes</label>
											<textarea class="form-control" id="id_antecedentes" name="id_antecedentes" maxlength="300" rows="3"><?php
												echo $lb_edit ? $array["V_ANTECEDENTES"] : (isset($array_ultima['V_ANTECEDENTES']) ? $array_ultima['V_ANTECEDENTES'] : '');
											?></textarea>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_alergias">Alergias</label>
											<textarea class="form-control" id="id_alergias" name="id_alergias" maxlength="300" rows="3"><?php
												echo $lb_edit ? $array["V_ALERGIAS"] : (isset($array_ultima['V_ALERGIAS']) ? $array_ultima['V_ALERGIAS'] : '');
											?></textarea>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_enfermedades">Enfermedades / Condiciones</label>
											<textarea class="form-control" id="id_enfermedades" name="id_enfermedades" maxlength="300" rows="3"><?php
												echo $lb_edit ? $array["V_ENFERMEDADES"] : (isset($array_ultima['V_ENFERMEDADES']) ? $array_ultima['V_ENFERMEDADES'] : '');
											?></textarea>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-12">
										<?php if ($lb_edit) { ?>
											<input class="btn btn-success" type="submit" id="btn_actualizar" value="Actualizar"
												onclick="this.form.action='<?php echo $url_actualizar?>'">
											<input class="btn btn-danger" type="submit" id="btn_eliminar" value="Eliminar" formnovalidate
												onclick="return confirm('¿Eliminar esta entrada? Solo se permite si es la última registrada.') && (this.form.action='<?php echo $url_eliminar?>')">
											<input class="btn btn-default" type="submit" id="btn_cancelar" value="Volver" formnovalidate
												onclick="this.form.action='<?php echo $url_lista?>'">
										<?php } else { ?>
											<input class="btn btn-success" type="submit" id="btn_agregar" value="Guardar"
												onclick="this.form.action='<?php echo $url_registrar?>'">
											<input class="btn btn-default" type="submit" id="btn_cancelar" value="Volver" formnovalidate
												onclick="this.form.action='<?php echo $url_lista?>'">
										<?php } ?>
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
