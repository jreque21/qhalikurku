<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Pagos (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta, $as_fec_ini_filtro = '', $as_fec_fin_filtro = '', $as_cod_paciente_filtro = '', $as_cod_formapago_filtro = ''){

	// Enlaces
	$url_nuevo		= "mov_pago_nuevo.php";
	$url_editar		= "mov_pago_editar.php";
	$url_eliminar	= "../controlador/mov_pago_eliminar.php";
	$url_lista		= "mov_pago_lista.php";

	// Instanciar clase
	$crud = new crud();
	$bd   = new baseDatos();

	// Armar condición dinámica según los filtros elegidos
	$ls_condicion = "V_FLAG_ESTADO = '1'";

	if (!empty($as_fec_ini_filtro) && !empty($as_fec_fin_filtro)) {
		$ls_condicion .= " AND D_FEC_PAGO BETWEEN '" . $bd->bd_escapeCadena($as_fec_ini_filtro) . "' AND '" . $bd->bd_escapeCadena($as_fec_fin_filtro) . "'";
	} elseif (!empty($as_fec_ini_filtro)) {
		$ls_condicion .= " AND D_FEC_PAGO >= '" . $bd->bd_escapeCadena($as_fec_ini_filtro) . "'";
	} elseif (!empty($as_fec_fin_filtro)) {
		$ls_condicion .= " AND D_FEC_PAGO <= '" . $bd->bd_escapeCadena($as_fec_fin_filtro) . "'";
	}

	// Filtro por paciente: vía subconsulta (evita ambigüedad de columnas con JOIN)
	if (!empty($as_cod_paciente_filtro)) {
		$ls_condicion .= " AND N_COD_CITA IN (SELECT N_COD_CITA FROM " . DEF_TABLA_CITA . " WHERE N_COD_PACIENTE = '" . $bd->bd_escapeCadena($as_cod_paciente_filtro) . "')";
	}

	// Filtro por forma de pago: vía subconsulta sobre el comprobante
	if (!empty($as_cod_formapago_filtro)) {
		$ls_condicion .= " AND N_COD_COMPROBANTE IN (SELECT N_COD_COMPROBANTE FROM " . DEF_TABLA_COMPROBANTE . " WHERE N_COD_FORMAPAGO = '" . $bd->bd_escapeCadena($as_cod_formapago_filtro) . "')";
	}

	// Listado (más recientes primero)
	$array = $crud->fila_listar_solocondicion(DEF_TABLA_PAGO . " WHERE $ls_condicion", 'D_FEC_PAGO', 'D', 0, 1000);

	// Total recaudado (solo para mostrar un resumen rápido)
	$ln_total = 0;

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
									<label>Fecha Inicio</label>
									<input type="date" class="form-control" name="id_fec_ini_filtro" value="<?php echo htmlspecialchars($as_fec_ini_filtro); ?>">
								</div>
								&nbsp;
								<div class="form-group">
									<label>Fecha Fin</label>
									<input type="date" class="form-control" name="id_fec_fin_filtro" value="<?php echo htmlspecialchars($as_fec_fin_filtro); ?>">
								</div>
								&nbsp;
								<div class="form-group">
									<label>Paciente</label>
									<select class="form-control" name="id_cod_paciente_filtro">
										<option value="">Todos</option>
										<?php
										$array_campo_pk	= array('V_FLAG_ESTADO');
										$array_valor_pk	= array('1');
										$array_pac_filtro = $crud->fila_listar(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO, V_APE_MATERNO, V_NOMBRES', 'A', 0, 999);
										while ($this_pac_filtro = mysqli_fetch_assoc($array_pac_filtro)) {
											echo "<option value=\"".$this_pac_filtro["N_COD_PACIENTE"]."\"";
											if ($as_cod_paciente_filtro == $this_pac_filtro["N_COD_PACIENTE"]) echo " selected";
											echo ">".$this_pac_filtro["V_APE_PATERNO"].' '.$this_pac_filtro["V_APE_MATERNO"].', '.$this_pac_filtro["V_NOMBRES"]."</option>\n";
										}
										?>
									</select>
								</div>
								&nbsp;
								<div class="form-group">
									<label>Forma de Pago</label>
									<select class="form-control" name="id_cod_formapago_filtro">
										<option value="">Todas</option>
										<?php
										$array_campo_pk	= array('V_FLAG_ESTADO');
										$array_valor_pk	= array('1');
										$array_fp_filtro = $crud->fila_listar(DEF_TABLA_FORMAPAGO, $array_campo_pk, $array_valor_pk, 'N_COD_FORMAPAGO', 'A', 0, 20);
										while ($this_fp_filtro = mysqli_fetch_assoc($array_fp_filtro)) {
											echo "<option value=\"".$this_fp_filtro["N_COD_FORMAPAGO"]."\"";
											if ($as_cod_formapago_filtro == $this_fp_filtro["N_COD_FORMAPAGO"]) echo " selected";
											echo ">".$this_fp_filtro["V_DES_CORTA"]."</option>\n";
										}
										?>
									</select>
								</div>
								&nbsp;
								<button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
								<a href="<?php echo $url_lista; ?>?id_fec_ini_filtro=&id_fec_fin_filtro=&id_cod_paciente_filtro=&id_cod_formapago_filtro=" class="btn btn-default">Ver Todos</a>
							</form>
						</div>
					</div>
					<!-- Fin Panel de Filtros -->

					<!-- Panel -->
					<div class="panel panel-primary">

						<!-- Cabecera -->
						<div class="box-header">
							<i class="fa fa-money"></i>
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
												<th>Fecha de Pago</th>
												<th>Paciente</th>
												<th>Comprobante</th>
												<th>Forma de Pago</th>
												<th>Medio de Pago</th>
												<th>Monto Pagado</th>
												<th>Total del Comprobante</th>
												<th>Saldo Pendiente</th>
												<th>Acciones</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {

												$url_editar_fila = $url_editar."?id_codigo=".intval($row["N_COD_PAGO"]);

												// Paciente (vía la cita)
												$array_campo_pk	= array('N_COD_CITA');
												$array_valor_pk	= array($row["N_COD_CITA"]);
												$li_cod_paciente = $crud->fila_recuperar_campo(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk, 'N_COD_PACIENTE');

												$array_campo_pk	= array('N_COD_PACIENTE');
												$array_valor_pk	= array($li_cod_paciente);
												$ls_pac_ape_p	= $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO');
												$ls_pac_ape_m	= $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');
												$ls_pac_nombres	= $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_NOMBRES');

												// Moneda
												$array_campo_pk	= array('N_COD_MONEDA');
												$array_valor_pk	= array($row["N_COD_MONEDA"]);
												$ls_simbolo		= $crud->fila_recuperar_campo(DEF_TABLA_MONEDA, $array_campo_pk, $array_valor_pk, 'V_SIMBOLO');

												// Medio de pago
												$array_campo_pk	= array('N_COD_MEDIOPAGO');
												$array_valor_pk	= array($row["N_COD_MEDIOPAGO"]);
												$ls_mediopago	= $crud->fila_recuperar_campo(DEF_TABLA_MEDIOPAGO, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');

												// Comprobante (para saber Forma de Pago, Monto Total y Saldo)
												$array_campo_pk	= array('N_COD_COMPROBANTE');
												$array_valor_pk	= array($row["N_COD_COMPROBANTE"]);
												$array_comp		= $crud->fila_recuperar(DEF_TABLA_COMPROBANTE, $array_campo_pk, $array_valor_pk);

												$array_campo_pk	= array('N_COD_FORMAPAGO');
												$array_valor_pk	= array($array_comp["N_COD_FORMAPAGO"]);
												$ls_formapago	= $crud->fila_recuperar_campo(DEF_TABLA_FORMAPAGO, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');

												$lb_es_credito = ($array_comp["N_COD_FORMAPAGO"] == '2');

												if ($lb_es_credito) {
													// Cuánto se ha abonado en total a este comprobante (todas las cuotas)
													$ls_cond_pagos = "N_COD_COMPROBANTE = '".$row["N_COD_COMPROBANTE"]."' AND V_FLAG_ESTADO = '1'";
													$lr_sum = $crud->fila_listar_solocondicion("(SELECT SUM(N_MONTO) AS TOTAL FROM ".DEF_TABLA_PAGO." WHERE $ls_cond_pagos) t", '', '', -1, 0);
													$row_sum = $lr_sum ? mysqli_fetch_assoc($lr_sum) : null;
													$ln_abonado_comp = $row_sum && $row_sum['TOTAL'] ? floatval($row_sum['TOTAL']) : 0;
													$ln_saldo_comp = round(floatval($array_comp["N_MONTO"]) - $ln_abonado_comp, 2);
												}

												$ln_total += floatval($row["N_MONTO"]);
												?>
												<tr>
													<td><?php echo intval($row["N_COD_PAGO"]); ?></td>
													<td><?php echo date('d/m/Y', strtotime($row["D_FEC_PAGO"])); ?></td>
													<td><a href="<?php echo $url_editar_fila?>"><?php echo $ls_pac_ape_p.' '.$ls_pac_ape_m.', '.$ls_pac_nombres; ?></a></td>
													<td><?php echo $row["V_COMPROBANTE"] ? $row["V_COMPROBANTE"] : '-'; ?></td>
													<td>
														<span class="label <?php echo $lb_es_credito ? 'label-warning' : 'label-success'; ?>">
															<?php echo $ls_formapago; ?>
														</span>
													</td>
													<td><?php echo $ls_mediopago; ?></td>
													<td><?php echo $ls_simbolo.' '.number_format($row["N_MONTO"], 2); ?></td>
													<td><?php echo $lb_es_credito ? $ls_simbolo.' '.number_format($array_comp["N_MONTO"], 2) : '-'; ?></td>
													<td>
														<?php if ($lb_es_credito) { ?>
															<?php if ($ln_saldo_comp > 0) { ?>
																<strong class="text-danger"><?php echo $ls_simbolo.' '.number_format($ln_saldo_comp, 2); ?></strong>
															<?php } else { ?>
																<span class="label label-success">Saldado</span>
															<?php } ?>
														<?php } else { ?>
															-
														<?php } ?>
													</td>
													<td align="center">
														<a href="<?php echo $url_editar_fila?>">
															<i class="fa fa-edit" title="Editar"></i>
														</a>
														&nbsp;
														<a href="../controlador/mov_pago_recibo_pdf.php?id_codigo=<?php echo intval($row["N_COD_PAGO"]); ?>" target="_blank">
															<i class="fa fa-file-pdf-o text-danger" title="Imprimir Recibo"></i>
														</a>
													</td>
												</tr>
												<?php
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="6" style="text-align:right">Total mostrado:</th>
												<th colspan="4"><?php echo 'S/ '.number_format($ln_total, 2); ?></th>
											</tr>
										</tfoot>

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
						<span class="glyphicon glyphicon-plus"></span>&nbsp;Nuevo Pago
					</a>
					<a class="btn btn-warning" href="mov_comprobante_lista.php" role="button">
						<i class="fa fa-credit-card"></i>&nbsp;Cuentas por Cobrar
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
function f_formulario($as_titulo, $as_icono, $as_msgRpta, $array = "", $as_cod_cita_pre = '') {

	// Inicalizando variables
	$lb_edit = is_array($array);

	// Instanciar clase
	$crud = new crud();

	// Datos del comprobante y regla de solo-lectura (solo aplica en modo edición)
	$array_comprobante_edit = null;
	$ls_formapago_edit = '';
	$lb_readonly = false;

	if ($lb_edit) {

		$array_campo_pk = array('N_COD_COMPROBANTE');
		$array_valor_pk = array($array["N_COD_COMPROBANTE"]);
		$array_comprobante_edit = $crud->fila_recuperar(DEF_TABLA_COMPROBANTE, $array_campo_pk, $array_valor_pk);

		$array_campo_pk = array('N_COD_FORMAPAGO');
		$array_valor_pk = array($array_comprobante_edit["N_COD_FORMAPAGO"]);
		$ls_formapago_edit = $crud->fila_recuperar_campo(DEF_TABLA_FORMAPAGO, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');

		// Cuántos pagos/abonos tiene este comprobante en total
		$array_campo_pk = array('N_COD_COMPROBANTE', 'V_FLAG_ESTADO');
		$array_valor_pk = array($array["N_COD_COMPROBANTE"], '1');
		$li_cant_pagos = $crud->fila_contar(DEF_TABLA_PAGO, $array_campo_pk, $array_valor_pk);

		// Solo lectura si: el comprobante ya está en estado final (Pagado),
		// o si es a Crédito y ya se registraron cuotas/abonos además de este pago
		if ($array_comprobante_edit["V_ESTADO_COMPROBANTE"] == 'PAG' || intval($li_cant_pagos) > 1) {
			$lb_readonly = true;
		}
	}

	// Enlaces
	$url_lista		= "mov_pago_lista.php";
	$url_registrar	= "../controlador/mov_pago_registrar.php";
	$url_actualizar	= "../controlador/mov_pago_actualizar.php";
	$url_eliminar	= "../controlador/mov_pago_eliminar.php";

	if($lb_edit) {
		$ls_modo = DEF_MSG_FORM_EDICION;
	}else{
		$ls_modo = DEF_MSG_FORM_NUEVO;
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

								<input type="hidden" id="id_cod_pago_hide" name="id_cod_pago_hide"
									value="<?php echo $lb_edit?$array["N_COD_PAGO"]:""; ?>">
								<input type="hidden" id="id_cod_comprobante_hide" name="id_cod_comprobante_hide"
									value="<?php echo $lb_edit?$array["N_COD_COMPROBANTE"]:""; ?>">

								<?php if ($lb_readonly) { ?>
								<div class="alert alert-info">
									<i class="fa fa-lock"></i>
									<?php if ($array_comprobante_edit["V_ESTADO_COMPROBANTE"] == 'PAG') { ?>
										Este comprobante ya está <strong>Pagado en su totalidad</strong>, no puede modificarse.
									<?php } else { ?>
										Este pago es a <strong>Crédito</strong> y ya tiene cuotas/abonos registrados, no puede modificarse. Usa <a href="mov_comprobante_lista.php">Cuentas por Cobrar</a> para gestionar los abonos.
									<?php } ?>
								</div>
								<?php } ?>

								<fieldset <?php echo $lb_readonly ? 'disabled' : ''; ?>>

								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label for="id_cod_cita">Cita</label>
											<?php if ($lb_edit) { ?>
												<?php
												$array_campo_pk	= array('N_COD_CITA');
												$array_valor_pk	= array($array["N_COD_CITA"]);
												$array_cita_edit = $crud->fila_recuperar(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk);

												$array_campo_pk	= array('N_COD_PACIENTE');
												$array_valor_pk	= array($array_cita_edit["N_COD_PACIENTE"]);
												$ls_pac = $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO') . ' ' .
														  $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');
												?>
												<input type="text" class="form-control" disabled
													value="<?php echo '#'.intval($array["N_COD_CITA"]).' - '.$ls_pac.' - '.date('d/m/Y', strtotime($array_cita_edit["D_FEC_CITA"])).' '.substr($array_cita_edit["D_HORA_INICIO"],0,5); ?>">
											<?php } else { ?>
												<select class="form-control" id="id_cod_cita" name="id_cod_cita" required>
													<?php
													// Solo citas activas que aún no tienen comprobante registrado (ni contado ni crédito)
													$ls_condicion = "c.V_FLAG_ESTADO = '1'
															AND c.V_ESTADO_CITA <> 'CAN'
															AND NOT EXISTS (SELECT 1 FROM MOV_COMPROBANTE cp WHERE cp.N_COD_CITA = c.N_COD_CITA AND cp.V_FLAG_ESTADO = '1')";
													$array_cita = $crud->fila_listar_solocondicion(DEF_TABLA_CITA.' c WHERE '.$ls_condicion, 'c.D_FEC_CITA', 'D', 0, 999);

													echo "<option value='' selected disabled hidden>Seleccione opción</option>";
													if ($array_cita) {
														while ($this_cita = mysqli_fetch_assoc($array_cita)) {

															$array_campo_pk	= array('N_COD_PACIENTE');
															$array_valor_pk	= array($this_cita["N_COD_PACIENTE"]);
															$ls_pac = $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO') . ' ' .
																	  $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');

															$ln_precio_default = 0;
															if (!empty($this_cita["N_COD_TIPOTERAPIA"])) {
																$array_campo_pk	= array('N_COD_TIPOTERAPIA');
																$array_valor_pk	= array($this_cita["N_COD_TIPOTERAPIA"]);
																$ln_precio_default = $crud->fila_recuperar_campo(DEF_TABLA_TIPOTERAPIA, $array_campo_pk, $array_valor_pk, 'N_PRECIO');
															}

															echo "<option value=\"".$this_cita["N_COD_CITA"]."\" data-monto=\"".floatval($ln_precio_default)."\"";
															if ($as_cod_cita_pre == $this_cita["N_COD_CITA"]) {
																echo " selected";
															}
															echo ">#".intval($this_cita["N_COD_CITA"]).' - '.$ls_pac.' - '.date('d/m/Y', strtotime($this_cita["D_FEC_CITA"])).' '.substr($this_cita["D_HORA_INICIO"],0,5)."\n";
														}
													}
													?>
												</select>
												<span class="help-block">Solo se muestran citas activas sin pago registrado. Al elegir, sugiere el monto según el tipo de terapia.</span>
											<?php } ?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="id_fec_pago">Fecha de Pago</label>
											<input type="date" class="form-control" id="id_fec_pago" name="id_fec_pago" required
												value="<?php echo $lb_edit?$array["D_FEC_PAGO"]:date('Y-m-d'); ?>">
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="id_cod_moneda">Moneda</label>
											<select class="form-control" id="id_cod_moneda" name="id_cod_moneda" required>
												<?php
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_mon      = $crud->fila_listar(DEF_TABLA_MONEDA, $array_campo_pk, $array_valor_pk, 'N_COD_MONEDA', 'A', 0, 20);

												while ($this_mon = mysqli_fetch_assoc($array_mon)) {
													echo "<option value=\"".$this_mon["N_COD_MONEDA"]."\"";
													if ($lb_edit && $this_mon["N_COD_MONEDA"] == $array["N_COD_MONEDA"]){
														echo " selected";
													} elseif (!$lb_edit && $this_mon["V_DES_CORTA"] == 'Soles') {
														echo " selected";
													}
													echo ">".$this_mon["V_DES_CORTA"]." (".$this_mon["V_SIMBOLO"].")\n";
												}
												?>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_formapago">Forma de Pago</label>
											<?php if (!$lb_edit) { ?>
											<select class="form-control" id="id_cod_formapago" name="id_cod_formapago" required>
												<?php
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_fp       = $crud->fila_listar(DEF_TABLA_FORMAPAGO, $array_campo_pk, $array_valor_pk, 'N_COD_FORMAPAGO', 'A', 0, 20);
												while ($this_fp = mysqli_fetch_assoc($array_fp)) {
													echo "<option value=\"".$this_fp["N_COD_FORMAPAGO"]."\"";
													if ($this_fp["N_COD_FORMAPAGO"] == '1') echo " selected";
													echo ">".$this_fp["V_DES_CORTA"]."\n";
												}
												?>
											</select>
											<?php } else { ?>
											<input type="text" class="form-control" disabled value="<?php echo $ls_formapago_edit; ?>">
											<span class="help-block">La forma de pago no puede cambiarse después de creado el comprobante.</span>
											<?php } ?>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_monto"><?php echo $lb_edit ? 'Monto' : 'Monto Total'; ?></label>
											<input type="number" step="0.01" min="0.01" class="form-control" id="id_monto" name="id_monto" required
												value="<?php echo $lb_edit?$array["N_MONTO"]:""; ?>">
											<?php if (!$lb_edit) { ?>
											<span class="help-block" id="ayuda_monto">Si es al Crédito, ingresa el monto total que debe el paciente (sin adelanto).</span>
											<?php } ?>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_mediopago">Medio de Pago<?php echo $lb_edit ? '' : ' <small>(solo si es al Contado)</small>'; ?></label>
											<select class="form-control" id="id_cod_mediopago" name="id_cod_mediopago" <?php echo $lb_edit ? 'required' : ''; ?>>
												<?php
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_mp       = $crud->fila_listar(DEF_TABLA_MEDIOPAGO, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA', 'A', 0, 20);

												echo "<option value=''".($lb_edit ? " selected disabled hidden" : "").">Seleccione opción</option>";
												while ($this_mp = mysqli_fetch_assoc($array_mp)) {
													echo "<option value=\"".$this_mp["N_COD_MEDIOPAGO"]."\"";
													if ($lb_edit && $this_mp["N_COD_MEDIOPAGO"] == $array["N_COD_MEDIOPAGO"]){
														echo " selected";
													}
													echo ">".$this_mp["V_DES_CORTA"]."\n";
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_comprobante">N° de Comprobante / Boleta</label>
											<input type="text" class="form-control" id="id_comprobante" name="id_comprobante" maxlength="50"
												placeholder="(Opcional) Ej: B001-000123"
												value="<?php echo $lb_edit?$array["V_COMPROBANTE"]:""; ?>">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label for="id_observacion">Observación</label>
											<textarea class="form-control" id="id_observacion" name="id_observacion" rows="2"><?php echo $lb_edit?$array["V_OBSERVACION"]:""; ?></textarea>
										</div>
									</div>
								</div>

								</fieldset>

								<div class="row">
									<div class="col-sm-12">
										<?php if ($lb_edit) { ?>
											<?php if (!$lb_readonly) { ?>
											<input class="btn btn-success" type="submit" id="btn_actualizar" value="Actualizar"
												onclick="this.form.action='<?php echo $url_actualizar?>'">
											<?php } ?>
											<a class="btn btn-danger" href="../controlador/mov_pago_recibo_pdf.php?id_codigo=<?php echo intval($array["N_COD_PAGO"]); ?>" target="_blank">
												<i class="fa fa-file-pdf-o"></i> Imprimir Recibo
											</a>
											<?php if (!$lb_readonly) { ?>
											<input class="btn btn-danger" type="submit" id="btn_eliminar" value="Eliminar" formnovalidate
												onclick="return confirm('¿Está seguro de eliminar este pago y su comprobante?') && (this.form.action='<?php echo $url_eliminar?>')">
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

	<?php if (!$lb_edit) { ?>
	<script>
	$(document).ready(function () {
		$('#id_cod_cita').on('change', function () {
			var monto = parseFloat($(this).find('option:selected').data('monto'));
			if (monto && monto > 0 && !$('#id_monto').val()) {
				$('#id_monto').val(monto.toFixed(2));
			}
		});

		function f_toggle_mediopago() {
			if ($('#id_cod_formapago').val() === '2') {
				// Crédito: el medio de pago se define en cada abono, no aquí
				$('#id_cod_mediopago').val('').prop('disabled', true).prop('required', false);
				$('#ayuda_monto').text('Ingresa el monto TOTAL que debe el paciente. El medio de pago se registrará en cada abono, desde "Cuentas por Cobrar".');
			} else {
				$('#id_cod_mediopago').prop('disabled', false).prop('required', true);
				$('#ayuda_monto').text('');
			}
		}

		$('#id_cod_formapago').on('change', f_toggle_mediopago);
		f_toggle_mediopago();

		<?php if (!empty($as_cod_cita_pre)) { ?>
		$('#id_cod_cita').trigger('change');
		<?php } ?>
	});
	</script>
	<?php } ?>

<?php
}

?>
