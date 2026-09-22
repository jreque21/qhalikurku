<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Sesiones (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta, $as_cod_tratamiento_filtro = ''){

	// Enlaces
	$url_editar		= "mov_sesion_editar.php";
	$url_eliminar	= "../controlador/mov_sesion_eliminar.php";
	$url_tratamientos = "mov_tratamiento_lista.php";

	// Instanciar clase
	$crud = new crud();
	$bd   = new baseDatos();

	$array_tratamiento = null;
	if (!empty($as_cod_tratamiento_filtro)) {
		$array_campo_pk	= array('N_COD_TRATAMIENTO');
		$array_valor_pk	= array($as_cod_tratamiento_filtro);
		$array_tratamiento = $crud->fila_recuperar(DEF_TABLA_TRATAMIENTO, $array_campo_pk, $array_valor_pk);
	}

	$url_nuevo = "mov_sesion_nuevo.php?id_cod_tratamiento=" . $as_cod_tratamiento_filtro;

	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<!-- Cabecera de Sección Contenido -->
		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo;?>
			</h1>
			<ol class="breadcrumb">
				<li><a href="<?php echo DEF_URL_LOGIN;?>"><i class="fa fa-home"></i> Inicio</a></li>
				<li><a href="<?php echo $url_tratamientos;?>">Tratamientos</a></li>
			</ol>
		</section>
		<!-- Fin de Cabecera de Sección Contenido -->

		<!-- Contenido -->
		<section class="content">

			<?php
			if($as_msgRpta) {
				if (substr($as_msgRpta,0,2)=='OK'){
					echo "<div class='alert alert-success'>";
						echo DEF_MSG_FORM_AVISO_OK.substr($as_msgRpta,3);
					echo "</div>";
				}else{
					echo "<div class='alert alert-danger'>";
						echo DEF_MSG_FORM_AVISO.$as_msgRpta;
					echo "</div>";
				}
			}
			?>

			<?php if ($array_tratamiento === null) { ?>
				<div class="alert alert-warning">
					Para ver las sesiones de un tratamiento, ábrelo desde el listado de
					<a href="<?php echo $url_tratamientos;?>">Tratamientos</a> y usa el botón "Ver Sesiones".
				</div>
			<?php } else { ?>

			<!-- Fila Principal -->
			<div class="row">

				<!-- Columna Izquierda -->
				<section class="col-lg-12 connectedSortable">

					<?php
					// Datos del paciente / especialista para dar contexto
					$array_campo_pk	= array('N_COD_PACIENTE');
					$array_valor_pk	= array($array_tratamiento["N_COD_PACIENTE"]);
					$ls_pac = $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO') . ' ' .
							  $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO') . ', ' .
							  $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_NOMBRES');

					$array_campo_pk	= array('N_COD_TRATAMIENTO', 'V_FLAG_ESTADO');
					$array_valor_pk	= array($as_cod_tratamiento_filtro, '1');
					$li_realizadas = intval($crud->fila_contar(DEF_TABLA_SESION, $array_campo_pk, $array_valor_pk));
					$li_planificadas = intval($array_tratamiento["N_NUM_SESIONES"]);
					$li_porcentaje = $li_planificadas > 0 ? round(($li_realizadas / $li_planificadas) * 100) : 0;
					?>

					<div class="panel panel-default">
						<div class="panel-body">
							<strong>Paciente:</strong> <?php echo $ls_pac; ?> &nbsp;|&nbsp;
							<strong>ID Tratamiento:</strong> <?php echo intval($as_cod_tratamiento_filtro); ?> &nbsp;|&nbsp;
							<strong>Diagnóstico:</strong> <?php echo $array_tratamiento["V_DIAGNOSTICO"]; ?>
							<div class="progress" style="margin-top:10px; margin-bottom:0;">
								<div class="progress-bar progress-bar-success" role="progressbar" style="width: <?php echo $li_porcentaje; ?>%;">
									<?php echo $li_realizadas; ?> / <?php echo $li_planificadas; ?> sesiones
								</div>
							</div>
						</div>
					</div>

					<!-- Panel -->
					<div class="panel panel-primary">

						<!-- Cabecera -->
						<div class="box-header">
							<i class="fa fa-stethoscope"></i>
							MANTENIMIENTO - <?php echo DEF_MSG_FORM_LISTADO;?>
						</div>

						<!-- Cuerpo -->
						<div class="panel-body">

							<?php
							$array_campo_pk	= array('N_COD_TRATAMIENTO', 'V_FLAG_ESTADO');
							$array_valor_pk	= array($as_cod_tratamiento_filtro, '1');
							$array = $crud->fila_listar(DEF_TABLA_SESION, $array_campo_pk, $array_valor_pk, 'N_NUM_SESION', 'A', 0, 999);

							if (!($array) || $array->num_rows == 0) {
								?>
								<div class="alert alert-warning">
									<?php echo DEF_MSG_SIN_REGISTROS;?>
								</div>
								<?php
							}else {
								?>
								<div class="table-responsive">

									<table id="lista" class="table table-striped table-bordered table-hover">

										<thead>
											<tr>
												<th>N°</th>
												<th>Fecha</th>
												<th>Observación</th>
												<th>Evolución</th>
												<th>Acciones</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {

												$url_editar_fila = $url_editar."?id_codigo=".intval($row["N_COD_SESION"]);
												?>
												<tr>
													<td><?php echo intval($row["N_NUM_SESION"]); ?></td>
													<td><?php echo date('d/m/Y', strtotime($row["D_FEC_SESION"])); ?></td>
													<td><?php echo $row["V_OBSERVACION"]; ?></td>
													<td><?php echo $row["V_EVOLUCION"]; ?></td>
													<td align="center">
														<a href="<?php echo $url_editar_fila?>">
															<i class="fa fa-edit" title="Editar"></i>
														</a>
													</td>
												</tr>
												<?php
											}
											?>
										</tbody>

									</table>

								</div>

							<?php
							}
						?>
						<!-- Fin Cuerpo -->
						</div>

					</div>
					<!-- Fin Panel -->

					<?php if ($array_tratamiento["V_ESTADO_TRATAMIENTO"] == 'ACT' && $li_realizadas < $li_planificadas) { ?>
					<a class="btn btn-primary" href="<?php echo $url_nuevo?>" role="button">
						<span class="glyphicon glyphicon-plus"></span>&nbsp;Registrar Sesión
					</a>
					<?php } elseif ($li_realizadas >= $li_planificadas && $li_planificadas > 0) { ?>
					<div class="alert alert-info" style="display:inline-block;">Este tratamiento ya completó todas sus sesiones planificadas.</div>
					<?php } ?>

					</br>

				</section>
				<!-- Fin Columna Izquierda -->

			</div>
			<!-- Fin Fila Principal -->

			<?php } ?>

		</section>
		<!-- Fin Contenido -->

	</div>
	<!-- Fin sección Contenido -->

<?php
}

