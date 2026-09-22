<?php

// Incluye Librería BD
require_once("../config/funciones.php");

function f_reporte($as_titulo, $as_icono, $as_fec_ini, $as_fec_fin){

	$crud = new crud();
	$bd   = new baseDatos();

	$ls_fec_ini = $bd->bd_escapeCadena($as_fec_ini);
	$ls_fec_fin = $bd->bd_escapeCadena($as_fec_fin);

	// Detalle de pagos en el rango
	$ls_cond = "D_FEC_PAGO BETWEEN '$ls_fec_ini' AND '$ls_fec_fin' AND V_FLAG_ESTADO = '1'";
	$array_pagos = $crud->fila_listar_solocondicion(DEF_TABLA_PAGO . " WHERE $ls_cond", 'D_FEC_PAGO', 'A', 0, 2000);

	$ln_total_general = 0;
	$array_x_formapago = array();  // [cod_formapago] => monto
	$array_x_mediopago = array();  // [cod_mediopago] => monto
	$array_detalle = array();

	if ($array_pagos) {
		while ($row = mysqli_fetch_assoc($array_pagos)) {

			$ln_total_general += floatval($row['N_MONTO']);

			// Forma de pago (vía el comprobante)
			$array_campo_pk = array('N_COD_COMPROBANTE');
			$array_valor_pk = array($row['N_COD_COMPROBANTE']);
			$li_cod_formapago = $crud->fila_recuperar_campo(DEF_TABLA_COMPROBANTE, $array_campo_pk, $array_valor_pk, 'N_COD_FORMAPAGO');

			if (!isset($array_x_formapago[$li_cod_formapago])) {
				$array_x_formapago[$li_cod_formapago] = 0;
			}
			$array_x_formapago[$li_cod_formapago] += floatval($row['N_MONTO']);

			// Medio de pago
			if (!isset($array_x_mediopago[$row['N_COD_MEDIOPAGO']])) {
				$array_x_mediopago[$row['N_COD_MEDIOPAGO']] = 0;
			}
			$array_x_mediopago[$row['N_COD_MEDIOPAGO']] += floatval($row['N_MONTO']);

			$array_detalle[] = $row;
		}
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
				<li><a href="rpt_reportes_lista.php">Reportes</a></li>
			</ol>
		</section>

		<section class="content">

			<!-- Filtros -->
			<div class="panel panel-default">
				<div class="panel-body">
					<form role="form" method="get" action="" class="form-inline">
						<div class="form-group">
							<label>Desde</label>
							<input type="date" class="form-control" name="id_fec_ini" value="<?php echo $as_fec_ini; ?>">
						</div>
						&nbsp;
						<div class="form-group">
							<label>Hasta</label>
							<input type="date" class="form-control" name="id_fec_fin" value="<?php echo $as_fec_fin; ?>">
						</div>
						&nbsp;
						<button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Consultar</button>
						<button type="button" class="btn btn-default" onclick="window.print()"><i class="fa fa-print"></i> Imprimir</button>
						<a class="btn btn-success" href="../controlador/rpt_ingresos_export.php?formato=csv&id_fec_ini=<?php echo $as_fec_ini; ?>&id_fec_fin=<?php echo $as_fec_fin; ?>">
							<i class="fa fa-file-excel-o"></i> Excel
						</a>
						<a class="btn btn-danger" href="../controlador/rpt_ingresos_export.php?formato=pdf&id_fec_ini=<?php echo $as_fec_ini; ?>&id_fec_fin=<?php echo $as_fec_fin; ?>" target="_blank">
							<i class="fa fa-file-pdf-o"></i> PDF
						</a>
					</form>
				</div>
			</div>

			<!-- Total general -->
			<div class="row">
				<div class="col-md-4">
					<div class="small-box bg-green">
						<div class="inner">
							<h3>S/ <?php echo number_format($ln_total_general, 2); ?></h3>
							<p>Total del Periodo (<?php echo date('d/m/Y', strtotime($as_fec_ini)).' - '.date('d/m/Y', strtotime($as_fec_fin)); ?>)</p>
						</div>
						<div class="icon"><i class="fa fa-money"></i></div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="panel panel-default">
						<div class="box-header"><i class="fa fa-pie-chart"></i> Por Forma de Pago</div>
						<div class="panel-body">
							<table class="table table-condensed">
								<?php
								$array_campo_pk	= array('V_FLAG_ESTADO');
								$array_valor_pk	= array('1');
								$array_fp = $crud->fila_listar(DEF_TABLA_FORMAPAGO, $array_campo_pk, $array_valor_pk, 'N_COD_FORMAPAGO', 'A', 0, 20);
								while ($this_fp = mysqli_fetch_assoc($array_fp)) {
									$ln_monto = isset($array_x_formapago[$this_fp['N_COD_FORMAPAGO']]) ? $array_x_formapago[$this_fp['N_COD_FORMAPAGO']] : 0;
									?>
									<tr>
										<td><?php echo $this_fp['V_DES_CORTA']; ?></td>
										<td class="text-right"><strong>S/ <?php echo number_format($ln_monto, 2); ?></strong></td>
									</tr>
									<?php
								}
								?>
							</table>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="panel panel-default">
						<div class="box-header"><i class="fa fa-credit-card"></i> Por Medio de Pago</div>
						<div class="panel-body">
							<table class="table table-condensed">
								<?php
								$array_campo_pk	= array('V_FLAG_ESTADO');
								$array_valor_pk	= array('1');
								$array_mp = $crud->fila_listar(DEF_TABLA_MEDIOPAGO, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA', 'A', 0, 20);
								while ($this_mp = mysqli_fetch_assoc($array_mp)) {
									$ln_monto = isset($array_x_mediopago[$this_mp['N_COD_MEDIOPAGO']]) ? $array_x_mediopago[$this_mp['N_COD_MEDIOPAGO']] : 0;
									?>
									<tr>
										<td><?php echo $this_mp['V_DES_CORTA']; ?></td>
										<td class="text-right"><strong>S/ <?php echo number_format($ln_monto, 2); ?></strong></td>
									</tr>
									<?php
								}
								?>
							</table>
						</div>
					</div>
				</div>
			</div>

			<!-- Detalle -->
			<div class="panel panel-primary">
				<div class="box-header"><i class="fa fa-list"></i> Detalle de Pagos</div>
				<div class="panel-body">
					<?php if (empty($array_detalle)) { ?>
						<div class="alert alert-warning">No se encontraron pagos en el periodo seleccionado.</div>
					<?php } else { ?>
						<div class="table-responsive">
							<table class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>Fecha</th>
										<th>Paciente</th>
										<th>Comprobante</th>
										<th>Medio de Pago</th>
										<th>Monto</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($array_detalle as $row) {

										$array_campo_pk	= array('N_COD_CITA');
										$array_valor_pk	= array($row["N_COD_CITA"]);
										$li_cod_paciente = $crud->fila_recuperar_campo(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk, 'N_COD_PACIENTE');

										$array_campo_pk	= array('N_COD_PACIENTE');
										$array_valor_pk	= array($li_cod_paciente);
										$ls_pac = $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO') . ' ' .
												  $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');

										$array_campo_pk	= array('N_COD_MEDIOPAGO');
										$array_valor_pk	= array($row["N_COD_MEDIOPAGO"]);
										$ls_mediopago = $crud->fila_recuperar_campo(DEF_TABLA_MEDIOPAGO, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
										?>
										<tr>
											<td><?php echo date('d/m/Y', strtotime($row["D_FEC_PAGO"])); ?></td>
											<td><?php echo $ls_pac; ?></td>
											<td><?php echo $row["V_COMPROBANTE"] ? $row["V_COMPROBANTE"] : '-'; ?></td>
											<td><?php echo $ls_mediopago; ?></td>
											<td>S/ <?php echo number_format($row["N_MONTO"], 2); ?></td>
										</tr>
									<?php } ?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="4" style="text-align:right">Total:</th>
										<th>S/ <?php echo number_format($ln_total_general, 2); ?></th>
									</tr>
								</tfoot>
							</table>
						</div>
					<?php } ?>
				</div>
			</div>

		</section>

	</div>

<?php
}

?>
