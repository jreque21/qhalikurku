<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Catálogo de estados de tratamiento (debe coincidir con V_ESTADO_TRATAMIENTO en MOV_TRATAMIENTO)
function f_estados_tratamiento() {
	return array(
		'ACT' => array('Activo',     'label-success'),
		'FIN' => array('Finalizado', 'label-default'),
		'SUS' => array('Suspendido', 'label-danger'),
	);
}

// Lista de Tratamientos (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta, $as_cod_paciente_filtro = ''){

	// Enlaces
	$url_nuevo		= "mov_tratamiento_nuevo.php";
	$url_editar		= "mov_tratamiento_editar.php";
	$url_eliminar	= "../controlador/mov_tratamiento_eliminar.php";
	$url_lista		= "mov_tratamiento_lista.php";

	// Instanciar clase
	$crud = new crud();
	$bd   = new baseDatos();

	// Armar condición dinámica según el filtro elegido
	$ls_condicion = "V_FLAG_ESTADO = '1'";

	if (!empty($as_cod_paciente_filtro)) {
		$ls_condicion .= " AND N_COD_PACIENTE = '" . $bd->bd_escapeCadena($as_cod_paciente_filtro) . "'";
	}

	// Listado (más recientes primero)
	$array = $crud->fila_listar_solocondicion(DEF_TABLA_TRATAMIENTO . ' WHERE ' . $ls_condicion, 'D_FEC_INICIO', 'D', 0, 1000);

	$array_estados = f_estados_tratamiento();

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

					<!-- Panel de Filtros -->
					<div class="panel panel-default">
						<div class="panel-body">
							<form role="form" method="get" action="<?php echo $url_lista; ?>" class="form-inline">
								<div class="form-group">
									<label for="id_cod_paciente_filtro">Paciente</label>
									<select class="form-control" id="id_cod_paciente_filtro" name="id_cod_paciente_filtro">
										<option value="">Todos</option>
										<?php
										$array_campo_pk	= array('V_FLAG_ESTADO');
										$array_valor_pk	= array('1');
										$array_pac_filtro = $crud->fila_listar(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO, V_APE_MATERNO, V_NOMBRES', 'A', 0, 999);

										while ($this_pac_filtro = mysqli_fetch_assoc($array_pac_filtro)) {
											echo "<option value=\"".$this_pac_filtro["N_COD_PACIENTE"]."\"";
											if ($as_cod_paciente_filtro == $this_pac_filtro["N_COD_PACIENTE"]) {
												echo " selected";
											}
											echo ">".$this_pac_filtro["V_APE_PATERNO"].' '.$this_pac_filtro["V_APE_MATERNO"].', '.$this_pac_filtro["V_NOMBRES"]."</option>\n";
										}
										?>
									</select>
								</div>
								&nbsp;
								<button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
								<a href="<?php echo $url_lista; ?>" class="btn btn-default">Ver Todos</a>
							</form>
						</div>
					</div>
					<!-- Fin Panel de Filtros -->

					<!-- Panel -->
					<div class="panel panel-primary">

						<!-- Cabecera -->
						<div class="box-header">
							<i class="fa fa-medkit"></i>
							MANTENIMIENTO - TRATAMIENTOS
						</div>

						<!-- Cuerpo -->
						<div class="panel-body">

							<?php
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
												<th>ID</th>
												<th>Paciente</th>
												<th>Especialista</th>
												<th>Sede</th>
												<th>Fecha Inicio</th>
												<th>Diagnóstico</th>
												<th>Sesiones</th>
												<th>Estado</th>
												<th>Acciones</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {

												$url_editar_fila = $url_editar."?id_codigo=".intval($row["N_COD_TRATAMIENTO"]);

												// Paciente
												$array_campo_pk	= array('N_COD_PACIENTE');
												$array_valor_pk	= array($row["N_COD_PACIENTE"]);
												$ls_pac_ape_p	= $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO');
												$ls_pac_ape_m	= $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');
												$ls_pac_nombres	= $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_NOMBRES');

												// Especialista
												$array_campo_pk	= array('N_COD_ESPECIALISTA');
												$array_valor_pk	= array($row["N_COD_ESPECIALISTA"]);
												$ls_esp_ape_p	= $crud->fila_recuperar_campo(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO');
												$ls_esp_ape_m	= $crud->fila_recuperar_campo(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');

												// Sede
												$array_campo_pk	= array('N_COD_SEDE');
												$array_valor_pk	= array($row["N_COD_SEDE"]);
												$ls_sede		= $crud->fila_recuperar_campo(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE');

												// Sesiones realizadas (contadas desde MOV_SESION)
												$array_campo_pk	= array('N_COD_TRATAMIENTO', 'V_FLAG_ESTADO');
												$array_valor_pk	= array($row["N_COD_TRATAMIENTO"], '1');
												$li_sesiones_realizadas = $crud->fila_contar(DEF_TABLA_SESION, $array_campo_pk, $array_valor_pk);

												$ls_estado_cod  = $row["V_ESTADO_TRATAMIENTO"];
												$ls_estado_des  = isset($array_estados[$ls_estado_cod]) ? $array_estados[$ls_estado_cod][0] : $ls_estado_cod;
												$ls_estado_css  = isset($array_estados[$ls_estado_cod]) ? $array_estados[$ls_estado_cod][1] : 'label-default';
												?>
												<tr>
													<td><?php echo intval($row["N_COD_TRATAMIENTO"]); ?></td>
													<td><a href="<?php echo $url_editar_fila?>"><?php echo $ls_pac_ape_p.' '.$ls_pac_ape_m.', '.$ls_pac_nombres; ?></a></td>
													<td><?php echo $ls_esp_ape_p.' '.$ls_esp_ape_m; ?></td>
													<td><?php echo $ls_sede; ?></td>
													<td><?php echo date('d/m/Y', strtotime($row["D_FEC_INICIO"])); ?></td>
													<td><?php echo $row["V_DIAGNOSTICO"]; ?></td>
													<td><?php echo intval($li_sesiones_realizadas).' / '.intval($row["N_NUM_SESIONES"]); ?></td>
													<td><span class="label <?php echo $ls_estado_css;?>"><?php echo $ls_estado_des;?></span></td>
													<td align="center">
														<a href="<?php echo $url_editar_fila?>">
															<i class="fa fa-edit" title="Editar / Ver detalle"></i>
														</a>
														&nbsp;
														<a href="mov_sesion_lista.php?id_cod_tratamiento_filtro=<?php echo intval($row["N_COD_TRATAMIENTO"]); ?>">
															<i class="fa fa-stethoscope text-info" title="Ver Sesiones"></i>
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

					<a class="btn btn-primary" href="<?php echo $url_nuevo?>" role="button">
						<span class="glyphicon glyphicon-plus"></span>&nbsp;Nuevo Tratamiento
					</a>

					</br>

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

// Formulario de Registro / Edición
function f_formulario($as_titulo, $as_icono, $as_msgRpta, $array = "") {

	// Inicalizando variables
	$lb_edit = is_array($array);

	// Instanciar clase
	$crud = new crud();

	// Enlaces
	$url_lista		= "mov_tratamiento_lista.php";
	$url_registrar	= "../controlador/mov_tratamiento_registrar.php";
	$url_actualizar	= "../controlador/mov_tratamiento_actualizar.php";
	$url_eliminar	= "../controlador/mov_tratamiento_eliminar.php";

	if($lb_edit) {
		$ls_modo = DEF_MSG_FORM_EDICION;
	}else{
		$ls_modo = DEF_MSG_FORM_NUEVO;
	}

	$array_estados = f_estados_tratamiento();

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
				<li class="active"><a href="<?php echo $url_lista?>">Volver a Lista</a></li>
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
							TRATAMIENTOS - <?php echo strtoupper($ls_modo);?>
						</div>

						<!-- Cuerpo -->
						<div class="panel-body">

							<form id="form_mtto" role="form" method="post" action="" autocomplete="off">

								<input type="hidden" id="id_cod_tratamiento_hide" name="id_cod_tratamiento_hide"
									value="<?php echo $lb_edit?$array["N_COD_TRATAMIENTO"]:""; ?>">

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_sede">Sede</label>
											<select class="form-control" id="id_cod_sede" name="id_cod_sede" required>
												<?php
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_sede     = $crud->fila_listar(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE', 'A', 0, 100);

												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_sede = mysqli_fetch_assoc($array_sede)) {
													echo "<option value=\"".$this_sede["N_COD_SEDE"]."\"";
													if ($lb_edit && $this_sede["N_COD_SEDE"] == $array["N_COD_SEDE"]){
														echo " selected";
													}
													echo ">".$this_sede["V_NOMBRE"]."\n";
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_paciente">Paciente</label>
											<select class="form-control" id="id_cod_paciente" name="id_cod_paciente" required>
												<?php
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_pac      = $crud->fila_listar(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO, V_APE_MATERNO, V_NOMBRES', 'A', 0, 999);

												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_pac = mysqli_fetch_assoc($array_pac)) {
													echo "<option value=\"".$this_pac["N_COD_PACIENTE"]."\"";
													if ($lb_edit && $this_pac["N_COD_PACIENTE"] == $array["N_COD_PACIENTE"]){
														echo " selected";
													}
													echo ">".$this_pac["V_APE_PATERNO"].' '.$this_pac["V_APE_MATERNO"].', '.$this_pac["V_NOMBRES"]."\n";
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_especialista">Especialista</label>
											<select class="form-control" id="id_cod_especialista" name="id_cod_especialista" required>
												<?php
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_esp      = $crud->fila_listar(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO, V_APE_MATERNO', 'A', 0, 999);

												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_esp = mysqli_fetch_assoc($array_esp)) {
													echo "<option value=\"".$this_esp["N_COD_ESPECIALISTA"]."\"";
													if ($lb_edit && $this_esp["N_COD_ESPECIALISTA"] == $array["N_COD_ESPECIALISTA"]){
														echo " selected";
													}
													echo ">".$this_esp["V_APE_PATERNO"].' '.$this_esp["V_APE_MATERNO"]."\n";
												}
												?>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fec_inicio">Fecha de Inicio</label>
											<input type="date" class="form-control" id="id_fec_inicio" name="id_fec_inicio" required
												value="<?php echo $lb_edit?$array["D_FEC_INICIO"]:""; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_num_sesiones">Número de Sesiones</label>
											<input type="number" class="form-control" id="id_num_sesiones" name="id_num_sesiones" min="1" required
												value="<?php echo $lb_edit?$array["N_NUM_SESIONES"]:""; ?>">
										</div>
									</div>
									<?php if ($lb_edit) { ?>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fec_fin">Fecha de Fin</label>
											<input type="date" class="form-control" id="id_fec_fin" name="id_fec_fin"
												value="<?php echo $lb_edit && $array["D_FEC_FIN"] ? $array["D_FEC_FIN"] : ""; ?>">
											<span class="help-block">Obligatoria solo si el estado pasa a "Finalizado".</span>
										</div>
									</div>
									<?php } ?>
								</div>

								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label for="id_diagnostico">Diagnóstico</label>
											<textarea class="form-control" id="id_diagnostico" name="id_diagnostico" maxlength="300" rows="3" required
												placeholder="(*) Ejemplo: Lumbalgia crónica, requiere 10 sesiones de fisioterapia"><?php echo $lb_edit?$array["V_DIAGNOSTICO"]:""; ?></textarea>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label for="id_observacion_trat">Observaciones</label>
											<textarea class="form-control" id="id_observacion_trat" name="id_observacion_trat" maxlength="300" rows="3"
												placeholder="(Opcional) Notas adicionales sobre el tratamiento"><?php echo $lb_edit?$array["V_OBSERVACION"]:""; ?></textarea>
										</div>
									</div>
								</div>

								<?php if ($lb_edit) { ?>
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_estado_tratamiento">Estado</label>
											<select class="form-control" id="id_estado_tratamiento" name="id_estado_tratamiento">
												<?php
												foreach ($array_estados as $cod => $info) {
													echo "<option value=\"$cod\"";
													if ($array["V_ESTADO_TRATAMIENTO"] == $cod) echo " selected";
													echo ">".$info[0]."</option>\n";
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-sm-8">
										<div class="form-group">
											<label>Progreso de Sesiones</label>
											<?php
											$array_campo_pk	= array('N_COD_TRATAMIENTO', 'V_FLAG_ESTADO');
											$array_valor_pk	= array($array["N_COD_TRATAMIENTO"], '1');
											$li_sesiones_realizadas = $crud->fila_contar(DEF_TABLA_SESION, $array_campo_pk, $array_valor_pk);
											$li_porcentaje = $array["N_NUM_SESIONES"] > 0 ? round(($li_sesiones_realizadas / $array["N_NUM_SESIONES"]) * 100) : 0;
											?>
											<div class="progress">
												<div class="progress-bar progress-bar-success" role="progressbar"
													style="width: <?php echo $li_porcentaje; ?>%;">
													<?php echo intval($li_sesiones_realizadas); ?> / <?php echo intval($array["N_NUM_SESIONES"]); ?> sesiones
												</div>
											</div>
										</div>
									</div>
								</div>
								<?php } ?>

								<div class="row">
									<div class="col-sm-12">
										<?php if ($lb_edit) { ?>
											<input class="btn btn-success" type="submit" id="btn_actualizar" value="Actualizar"
												onclick="this.form.action='<?php echo $url_actualizar?>'">
											<a class="btn btn-info" href="mov_sesion_lista.php?id_cod_tratamiento_filtro=<?php echo intval($array["N_COD_TRATAMIENTO"]); ?>">
												<i class="fa fa-stethoscope"></i> Ver Sesiones
											</a>
											<input class="btn btn-danger" type="submit" id="btn_eliminar" value="Eliminar" formnovalidate
												onclick="this.form.action='<?php echo $url_eliminar?>'">
											<input class="btn btn-default" type="submit" id="btn_cancelar" value="Volver a Lista" formnovalidate
												onclick="this.form.action='<?php echo $url_lista?>'">
										<?php } else { ?>
											<input class="btn btn-success" type="submit" id="btn_agregar" value="Guardar"
												onclick="this.form.action='<?php echo $url_registrar?>'">
											<input class="btn btn-default" type="submit" id="btn_cancelar" value="Volver a Lista" formnovalidate
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