// Formulario de Registro / Edición
function f_formulario($as_titulo, $as_icono, $as_msgRpta, $array = "", $as_cod_tratamiento = '') {

	// Inicalizando variables
	$lb_edit = is_array($array);

	// Instanciar clase
	$crud = new crud();

	$li_cod_tratamiento = $lb_edit ? $array["N_COD_TRATAMIENTO"] : $as_cod_tratamiento;

	// Enlaces
	$url_lista		= "mov_sesion_lista.php?id_cod_tratamiento_filtro=" . $li_cod_tratamiento;
	$url_registrar	= "../controlador/mov_sesion_registrar.php";
	$url_actualizar	= "../controlador/mov_sesion_actualizar.php";
	$url_eliminar	= "../controlador/mov_sesion_eliminar.php";

	if($lb_edit) {
		$ls_modo = DEF_MSG_FORM_EDICION;
	}else{
		$ls_modo = DEF_MSG_FORM_NUEVO;
	}

	// Datos del tratamiento para mostrar contexto
	$array_campo_pk	= array('N_COD_TRATAMIENTO');
	$array_valor_pk	= array($li_cod_tratamiento);
	$array_tratamiento = $crud->fila_recuperar(DEF_TABLA_TRATAMIENTO, $array_campo_pk, $array_valor_pk);

	$array_campo_pk	= array('N_COD_PACIENTE');
	$array_valor_pk	= array($array_tratamiento["N_COD_PACIENTE"]);
	$ls_pac = $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO') . ' ' .
			  $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO') . ', ' .
			  $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_NOMBRES');

	// Siguiente número de sesión (solo informativo en modo nuevo)
	if (!$lb_edit) {
		$array_campo_pk	= array('N_COD_TRATAMIENTO', 'V_FLAG_ESTADO');
		$array_valor_pk	= array($li_cod_tratamiento, '1');
		$li_siguiente_num = intval($crud->fila_contar(DEF_TABLA_SESION, $array_campo_pk, $array_valor_pk)) + 1;
	}

	// Formulario HTML
	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<!-- Cabecera de Sección Contenido -->
		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo;?>
			</h1>
			<ol class="breadcrumb">
				<li><a href="<?php echo DEF_URL_LOGIN;?>"><i class="fa fa-home"></i> Inicio</a></li>
				<li class="active"><a href="<?php echo $url_lista?>">Volver a Sesiones</a></li>
			</ol>
		</section>
		<!-- Fin de Cabecera de Sección Contenido -->

		<!-- Contenido -->
		<section class="content">

			<?php
			if($as_msgRpta) {
				if (substr($as_msgRpta,0,2)=='OK'){
					echo "<div class='alert alert-success'>";
						echo DEF_MSG_FORM_AVISO_OK.substr($as_msgRpta,3);
					echo "</div>";
				}else{
					echo "<div class='alert alert-danger'>";
						echo DEF_MSG_FORM_AVISO.$as_msgRpta;
					echo "</div>";
				}
			}
			?>

			<!-- Fila Principal -->
			<div class="row">

				<!-- Columna Izquierda -->
				<section class="col-lg-12 connectedSortable">

					<!-- Panel -->
					<div class="panel panel-primary">

						<!-- Cabecera -->
						<div class="box-header">
							<i class="fa fa-edit"></i>
							MANTENIMIENTO - <?php echo strtoupper($ls_modo);?> &nbsp;
							<small><?php echo $ls_pac; ?></small>
						</div>

						<!-- Cuerpo -->
						<div class="panel-body">

							<form id="form_mtto" role="form" method="post" action="" autocomplete="off">

								<input type="hidden" id="id_cod_tratamiento" name="id_cod_tratamiento" value="<?php echo $li_cod_tratamiento; ?>">
								<input type="hidden" id="id_cod_tratamiento_hide" name="id_cod_tratamiento_hide" value="<?php echo $li_cod_tratamiento; ?>">
								<input type="hidden" id="id_cod_sesion_hide" name="id_cod_sesion_hide"
									value="<?php echo $lb_edit?$array["N_COD_SESION"]:""; ?>">

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label>N° de Sesión</label>
											<input type="text" class="form-control" disabled
												value="<?php echo $lb_edit ? intval($array["N_NUM_SESION"]) : $li_siguiente_num; ?> de <?php echo intval($array_tratamiento["N_NUM_SESIONES"]); ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fec_sesion">Fecha de la Sesión</label>
											<input type="date" class="form-control" id="id_fec_sesion" name="id_fec_sesion" required
												value="<?php echo $lb_edit?$array["D_FEC_SESION"]:date('Y-m-d'); ?>">
										</div>
									</div>
									<?php if (!$lb_edit) { ?>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_cita">Cita asociada (opcional)</label>
											<select class="form-control" id="id_cod_cita" name="id_cod_cita">
												<?php
												$ls_condicion = "N_COD_TRATAMIENTO = '$li_cod_tratamiento'
														AND V_FLAG_ESTADO = '1'
														AND V_ESTADO_CITA IN ('PRO','CON','ATE')
														AND NOT EXISTS (SELECT 1 FROM MOV_SESION s WHERE s.N_COD_CITA = MOV_CITA.N_COD_CITA AND s.V_FLAG_ESTADO = '1')";
												$array_cita = $crud->fila_listar_solocondicion(DEF_TABLA_CITA.' WHERE '.$ls_condicion, 'D_FEC_CITA', 'D', 0, 100);

												echo "<option value=''>Ninguna</option>";
												if ($array_cita) {
													while ($this_cita = mysqli_fetch_assoc($array_cita)) {
														echo "<option value=\"".$this_cita["N_COD_CITA"]."\">".
															date('d/m/Y', strtotime($this_cita["D_FEC_CITA"])).' '.substr($this_cita["D_HORA_INICIO"],0,5)."</option>\n";
													}
												}
												?>
											</select>
											<span class="help-block">Si la sesión corresponde a una cita agendada, selecciónala: se marcará como Atendida.</span>
										</div>
									</div>
									<?php } ?>
								</div>

								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label for="id_observacion">Observación</label>
											<textarea class="form-control" id="id_observacion" name="id_observacion" maxlength="300" rows="2"
												placeholder="(*) Ejemplo: Paciente refiere disminución del dolor"><?php echo $lb_edit?$array["V_OBSERVACION"]:""; ?></textarea>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label for="id_evolucion">Evolución Clínica</label>
											<textarea class="form-control" id="id_evolucion" name="id_evolucion" maxlength="300" rows="3"
												placeholder="(*) Ejemplo: Mejora de rango de movimiento en 15°, continúa con el plan"><?php echo $lb_edit?$array["V_EVOLUCION"]:""; ?></textarea>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-12">
										<?php if ($lb_edit) { ?>
											<input class="btn btn-success" type="submit" id="btn_actualizar" value="Actualizar"
												onclick="this.form.action='<?php echo $url_actualizar?>'">
											<input class="btn btn-danger" type="submit" id="btn_eliminar" value="Eliminar" formnovalidate
												onclick="return confirm('¿Eliminar esta sesión? Solo se permite si es la última registrada.') && (this.form.action='<?php echo $url_eliminar?>')">
											<input class="btn btn-default" type="submit" id="btn_cancelar" value="Volver a Sesiones" formnovalidate
												onclick="this.form.action='<?php echo $url_lista?>'">
										<?php } else { ?>
											<input class="btn btn-success" type="submit" id="btn_agregar" value="Guardar"
												onclick="this.form.action='<?php echo $url_registrar?>'">
											<input class="btn btn-default" type="submit" id="btn_cancelar" value="Volver a Sesiones" formnovalidate
												onclick="this.form.action='<?php echo $url_lista?>'">
										<?php } ?>
									</div>
								</div>

							</form>

						</div>
						<!-- Fin Cuerpo -->

					</div>
					<!-- Fin Panel -->

				</section>
				<!-- Fin Columna Izquierda -->

			</div>
			<!-- Fin Fila Principal -->

		</section>
		<!-- Fin Contenido -->

	</div>
	<!-- Fin sección Contenido -->

<?php
}

?>
