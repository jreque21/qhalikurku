<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Catálogo de estados de cita (debe coincidir con V_ESTADO_CITA en MOV_CITA)
function f_estados_cita() {
	return array(
		'PRO' => array('Programada',  'label-default'),
		'CON' => array('Confirmada',  'label-info'),
		'ATE' => array('Atendida',    'label-success'),
		'COM' => array('Completada',  'label-primary'),
		'REP' => array('Reprogramada','label-warning'),
		'CAN' => array('Cancelada',   'label-danger'),
		'NOA' => array('No asistió',  'label-danger'),
	);
}

// Lista de Citas (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta, $as_fec_filtro = '', $as_cod_especialista_filtro = '', $as_fec_fin_filtro = ''){

	// Enlaces
	$url_nuevo		= "mov_cita_nuevo.php";
	$url_editar		= "mov_cita_editar.php";
	$url_eliminar	= "../controlador/mov_cita_eliminar.php";
	$url_lista		= "mov_cita_lista.php";

	// Instanciar clase
	$crud = new crud();
	$bd   = new baseDatos();

	// Armar condición dinámica según los filtros elegidos
	$ls_condicion = "V_FLAG_ESTADO = '1'";

	if (!empty($as_fec_filtro) && !empty($as_fec_fin_filtro)) {
		$ls_condicion .= " AND D_FEC_CITA BETWEEN '" . $bd->bd_escapeCadena($as_fec_filtro) . "' AND '" . $bd->bd_escapeCadena($as_fec_fin_filtro) . "'";
	} elseif (!empty($as_fec_filtro)) {
		$ls_condicion .= " AND D_FEC_CITA >= '" . $bd->bd_escapeCadena($as_fec_filtro) . "'";
	} elseif (!empty($as_fec_fin_filtro)) {
		$ls_condicion .= " AND D_FEC_CITA <= '" . $bd->bd_escapeCadena($as_fec_fin_filtro) . "'";
	}

	if (!empty($as_cod_especialista_filtro)) {
		$ls_condicion .= " AND N_COD_ESPECIALISTA = '" . $bd->bd_escapeCadena($as_cod_especialista_filtro) . "'";
	}

	// Listado (más próximas / más recientes primero)
	$array = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . ' WHERE ' . $ls_condicion, 'D_FEC_CITA, D_HORA_INICIO', 'D', 0, 1000);

	$array_estados = f_estados_cita();

	// Si hay un especialista filtrado, armar el link para enviarle su agenda completa por WhatsApp
	$ls_wsp_agenda_link = '';
	if (!empty($as_cod_especialista_filtro)) {

		$array_campo_pk = array('N_COD_ESPECIALISTA');
		$array_valor_pk = array($as_cod_especialista_filtro);
		$ls_esp_nombre  = $crud->fila_recuperar_campo(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_NOMBRES');
		$ls_esp_movil   = $crud->fila_recuperar_campo(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_MOVIL');

		$lr_agenda = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . ' WHERE ' . $ls_condicion . " AND V_ESTADO_CITA <> 'CAN'", 'D_FEC_CITA, D_HORA_INICIO', 'A', 0, 100);

		$ls_msg_agenda = "Hola $ls_esp_nombre, esta es tu agenda:\n";
		$li_citas_agenda = 0;

		if ($lr_agenda) {
			while ($row_ag = mysqli_fetch_assoc($lr_agenda)) {
				$array_campo_pk = array('N_COD_PACIENTE');
				$array_valor_pk = array($row_ag["N_COD_PACIENTE"]);
				$ls_pac_ag = $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO') . ' ' .
							 $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_NOMBRES');

				$ls_msg_agenda .= "- " . date('d/m', strtotime($row_ag["D_FEC_CITA"])) . " " . substr($row_ag["D_HORA_INICIO"],0,5) . ": $ls_pac_ag\n";
				$li_citas_agenda++;
			}
		}

		if ($li_citas_agenda == 0) {
			$ls_msg_agenda .= "No tienes citas programadas en este rango.";
		}

		$ls_wsp_agenda_link = f_whatsapp_link($ls_esp_movil, $ls_msg_agenda);
	}

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
									<label for="id_fec_ini_filtro">Fecha Inicio</label>
									<input type="date" class="form-control" id="id_fec_ini_filtro" name="id_fec_ini_filtro"
										value="<?php echo htmlspecialchars($as_fec_filtro); ?>">
								</div>
								&nbsp;
								<div class="form-group">
									<label for="id_fec_fin_filtro">Fecha Fin</label>
									<input type="date" class="form-control" id="id_fec_fin_filtro" name="id_fec_fin_filtro"
										value="<?php echo htmlspecialchars($as_fec_fin_filtro); ?>">
								</div>
								&nbsp;
								<div class="form-group">
									<label for="id_cod_especialista_filtro">Especialista</label>
									<select class="form-control" id="id_cod_especialista_filtro" name="id_cod_especialista_filtro">
										<option value="">Todos</option>
										<?php
										$array_campo_pk	= array('V_FLAG_ESTADO');
										$array_valor_pk	= array('1');
										$array_esp_filtro = $crud->fila_listar(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO, V_APE_MATERNO', 'A', 0, 999);

										while ($this_esp_filtro = mysqli_fetch_assoc($array_esp_filtro)) {
											echo "<option value=\"".$this_esp_filtro["N_COD_ESPECIALISTA"]."\"";
											if ($as_cod_especialista_filtro == $this_esp_filtro["N_COD_ESPECIALISTA"]) {
												echo " selected";
											}
											echo ">".$this_esp_filtro["V_APE_PATERNO"].' '.$this_esp_filtro["V_APE_MATERNO"]."</option>\n";
										}
										?>
									</select>
								</div>
								&nbsp;
								<button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
								<a href="<?php echo $url_lista; ?>?id_fec_ini_filtro=&id_fec_fin_filtro=&id_cod_especialista_filtro=" class="btn btn-default">Ver Todas las Fechas</a>
								<a href="<?php echo $url_lista; ?>" class="btn btn-link">Volver a Hoy</a>
								<?php if (!empty($ls_wsp_agenda_link)) { ?>
								<a href="<?php echo $ls_wsp_agenda_link; ?>" target="_blank" class="btn btn-success">
									<i class="fa fa-whatsapp"></i> Enviar Agenda al Especialista
								</a>
								<?php } ?>
							</form>
						</div>
					</div>
					<!-- Fin Panel de Filtros -->

					<!-- Panel -->
					<div class="panel panel-primary">

						<!-- Cabecera -->
						<div class="box-header">
							<i class="ion ion-calendar"></i>
							MANTENIMIENTO - <?php echo DEF_MSG_FORM_LISTADO;?>
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
												<th>Fecha</th>
												<th>Hora</th>
												<th>Paciente</th>
												<th>Especialista</th>
												<th>Sede</th>
												<th>Estado</th>
												<th>Acciones</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {

												$url_editar_fila = $url_editar."?id_codigo=".intval($row["N_COD_CITA"]);

												// Paciente
												$array_campo_pk	= array('N_COD_PACIENTE');
												$array_valor_pk	= array($row["N_COD_PACIENTE"]);
												$ls_pac_ape_p	= $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO');
												$ls_pac_ape_m	= $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');
												$ls_pac_nombres	= $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_NOMBRES');
												$ls_pac_movil	= $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_MOVIL');

												// Especialista
												$array_campo_pk	= array('N_COD_ESPECIALISTA');
												$array_valor_pk	= array($row["N_COD_ESPECIALISTA"]);
												$ls_esp_ape_p	= $crud->fila_recuperar_campo(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO');
												$ls_esp_ape_m	= $crud->fila_recuperar_campo(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');

												// Sede
												$array_campo_pk	= array('N_COD_SEDE');
												$array_valor_pk	= array($row["N_COD_SEDE"]);
												$ls_sede		= $crud->fila_recuperar_campo(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE');

												$ls_estado_cod  = $row["V_ESTADO_CITA"];
												$ls_estado_des  = isset($array_estados[$ls_estado_cod]) ? $array_estados[$ls_estado_cod][0] : $ls_estado_cod;
												$ls_estado_css  = isset($array_estados[$ls_estado_cod]) ? $array_estados[$ls_estado_cod][1] : 'label-default';
												?>
												<tr>
													<td><?php echo intval($row["N_COD_CITA"]); ?></td>
													<td><?php echo date('d/m/Y', strtotime($row["D_FEC_CITA"])); ?></td>
													<td><?php echo substr($row["D_HORA_INICIO"],0,5).' - '.substr($row["D_HORA_FIN"],0,5); ?></td>
													<td><a href="<?php echo $url_editar_fila?>"><?php echo $ls_pac_ape_p.' '.$ls_pac_ape_m.', '.$ls_pac_nombres; ?></a></td>
													<td><?php echo $ls_esp_ape_p.' '.$ls_esp_ape_m; ?></td>
													<td><?php echo $ls_sede; ?></td>
													<td><span class="label <?php echo $ls_estado_css;?>"><?php echo $ls_estado_des;?></span></td>
													<td align="center">
														<a href="<?php echo $url_editar_fila?>">
															<i class="fa fa-edit" title="Editar / Ver detalle"></i>
														</a>
														<?php if ($ls_estado_cod != 'CAN' && $ls_estado_cod != 'ATE' && $ls_estado_cod != 'COM') { ?>
														&nbsp;
														<a href="javascript:void(0)" onclick="f_cancelar_cita(<?php echo intval($row["N_COD_CITA"]); ?>)">
															<i class="fa fa-times-circle text-danger" title="Cancelar cita"></i>
														</a>
														<?php } ?>
														<?php if ($ls_estado_cod == 'ATE') {
															$array_campo_pk_pago = array('N_COD_CITA', 'V_FLAG_ESTADO');
															$array_valor_pk_pago = array($row["N_COD_CITA"], '1');
															$li_tiene_pago = $crud->fila_contar('MOV_PAGO', $array_campo_pk_pago, $array_valor_pk_pago);
															if ($li_tiene_pago == 0) { ?>
														&nbsp;
														<a href="../vista/mov_pago_nuevo.php?id_cod_cita=<?php echo intval($row["N_COD_CITA"]); ?>">
															<i class="fa fa-money text-success" title="Registrar pago"></i>
														</a>
														<?php } } ?>
														<?php
														$ls_msg_wsp = "Hola " . $ls_pac_nombres . ", te recordamos tu cita el " . date('d/m/Y', strtotime($row["D_FEC_CITA"])) . " a las " . substr($row["D_HORA_INICIO"],0,5) . " con " . $ls_esp_ape_p . ' ' . $ls_esp_ape_m . " en " . $ls_sede . ". ¡Te esperamos!";
														$ls_wsp_link = f_whatsapp_link($ls_pac_movil, $ls_msg_wsp);
														if (!empty($ls_wsp_link) && $ls_estado_cod != 'CAN') { ?>
														&nbsp;
														<a href="<?php echo $ls_wsp_link; ?>" target="_blank">
															<i class="fa fa-whatsapp text-success" title="Enviar recordatorio por WhatsApp"></i>
														</a>
														<?php } ?>
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
						<span class="glyphicon glyphicon-plus"></span>&nbsp;Nueva Cita
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

	<script>
	function f_cancelar_cita(codigo) {
		var motivo = prompt("Motivo de la cancelación de la cita:");
		if (motivo === null) {
			return; // el usuario le dio Cancelar en el prompt
		}
		if (motivo.trim() === "") {
			alert("Debe indicar un motivo para cancelar la cita.");
			return;
		}
		window.location.href = "<?php echo $url_eliminar; ?>?id_codigo=" + codigo + "&id_motivo=" + encodeURIComponent(motivo);
	}
	</script>

<?php
}

// Formulario de Registro / Edición
function f_formulario($as_titulo, $as_icono, $as_msgRpta, $array = "") {

	// Inicalizando variables
	$lb_edit = is_array($array);
	// Una cita "Completada", "Cancelada" o "Reprogramada" ya no se puede modificar.
	// Una cita "Atendida" sí se puede seguir editando (por ejemplo, para completar diagnóstico
	// u observaciones antes de marcarla como "Completada").
	$lb_readonly = $lb_edit && in_array($array["V_ESTADO_CITA"], array('REP', 'CAN', 'COM'));

	// Instanciar clase
	$crud = new crud();

	// Enlaces
	$url_lista		= "mov_cita_lista.php";
	$url_registrar	= "../controlador/mov_cita_registrar.php";
	$url_actualizar	= "../controlador/mov_cita_actualizar.php";
	$url_eliminar	= "../controlador/mov_cita_eliminar.php";
	$url_reprogramar= "../controlador/mov_cita_reprogramar.php";

	if($lb_edit) {
		$ls_modo = DEF_MSG_FORM_EDICION;
	}else{
		$ls_modo = DEF_MSG_FORM_NUEVO;
	}

	$array_estados = f_estados_cita();

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
							MANTENIMIENTO - <?php echo strtoupper($ls_modo);?>
						</div>

						<!-- Cuerpo -->
						<div class="panel-body">

							<form id="form_mtto" role="form" method="post" action="" autocomplete="off">

							<?php if ($lb_readonly) { ?>
							<div class="alert alert-info">
								<i class="fa fa-lock"></i> Esta cita está en un estado que no permite modificaciones (<?php echo f_estados_cita()[$array["V_ESTADO_CITA"]][0]; ?>). Solo puede consultarse.
							</div>
							<?php } ?>

							<fieldset <?php echo $lb_readonly ? 'disabled' : ''; ?>>

								<input type="hidden" id="id_cod_cita_hide" name="id_cod_cita_hide"
									value="<?php echo $lb_edit?$array["N_COD_CITA"]:""; ?>">

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

													// Sedes donde atiende este especialista (para filtrar en el cliente)
													$array_campo_pk2 = array('N_COD_ESPECIALISTA', 'V_FLAG_ESTADO');
													$array_valor_pk2 = array($this_esp["N_COD_ESPECIALISTA"], '1');
													$array_sedes_esp = $crud->fila_listar(DEF_TABLA_SEDEESPECIALISTA, $array_campo_pk2, $array_valor_pk2, '', '', 0, 100);
													$ls_sedes_csv = array();
													if ($array_sedes_esp) {
														while ($row_se = mysqli_fetch_assoc($array_sedes_esp)) {
															$ls_sedes_csv[] = $row_se["N_COD_SEDE"];
														}
													}

													echo "<option value=\"".$this_esp["N_COD_ESPECIALISTA"]."\" data-sedes=\"".implode(',', $ls_sedes_csv)."\"";
													if ($lb_edit && $this_esp["N_COD_ESPECIALISTA"] == $array["N_COD_ESPECIALISTA"]){
														echo " selected";
													}
													echo ">".$this_esp["V_APE_PATERNO"].' '.$this_esp["V_APE_MATERNO"]."\n";
												}
												?>
											</select>
											<span class="help-block">Se filtra automáticamente según la Sede elegida (si el especialista tiene sedes asignadas).</span>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_tipoterapia">Tipo de Terapia</label>
											<select class="form-control" id="id_cod_tipoterapia" name="id_cod_tipoterapia">
												<?php
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_tt       = $crud->fila_listar(DEF_TABLA_TIPOTERAPIA, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA', 'A', 0, 100);

												echo "<option value=''>No especificado</option>";
												while ($this_tt = mysqli_fetch_assoc($array_tt)) {
													echo "<option value=\"".$this_tt["N_COD_TIPOTERAPIA"]."\" data-duracion=\"".$this_tt["N_DURACION_MIN"]."\"";
													if ($lb_edit && $this_tt["N_COD_TIPOTERAPIA"] == $array["N_COD_TIPOTERAPIA"]){
														echo " selected";
													}
													echo ">".$this_tt["V_DES_CORTA"]." (".$this_tt["N_DURACION_MIN"]." min)\n";
												}
												?>
											</select>
											<span class="help-block">Al elegir, calcula automáticamente la Hora de Fin.</span>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fec_cita">Fecha de la Cita</label>
											<input type="date" class="form-control" id="id_fec_cita" name="id_fec_cita" required
												value="<?php echo $lb_edit?$array["D_FEC_CITA"]:""; ?>">
										</div>
									</div>
									<?php if ($lb_edit) { ?>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_estado_cita">Estado</label>
											<select class="form-control" id="id_estado_cita" name="id_estado_cita">
												<?php
												foreach ($array_estados as $cod => $info) {
													echo "<option value=\"$cod\"";
													if ($array["V_ESTADO_CITA"] == $cod) echo " selected";
													echo ">".$info[0]."</option>\n";
												}
												?>
											</select>
										</div>
									</div>
									<?php } ?>
								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_hora_inicio">Hora de Inicio</label>
											<input type="time" class="form-control" id="id_hora_inicio" name="id_hora_inicio" required
												value="<?php echo $lb_edit?substr($array["D_HORA_INICIO"],0,5):""; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_hora_fin">Hora de Fin</label>
											<input type="time" class="form-control" id="id_hora_fin" name="id_hora_fin" required
												value="<?php echo $lb_edit?substr($array["D_HORA_FIN"],0,5):""; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_tratamiento">Tratamiento (opcional)</label>
											<select class="form-control" id="id_cod_tratamiento" name="id_cod_tratamiento">
												<?php
												$array_campo_pk	= array('V_FLAG_ESTADO', 'V_ESTADO_TRATAMIENTO');
												$array_valor_pk	= array('1', 'ACT');
												$array_trat     = $crud->fila_listar(DEF_TABLA_TRATAMIENTO, $array_campo_pk, $array_valor_pk, 'D_FEC_INICIO', 'D', 0, 999);

												echo "<option value=''>Ninguno / primera consulta</option>";
												if ($array_trat) {
													while ($this_trat = mysqli_fetch_assoc($array_trat)) {
														echo "<option value=\"".$this_trat["N_COD_TRATAMIENTO"]."\" data-paciente=\"".$this_trat["N_COD_PACIENTE"]."\"";
														if ($lb_edit && $this_trat["N_COD_TRATAMIENTO"] == $array["N_COD_TRATAMIENTO"]){
															echo " selected";
														}
														echo ">".substr($this_trat["V_DIAGNOSTICO"],0,40)." (".date('d/m/Y', strtotime($this_trat["D_FEC_INICIO"])).")\n";
													}
												}
												?>
											</select>
											<span class="help-block">Se filtra automáticamente según el Paciente elegido.</span>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label for="id_motivo_consulta">Motivo de Consulta</label>
											<textarea class="form-control" id="id_motivo_consulta" name="id_motivo_consulta" maxlength="300" rows="3"
												placeholder="(*) Ejemplo: Dolor lumbar, primera consulta"><?php echo $lb_edit?$array["V_MOT_CONSULTA"]:""; ?></textarea>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label for="id_diagnostico">Diagnóstico</label>
											<textarea class="form-control" id="id_diagnostico" name="id_diagnostico" maxlength="300" rows="3"
												placeholder="(Opcional) Diagnóstico del especialista"><?php echo $lb_edit?$array["V_DIAGNOSTICO"]:""; ?></textarea>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label for="id_observacion_cita">Observaciones</label>
											<textarea class="form-control" id="id_observacion_cita" name="id_observacion_cita" maxlength="300" rows="3"
												placeholder="(Opcional) Notas adicionales sobre la cita"><?php echo $lb_edit?$array["V_OBSERVACION"]:""; ?></textarea>
										</div>
									</div>
								</div>

								<?php if ($lb_edit && $array["V_MOT_CANC"]) { ?>
								<div class="row">
									<div class="col-sm-12">
										<div class="alert alert-warning">
											<strong>Motivo de cancelación registrado:</strong> <?php echo $array["V_MOT_CANC"]; ?>
										</div>
									</div>
								</div>
								<?php } ?>

								<?php if ($lb_edit && $array["V_MOT_REPROG"]) { ?>
								<div class="row">
									<div class="col-sm-12">
										<div class="alert alert-warning">
											<strong>Esta cita fue reprogramada.</strong> Motivo: <?php echo $array["V_MOT_REPROG"]; ?>
										</div>
									</div>
								</div>
								<?php } ?>

								<?php if ($lb_edit && $array["N_COD_CITA_ORIGEN"]) { ?>
								<div class="row">
									<div class="col-sm-12">
										<div class="alert alert-info">
											<strong>Esta cita proviene de una reprogramación</strong> de la cita
											<a href="mov_cita_editar.php?id_codigo=<?php echo intval($array["N_COD_CITA_ORIGEN"]); ?>">
												#<?php echo intval($array["N_COD_CITA_ORIGEN"]); ?>
											</a>.
										</div>
									</div>
								</div>
								<?php } ?>

								<?php if ($lb_edit && $array["V_ESTADO_CITA"] != 'CAN' && $array["V_ESTADO_CITA"] != 'ATE' && $array["V_ESTADO_CITA"] != 'REP' && $array["V_ESTADO_CITA"] != 'COM') { ?>
								<div class="row">
									<div class="col-sm-12">
										<div class="panel panel-default" id="panel_reprogramar" style="display:none;">
											<div class="panel-body">
												<h4><i class="fa fa-calendar"></i> Reprogramar Cita</h4>
												<div class="row">
													<div class="col-sm-4">
														<div class="form-group">
															<label for="id_nueva_fecha">Nueva Fecha</label>
															<input type="date" class="form-control" id="id_nueva_fecha">
														</div>
													</div>
													<div class="col-sm-4">
														<div class="form-group">
															<label for="id_nueva_hora_inicio">Nueva Hora Inicio</label>
															<input type="time" class="form-control" id="id_nueva_hora_inicio">
														</div>
													</div>
													<div class="col-sm-4">
														<div class="form-group">
															<label for="id_nueva_hora_fin">Nueva Hora Fin</label>
															<input type="time" class="form-control" id="id_nueva_hora_fin">
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-12">
														<div class="form-group">
															<label for="id_motivo_reprog">Motivo de la Reprogramación</label>
															<textarea class="form-control" id="id_motivo_reprog" rows="2"
																placeholder="(*) Ejemplo: Paciente solicitó cambio de horario"></textarea>
														</div>
													</div>
												</div>
												<button type="button" class="btn btn-warning" onclick="f_confirmar_reprogramacion()">Confirmar Reprogramación</button>
												<button type="button" class="btn btn-link" onclick="$('#panel_reprogramar').slideUp()">Cancelar</button>
											</div>
										</div>
									</div>
								</div>
								<?php } ?>

								</fieldset>

								<div class="row">
									<div class="col-sm-12">
										<?php if ($lb_edit) { ?>
											<?php if (!$lb_readonly) { ?>
											<input class="btn btn-success" type="submit" id="btn_actualizar" value="Actualizar"
												onclick="this.form.action='<?php echo $url_actualizar?>'">
											<?php } ?>
											<?php if ($array["V_ESTADO_CITA"] != 'CAN' && $array["V_ESTADO_CITA"] != 'ATE' && $array["V_ESTADO_CITA"] != 'REP' && $array["V_ESTADO_CITA"] != 'COM') { ?>
											<input class="btn btn-danger" type="button" id="btn_eliminar" value="Cancelar Cita"
												onclick="f_cancelar_cita_form()">
											<?php } ?>
											<?php if ($array["V_ESTADO_CITA"] != 'CAN' && $array["V_ESTADO_CITA"] != 'ATE' && $array["V_ESTADO_CITA"] != 'REP' && $array["V_ESTADO_CITA"] != 'COM') { ?>
											<input class="btn btn-warning" type="button" id="btn_reprogramar" value="Reprogramar"
												onclick="$('#panel_reprogramar').slideDown()">
											<?php } ?>
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

	<script>
	// Guardamos la lista completa de opciones (una sola vez) antes de filtrar nada,
	// porque ocultar <option> con CSS (.hide()/.show()) no es confiable en todos
	// los navegadores. Reconstruimos el <select> con solo las opciones que aplican.
	var _opcionesEspecialista = null;
	var _opcionesTratamiento  = null;

	function f_capturar_opciones_especialista() {
		if (_opcionesEspecialista !== null) return;
		_opcionesEspecialista = [];
		$('#id_cod_especialista option').each(function () {
			_opcionesEspecialista.push({
				value: $(this).val(),
				text: $(this).text(),
				sedes: ($(this).attr('data-sedes') || '').toString()
			});
		});
	}

	function f_capturar_opciones_tratamiento() {
		if (_opcionesTratamiento !== null) return;
		_opcionesTratamiento = [];
		$('#id_cod_tratamiento option').each(function () {
			_opcionesTratamiento.push({
				value: $(this).val(),
				text: $(this).text(),
				paciente: ($(this).attr('data-paciente') || '').toString()
			});
		});
	}

	function f_filtrar_especialistas_por_sede() {
		f_capturar_opciones_especialista();

		var sede = $('#id_cod_sede').val();
		var especialistaActual = $('#id_cod_especialista').val();
		var $select = $('#id_cod_especialista');

		$select.empty();

		$.each(_opcionesEspecialista, function (i, op) {
			// La opción vacía ("Seleccione opción") y las que no tienen sede asignada, siempre se muestran
			if (op.value === '' || op.sedes === '' || op.sedes.split(',').indexOf(String(sede)) !== -1) {
				var $opt = $('<option>').val(op.value).text(op.text).attr('data-sedes', op.sedes);
				if (op.value === '') {
					$opt.prop('disabled', true).prop('hidden', true);
				}
				if (op.value === especialistaActual) {
					$opt.prop('selected', true);
				}
				$select.append($opt);
			}
		});

		// Si el especialista seleccionado ya no quedó en la lista filtrada, limpiar
		if (especialistaActual !== '' && $select.find('option[value="' + especialistaActual + '"]').length === 0) {
			$select.val('');
		}
	}

	function f_filtrar_tratamientos_por_paciente() {
		f_capturar_opciones_tratamiento();

		var paciente = $('#id_cod_paciente').val();
		var tratamientoActual = $('#id_cod_tratamiento').val();
		var $select = $('#id_cod_tratamiento');

		$select.empty();

		$.each(_opcionesTratamiento, function (i, op) {
			// La opción "Ninguno / primera consulta" siempre se muestra
			if (op.value === '' || op.paciente === String(paciente)) {
				var $opt = $('<option>').val(op.value).text(op.text).attr('data-paciente', op.paciente);
				if (op.value === tratamientoActual) {
					$opt.prop('selected', true);
				}
				$select.append($opt);
			}
		});

		// Si el tratamiento seleccionado ya no quedó en la lista filtrada, limpiar
		if (tratamientoActual !== '' && $select.find('option[value="' + tratamientoActual + '"]').length === 0) {
			$select.val('');
		}
	}

	function f_calcular_hora_fin() {
		var duracion = parseInt($('#id_cod_tipoterapia option:selected').data('duracion'), 10);
		var horaInicio = $('#id_hora_inicio').val();

		if (!duracion || !horaInicio) {
			return;
		}

		var partes = horaInicio.split(':');
		var minutos = parseInt(partes[0], 10) * 60 + parseInt(partes[1], 10) + duracion;
		var horaFin = String(Math.floor(minutos / 60) % 24).padStart(2, '0') + ':' + String(minutos % 60).padStart(2, '0');

		$('#id_hora_fin').val(horaFin);
	}

	function f_cancelar_cita_form() {
		var motivo = prompt("Motivo de la cancelación de la cita:");
		if (motivo === null) {
			return;
		}
		if (motivo.trim() === "") {
			alert("Debe indicar un motivo para cancelar la cita.");
			return;
		}
		var form = document.getElementById('form_mtto');
		var inputCodigo = document.createElement('input');
		inputCodigo.type = 'hidden';
		inputCodigo.name = 'id_codigo';
		inputCodigo.value = $('#id_cod_cita_hide').val();
		form.appendChild(inputCodigo);

		var inputMotivo = document.createElement('input');
		inputMotivo.type = 'hidden';
		inputMotivo.name = 'id_motivo_cancelacion';
		inputMotivo.value = motivo;
		form.appendChild(inputMotivo);

		form.action = '<?php echo $url_eliminar; ?>';
		form.submit();
	}

	function f_confirmar_reprogramacion() {
		var nuevaFecha = $('#id_nueva_fecha').val();
		var nuevaHoraIni = $('#id_nueva_hora_inicio').val();
		var nuevaHoraFin = $('#id_nueva_hora_fin').val();
		var motivo = $('#id_motivo_reprog').val();

		if (!nuevaFecha || !nuevaHoraIni || !nuevaHoraFin) {
			alert("Complete fecha, hora de inicio y hora de fin de la nueva cita.");
			return;
		}
		if (!motivo || motivo.trim() === "") {
			alert("Debe indicar el motivo de la reprogramación.");
			return;
		}
		if (nuevaHoraFin <= nuevaHoraIni) {
			alert("La hora de fin debe ser posterior a la hora de inicio.");
			return;
		}

		var form = document.createElement('form');
		form.method = 'post';
		form.action = '<?php echo $url_reprogramar; ?>';

		function campoOculto(nombre, valor) {
			var input = document.createElement('input');
			input.type = 'hidden';
			input.name = nombre;
			input.value = valor;
			form.appendChild(input);
		}

		campoOculto('id_cod_cita_origen', $('#id_cod_cita_hide').val());
		campoOculto('id_nueva_fecha', nuevaFecha);
		campoOculto('id_nueva_hora_inicio', nuevaHoraIni);
		campoOculto('id_nueva_hora_fin', nuevaHoraFin);
		campoOculto('id_motivo_reprog', motivo);

		document.body.appendChild(form);
		form.submit();
	}

	$(document).ready(function () {
		f_filtrar_especialistas_por_sede();
		f_filtrar_tratamientos_por_paciente();

		$('#id_cod_sede').on('change', f_filtrar_especialistas_por_sede);
		$('#id_cod_paciente').on('change', f_filtrar_tratamientos_por_paciente);
		$('#id_cod_tipoterapia, #id_hora_inicio').on('change', f_calcular_hora_fin);
	});
	</script>

<?php
}

?>
