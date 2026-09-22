<?php

// Incluye Librería BD
require_once("../config/funciones.php");

function f_reporte($as_titulo, $as_icono, $as_fec_ini, $as_fec_fin){

	$crud = new crud();
	$bd   = new baseDatos();

	$ls_fec_ini = $bd->bd_escapeCadena($as_fec_ini);
	$ls_fec_fin = $bd->bd_escapeCadena($as_fec_fin);

	// Todos los especialistas activos
	$array_campo_pk	= array('V_FLAG_ESTADO');
	$array_valor_pk	= array('1');
	$array_esp = $crud->fila_listar(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO, V_APE_MATERNO', 'A', 0, 999);

	$array_filas = array();

	if ($array_esp) {
		while ($this_esp = mysqli_fetch_assoc($array_esp)) {

			$li_cod_esp = $this_esp['N_COD_ESPECIALISTA'];

			$ls_cond_base = "N_COD_ESPECIALISTA = '$li_cod_esp' AND D_FEC_CITA BETWEEN '$ls_fec_ini' AND '$ls_fec_fin' AND V_FLAG_ESTADO = '1'";

			$lr = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . " WHERE $ls_cond_base AND V_ESTADO_CITA = 'ATE'", '', '', -1, 0);
			$li_atendidas = $lr ? $lr->num_rows : 0;

			$lr = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . " WHERE $ls_cond_base AND V_ESTADO_CITA = 'CAN'", '', '', -1, 0);
			$li_canceladas = $lr ? $lr->num_rows : 0;

			$lr = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . " WHERE $ls_cond_base AND V_ESTADO_CITA = 'NOA'", '', '', -1, 0);
			$li_noasistio = $lr ? $lr->num_rows : 0;

			$lr = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . " WHERE $ls_cond_base", '', '', -1, 0);
			$li_total = $lr ? $lr->num_rows : 0;

			// Si no tuvo ninguna cita en el periodo, no se muestra en la tabla
			if ($li_total == 0) {
				continue;
			}

			// Ingresos generados: pagos de citas de este especialista, pagados dentro del periodo
			$ls_cond_ingresos = "p.V_FLAG_ESTADO = '1' AND p.D_FEC_PAGO BETWEEN '$ls_fec_ini' AND '$ls_fec_fin'
									AND EXISTS (SELECT 1 FROM " . DEF_TABLA_CITA . " c WHERE c.N_COD_CITA = p.N_COD_CITA AND c.N_COD_ESPECIALISTA = '$li_cod_esp')";
			$lr = $crud->fila_listar_solocondicion(
					"(SELECT COALESCE(SUM(p.N_MONTO),0) AS TOTAL FROM " . DEF_TABLA_PAGO . " p WHERE $ls_cond_ingresos) t",
					'', '', -1, 0
				  );
			$row_tmp = $lr ? mysqli_fetch_assoc($lr) : null;
			$ln_ingresos = $row_tmp ? floatval($row_tmp['TOTAL']) : 0;

			$array_filas[] = array(
				'nombre'      => $this_esp['V_APE_PATERNO'] . ' ' . $this_esp['V_APE_MATERNO'],
				'total'       => $li_total,
				'atendidas'   => $li_atendidas,
				'canceladas'  => $li_canceladas,
				'noasistio'   => $li_noasistio,
				'ingresos'    => $ln_ingresos,
			);
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
						<a class="btn btn-success" href="../controlador/rpt_productividad_export.php?formato=csv&id_fec_ini=<?php echo $as_fec_ini; ?>&id_fec_fin=<?php echo $as_fec_fin; ?>">
							<i class="fa fa-file-excel-o"></i> Excel
						</a>
						<a class="btn btn-danger" href="../controlador/rpt_productividad_export.php?formato=pdf&id_fec_ini=<?php echo $as_fec_ini; ?>&id_fec_fin=<?php echo $as_fec_fin; ?>" target="_blank">
							<i class="fa fa-file-pdf-o"></i> PDF
						</a>
					</form>
				</div>
			</div>

			<div class="panel panel-primary">
				<div class="box-header"><i class="fa fa-user-md"></i> Productividad por Especialista</div>
				<div class="panel-body">
					<?php if (empty($array_filas)) { ?>
						<div class="alert alert-warning">No se encontraron citas en el periodo seleccionado.</div>
					<?php } else { ?>
						<div class="table-responsive">
							<table class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>Especialista</th>
										<th>Total Citas</th>
										<th>Atendidas</th>
										<th>Canceladas</th>
										<th>No Asistió</th>
										<th>% Asistencia</th>
										<th>Ingresos Generados</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($array_filas as $fila) {
										$ln_pct_asistencia = $fila['total'] > 0 ? round(($fila['atendidas'] / $fila['total']) * 100, 1) : 0;
										?>
										<tr>
											<td><?php echo $fila['nombre']; ?></td>
											<td><?php echo $fila['total']; ?></td>
											<td><span class="label label-success"><?php echo $fila['atendidas']; ?></span></td>
											<td><span class="label label-danger"><?php echo $fila['canceladas']; ?></span></td>
											<td><span class="label label-danger"><?php echo $fila['noasistio']; ?></span></td>
											<td><?php echo $ln_pct_asistencia; ?>%</td>
											<td>S/ <?php echo number_format($fila['ingresos'], 2); ?></td>
										</tr>
									<?php } ?>
								</tbody>
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
