<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de reportes principales disponibles
function f_listado($as_titulo, $as_icono){

	$array_reportes = array(
		array(
			'titulo'      => 'Ingresos por Periodo',
			'descripcion' => 'Total recaudado en un rango de fechas, desglosado por forma de pago (Contado / Crédito) y medio de pago (Efectivo, Tarjeta, etc.).',
			'icono'       => 'fa fa-money',
			'color'       => 'bg-green',
			'url'         => 'rpt_ingresos.php'
		),
		array(
			'titulo'      => 'Citas por Periodo',
			'descripcion' => 'Cantidad de citas en un rango de fechas según su estado: Programadas, Atendidas, Canceladas, No asistió y Reprogramadas. Filtrable por especialista.',
			'icono'       => 'fa fa-calendar-check-o',
			'color'       => 'bg-aqua',
			'url'         => 'rpt_citas.php'
		),
		array(
			'titulo'      => 'Productividad por Especialista',
			'descripcion' => 'Citas atendidas, canceladas e inasistencias por especialista en un rango de fechas, junto con los ingresos que generó cada uno.',
			'icono'       => 'fa fa-user-md',
			'color'       => 'bg-yellow',
			'url'         => 'rpt_productividad.php'
		),
		array(
			'titulo'      => 'Cuentas por Cobrar',
			'descripcion' => 'Comprobantes a crédito con saldo pendiente: monto total, abonado y saldo por cada paciente.',
			'icono'       => 'fa fa-credit-card',
			'color'       => 'bg-red',
			'url'         => 'mov_comprobante_lista.php'
		),
	);

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

			<div class="row">
				<?php foreach ($array_reportes as $rpt) { ?>
				<div class="col-md-6">
					<div class="small-box <?php echo $rpt['color']; ?>" style="min-height:150px;">
						<div class="inner">
							<h4 style="margin-bottom:10px;"><i class="<?php echo $rpt['icono']; ?>"></i> &nbsp;<?php echo $rpt['titulo']; ?></h4>
							<p style="font-size:13px;"><?php echo $rpt['descripcion']; ?></p>
						</div>
						<a href="<?php echo $rpt['url']; ?>" class="small-box-footer">
							Ver reporte <i class="fa fa-arrow-circle-right"></i>
						</a>
					</div>
				</div>
				<?php } ?>
			</div>

		</section>
		<!-- Fin Contenido -->

	</div>
	<!-- Fin sección Contenido -->

<?php
}

?>
