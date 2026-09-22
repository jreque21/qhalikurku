<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Función auxiliar: calcula lo pagado y el saldo pendiente de un comprobante
function f_calcular_saldo($crud, $li_cod_comprobante, $ln_monto_total) {

	$ls_condicion = "N_COD_COMPROBANTE = '$li_cod_comprobante' AND V_FLAG_ESTADO = '1'";
	$lr = $crud->fila_listar_solocondicion(
			"(SELECT SUM(N_MONTO) AS TOTAL_PAGADO FROM " . DEF_TABLA_PAGO . " WHERE $ls_condicion) t",
			'', '', -1, 0
		  );
	$row = $lr ? mysqli_fetch_assoc($lr) : null;
	$ln_pagado = $row && $row['TOTAL_PAGADO'] ? floatval($row['TOTAL_PAGADO']) : 0;

	return array(
		'pagado' => $ln_pagado,
		'saldo'  => round(floatval($ln_monto_total) - $ln_pagado, 2)
	);
}

// Lista de Cuentas por Cobrar (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta, $as_fec_ini_filtro = '', $as_fec_fin_filtro = '', $as_cod_paciente_filtro = '', $as_estado_filtro = ''){

	$url_abono = "mov_comprobante_abono.php";
	$url_lista = "mov_comprobante_lista.php";

	// Instanciar clase
	$crud = new crud();
	$bd   = new baseDatos();

	// Solo comprobantes a Crédito (Contado siempre se paga completo al momento)
	$ls_condicion = "V_FLAG_ESTADO = '1' AND N_COD_FORMAPAGO = '2'";

	if (!empty($as_fec_ini_filtro) && !empty($as_fec_fin_filtro)) {
		$ls_condicion .= " AND D_FEC_EMISION BETWEEN '" . $bd->bd_escapeCadena($as_fec_ini_filtro) . "' AND '" . $bd->bd_escapeCadena($as_fec_fin_filtro) . "'";
	} elseif (!empty($as_fec_ini_filtro)) {
		$ls_condicion .= " AND D_FEC_EMISION >= '" . $bd->bd_escapeCadena($as_fec_ini_filtro) . "'";
	} elseif (!empty($as_fec_fin_filtro)) {
		$ls_condicion .= " AND D_FEC_EMISION <= '" . $bd->bd_escapeCadena($as_fec_fin_filtro) . "'";
	}

	if (!empty($as_cod_paciente_filtro)) {
		$ls_condicion .= " AND N_COD_CITA IN (SELECT N_COD_CITA FROM " . DEF_TABLA_CITA . " WHERE N_COD_PACIENTE = '" . $bd->bd_escapeCadena($as_cod_paciente_filtro) . "')";
	}

	if (!empty($as_estado_filtro)) {
		$ls_condicion .= " AND V_ESTADO_COMPROBANTE = '" . $bd->bd_escapeCadena($as_estado_filtro) . "'";
	}

	$array = $crud->fila_listar_solocondicion(DEF_TABLA_COMPROBANTE . " WHERE $ls_condicion", 'D_FEC_EMISION', 'D', 0, 1000);

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
				<li><a href="mov_pago_lista.php">Pagos</a></li>
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
									<label>Estado</label>
									<select class="form-control" name="id_estado_filtro">
										<option value="">Todos</option>
										<option value="PEN" <?php echo $as_estado_filtro=='PEN'?'selected':''; ?>>Pendiente</option>
										<option value="PAG" <?php echo $as_estado_filtro=='PAG'?'selected':''; ?>>Pagado</option>
									</select>
								</div>
								&nbsp;
								<button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
								<a href="<?php echo $url_lista; ?>?id_fec_ini_filtro=&id_fec_fin_filtro=&id_cod_paciente_filtro=&id_estado_filtro=" class="btn btn-default">Ver Todos</a>
							</form>
						</div>
					</div>
					<!-- Fin Panel de Filtros -->

					<!-- Panel -->
					<div class="panel panel-primary">

						<!-- Cabecera -->
						<div class="box-header">
							<i class="fa fa-credit-card"></i>
							CUENTAS POR COBRAR (Pagos a Crédito)
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
												<th>Fecha</th>
												<th>Paciente</th>
												<th>Monto Total</th>
												<th>Pagado</th>
												<th>Saldo</th>
												<th>Estado</th>
												<th>Acciones</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {

												// Paciente (vía la cita)
												$array_campo_pk	= array('N_COD_CITA');
												$array_valor_pk	= array($row["N_COD_CITA"]);
												$li_cod_paciente = $crud->fila_recuperar_campo(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk, 'N_COD_PACIENTE');

												$array_campo_pk	= array('N_COD_PACIENTE');
												$array_valor_pk	= array($li_cod_paciente);
												$ls_pac_ape_p	= $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO');
												$ls_pac_ape_m	= $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');

												$array_campo_pk	= array('N_COD_MONEDA');
												$array_valor_pk	= array($row["N_COD_MONEDA"]);
												$ls_simbolo		= $crud->fila_recuperar_campo(DEF_TABLA_MONEDA, $array_campo_pk, $array_valor_pk, 'V_SIMBOLO');

												$as_saldo = f_calcular_saldo($crud, $row["N_COD_COMPROBANTE"], $row["N_MONTO"]);

												$ls_estado_css = $row["V_ESTADO_COMPROBANTE"] == 'PAG' ? 'label-success' : 'label-warning';
												$ls_estado_des = $row["V_ESTADO_COMPROBANTE"] == 'PAG' ? 'Pagado' : 'Pendiente';
												?>
												<tr>
													<td><?php echo date('d/m/Y', strtotime($row["D_FEC_EMISION"])); ?></td>
													<td><?php echo $ls_pac_ape_p.' '.$ls_pac_ape_m; ?></td>
													<td><?php echo $ls_simbolo.' '.number_format($row["N_MONTO"], 2); ?></td>
													<td><?php echo $ls_simbolo.' '.number_format($as_saldo['pagado'], 2); ?></td>
													<td><strong><?php echo $ls_simbolo.' '.number_format($as_saldo['saldo'], 2); ?></strong></td>
													<td><span class="label <?php echo $ls_estado_css;?>"><?php echo $ls_estado_des;?></span></td>
													<td align="center">
														<?php if ($row["V_ESTADO_COMPROBANTE"] == 'PEN') { ?>
														<a href="<?php echo $url_abono; ?>?id_codigo=<?php echo intval($row["N_COD_COMPROBANTE"]); ?>" class="btn btn-xs btn-success">
															<i class="fa fa-plus"></i> Abonar
														</a>
														<?php } else { ?>
															<span class="text-muted">-</span>
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

// Formulario de Registrar Abono
function f_formulario_abono($as_titulo, $as_icono, $as_msgRpta, $as_cod_comprobante) {

	$crud = new crud();

	$url_lista     = "mov_comprobante_lista.php";
	$url_registrar = "../controlador/mov_comprobante_abono_registrar.php";

	$array_campo_pk	= array('N_COD_COMPROBANTE');
	$array_valor_pk	= array($as_cod_comprobante);
	$array_comprobante = $crud->fila_recuperar(DEF_TABLA_COMPROBANTE, $array_campo_pk, $array_valor_pk);

	if ($array_comprobante === null) {
		echo "<div class='content-wrapper'><section class='content'><div class='alert alert-danger'>El comprobante indicado no existe.</div></section></div>";
		return;
	}

	// Paciente
	$array_campo_pk	= array('N_COD_CITA');
	$array_valor_pk	= array($array_comprobante["N_COD_CITA"]);
	$li_cod_paciente = $crud->fila_recuperar_campo(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk, 'N_COD_PACIENTE');

	$array_campo_pk	= array('N_COD_PACIENTE');
	$array_valor_pk	= array($li_cod_paciente);
	$ls_pac = $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO') . ' ' .
			  $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO') . ', ' .
			  $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_NOMBRES');

	// Moneda
	$array_campo_pk	= array('N_COD_MONEDA');
	$array_valor_pk	= array($array_comprobante["N_COD_MONEDA"]);
	$ls_simbolo = $crud->fila_recuperar_campo(DEF_TABLA_MONEDA, $array_campo_pk, $array_valor_pk, 'V_SIMBOLO');

	$as_saldo = f_calcular_saldo($crud, $as_cod_comprobante, $array_comprobante["N_MONTO"]);

	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo;?>
			</h1>
			<ol class="breadcrumb">
				<li><a href="<?php echo DEF_URL_LOGIN;?>"><i class="fa fa-home"></i> Inicio</a></li>
				<li class="active"><a href="<?php echo $url_lista?>">Volver a Cuentas por Cobrar</a></li>
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

					<div class="panel panel-default">
						<div class="panel-body">
							<strong>Paciente:</strong> <?php echo $ls_pac; ?><br>
							<strong>Monto Total:</strong> <?php echo $ls_simbolo.' '.number_format($array_comprobante["N_MONTO"], 2); ?> &nbsp;|&nbsp;
							<strong>Pagado:</strong> <?php echo $ls_simbolo.' '.number_format($as_saldo['pagado'], 2); ?> &nbsp;|&nbsp;
							<strong>Saldo Pendiente:</strong> <span class="text-danger"><strong><?php echo $ls_simbolo.' '.number_format($as_saldo['saldo'], 2); ?></strong></span>
						</div>
					</div>

					<?php
					// Historial de abonos ya registrados
					$array_campo_pk	= array('N_COD_COMPROBANTE', 'V_FLAG_ESTADO');
					$array_valor_pk	= array($as_cod_comprobante, '1');
					$array_pagos = $crud->fila_listar(DEF_TABLA_PAGO, $array_campo_pk, $array_valor_pk, 'D_FEC_PAGO', 'A', 0, 100);

					if ($array_pagos && $array_pagos->num_rows > 0) {
						?>
						<div class="panel panel-default">
							<div class="panel-body">
								<strong>Historial de Abonos</strong>
								<table class="table table-condensed" style="margin-top:10px;">
									<thead><tr><th>Fecha</th><th>Monto</th><th>Observación</th></tr></thead>
									<tbody>
										<?php while ($this_pago = mysqli_fetch_assoc($array_pagos)) { ?>
										<tr>
											<td><?php echo date('d/m/Y', strtotime($this_pago["D_FEC_PAGO"])); ?></td>
											<td><?php echo $ls_simbolo.' '.number_format($this_pago["N_MONTO"], 2); ?></td>
											<td><?php echo $this_pago["V_OBSERVACION"]; ?></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
						<?php
					}
					?>

					<div class="panel panel-primary">
						<div class="box-header"><i class="fa fa-plus"></i> Registrar Abono</div>
						<div class="panel-body">

							<?php if ($array_comprobante["V_ESTADO_COMPROBANTE"] == 'PAG') { ?>
								<div class="alert alert-info">Este comprobante ya está pagado en su totalidad.</div>
							<?php } else { ?>

							<form id="form_mtto" role="form" method="post" action="<?php echo $url_registrar; ?>" autocomplete="off">

								<input type="hidden" name="id_cod_comprobante" value="<?php echo $as_cod_comprobante; ?>">

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fec_pago">Fecha del Abono</label>
											<input type="date" class="form-control" id="id_fec_pago" name="id_fec_pago" required value="<?php echo date('Y-m-d'); ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_monto_abono">Monto a Abonar (máx. <?php echo number_format($as_saldo['saldo'], 2); ?>)</label>
											<input type="number" step="0.01" min="0.01" max="<?php echo $as_saldo['saldo']; ?>" class="form-control" id="id_monto_abono" name="id_monto_abono" required>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_mediopago">Medio de Pago</label>
											<select class="form-control" id="id_cod_mediopago" name="id_cod_mediopago" required>
												<?php
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_mp       = $crud->fila_listar(DEF_TABLA_MEDIOPAGO, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA', 'A', 0, 20);
												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_mp = mysqli_fetch_assoc($array_mp)) {
													echo "<option value=\"".$this_mp["N_COD_MEDIOPAGO"]."\">".$this_mp["V_DES_CORTA"]."</option>\n";
												}
												?>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label for="id_observacion">Observación</label>
											<textarea class="form-control" id="id_observacion" name="id_observacion" rows="2"></textarea>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-12">
										<input class="btn btn-success" type="submit" value="Registrar Abono">
										<a class="btn btn-default" href="<?php echo $url_lista; ?>">Volver a Cuentas por Cobrar</a>
									</div>
								</div>

							</form>

							<?php } ?>

						</div>
					</div>

				</section>
			</div>

		</section>

	</div>

<?php
}

?>
