<?php

// Incluye Librería BD
require_once("../config/funciones.php");

function f_reporte($as_titulo, $as_icono, $as_fec_ini, $as_fec_fin, $as_cod_especialista){

	$crud = new crud();
	$bd   = new baseDatos();

	$ls_fec_ini = $bd->bd_escapeCadena($as_fec_ini);
	$ls_fec_fin = $bd->bd_escapeCadena($as_fec_fin);

	$array_estados = array(
		'PRO' => array('Programada',   'label-default'),
		'CON' => array('Confirmada',   'label-info'),
		'ATE' => array('Atendida',     'label-success'),
		'REP' => array('Reprogramada', 'label-warning'),
		'CAN' => array('Cancelada',    'label-danger'),
		'NOA' => array('No asistió',   'label-danger'),
	);

	// Armar condición
	$ls_cond = "D_FEC_CITA BETWEEN '$ls_fec_ini' AND '$ls_fec_fin' AND V_FLAG_ESTADO = '1'";
	if (!empty($as_cod_especialista)) {
		$ls_cond .= " AND N_COD_ESPECIALISTA = '" . $bd->bd_escapeCadena($as_cod_especialista) . "'";
	}

	$array_citas = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . " WHERE $ls_cond", 'D_FEC_CITA', 'A', 0, 3000);

	$array_x_estado = array('PRO'=>0, 'CON'=>0, 'ATE'=>0, 'REP'=>0, 'CAN'=>0, 'NOA'=>0);
	$array_detalle  = array();
	$li_total = 0;

	if ($array_citas) {
		while ($row = mysqli_fetch_assoc($array_citas)) {
			$li_total++;
			if (isset($array_x_estado[$row['V_ESTADO_CITA']])) {
				$array_x_estado[$row['V_ESTADO_CITA']]++;
			}
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
						<div class="form-group">
							<label>Especialista</label>
							<select class="form-control" name="id_cod_especialista">
								<option value="">Todos</option>
								<?php
								$array_campo_pk	= array('V_FLAG_ESTADO');
								$array_valor_pk	= array('1');
								$array_esp = $crud->fila_listar(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO, V_APE_MATERNO', 'A', 0, 999);
								while ($this_esp = mysqli_fetch_assoc($array_esp)) {
									echo "<option value=\"".$this_esp["N_COD_ESPECIALISTA"]."\"";
									if ($as_cod_especialista == $this_esp["N_COD_ESPECIALISTA"]) echo " selected";
									echo ">".$this_esp["V_APE_PATERNO"].' '.$this_esp["V_APE_MATERNO"]."</option>\n";
								}
								?>
							</select>
						</div>
						&nbsp;
						<button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Consultar</button>
						<button type="button" class="btn btn-default" onclick="window.print()"><i class="fa fa-print"></i> Imprimir</button>
						<a class="btn btn-success" href="../controlador/rpt_citas_export.php?formato=csv&id_fec_ini=<?php echo $as_fec_ini; ?>&id_fec_fin=<?php echo $as_fec_fin; ?>&id_cod_especialista=<?php echo $as_cod_especialista; ?>">
							<i class="fa fa-file-excel-o"></i> Excel
						</a>
						<a class="btn btn-danger" href="../controlador/rpt_citas_export.php?formato=pdf&id_fec_ini=<?php echo $as_fec_ini; ?>&id_fec_fin=<?php echo $as_fec_fin; ?>&id_cod_especialista=<?php echo $as_cod_especialista; ?>" target="_blank">
							<i class="fa fa-file-pdf-o"></i> PDF
						</a>
					</form>
				</div>
			</div>

			<!-- Resumen por estado -->
			<div class="row">
				<div class="col-md-2 col-xs-4">
					<div class="small-box bg-aqua">
						<div class="inner"><h3><?php echo $li_total; ?></h3><p>Total</p></div>
					</div>
				</div>
				<?php foreach ($array_estados as $cod => $info) { ?>
				<div class="col-md-2 col-xs-4">
					<div class="small-box" style="background:#f4f4f4;">
						<div class="inner">
							<h3><?php echo $array_x_estado[$cod]; ?></h3>
							<p><span class="label <?php echo $info[1]; ?>"><?php echo $info[0]; ?></span></p>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>

			<!-- Detalle -->
			<div class="panel panel-primary">
				<div class="box-header"><i class="fa fa-list"></i> Detalle de Citas</div>
				<div class="panel-body">
					<?php if (empty($array_detalle)) { ?>
						<div class="alert alert-warning">No se encontraron citas en el periodo seleccionado.</div>
					<?php } else { ?>
						<div class="table-responsive">
							<table class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>Fecha</th>
										<th>Hora</th>
										<th>Paciente</th>
										<th>Especialista</th>
										<th>Estado</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($array_detalle as $row) {

										$array_campo_pk	= array('N_COD_PACIENTE');
										$array_valor_pk	= array($row["N_COD_PACIENTE"]);
										$ls_pac = $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO') . ' ' .
												  $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');

										$array_campo_pk	= array('N_COD_ESPECIALISTA');
										$array_valor_pk	= array($row["N_COD_ESPECIALISTA"]);
										$ls_esp = $crud->fila_recuperar_campo(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO') . ' ' .
												  $crud->fila_recuperar_campo(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');

										$ls_cod_estado = $row["V_ESTADO_CITA"];
										$ls_des_estado = isset($array_estados[$ls_cod_estado]) ? $array_estados[$ls_cod_estado][0] : $ls_cod_estado;
										$ls_css_estado = isset($array_estados[$ls_cod_estado]) ? $array_estados[$ls_cod_estado][1] : 'label-default';
										?>
										<tr>
											<td><?php echo date('d/m/Y', strtotime($row["D_FEC_CITA"])); ?></td>
											<td><?php echo substr($row["D_HORA_INICIO"],0,5); ?></td>
											<td><?php echo $ls_pac; ?></td>
											<td><?php echo $ls_esp; ?></td>
											<td><span class="label <?php echo $ls_css_estado; ?>"><?php echo $ls_des_estado; ?></span></td>
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
