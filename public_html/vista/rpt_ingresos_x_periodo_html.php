<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Login de Permiso
function f_form_permiso($as_titulo, $as_icono){

	// Enlaces	
	$url_recuperar	= "rpt_ingresos_x_periodo.php";
	$url_validar	= "../controlador/mae_validar_seguridad.php";
	$url_cancelar	= "panel_admin.php";

	// Instanciar clase
	$crud = new crud();

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

			<!-- Fila Principal -->
			<div class="row">

				<!-- Columna Izquierda -->
				<section class="col-lg-12 connectedSortable">

					<!-- Panel -->
					<div class="panel panel-primary">

						<!-- Cabecera -->
						<div class="box-header">
							<i class="ion ion-clipboard"></i>
							<?php echo 'PERMISO DE ACCESO';?>
						</div>

						<!-- Cuerpo -->
						<div class="panel-body">

							<!-- Filtro -->
							<form action="" id="form_mtto" method="post" class="d-flex align-self-center">
								
								<div class="row">
									
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_clave">Clave de Seguridad</label>
											<input name="id_clave" id="id_clave" type="password" class="form-control" placeholder="Ingrese su clave de seguridad" maxlength="40" required>
										</div>
									</div>

								</div>

								<?php
								echo "<input type=hidden name=id_url value=\"".$url_recuperar."\">";
								?>
								
								<input class="btn btn-primary" type="submit" id ="btn_recuperar" value="Aceptar" onclick=this.form.action="<?php echo $url_validar?>">
								<input class="btn btn-primary" type="submit" id ="btn_cancelar"  value="Cancelar" formnovalidate onclick=this.form.action="<?php echo $url_cancelar?>">

            				</form>

							<!-- Resultados -->
							<?php
							
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

// Lista de Datos (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $an_cod_sede, $adt_fini, $adt_ffin){

	// Enlaces	
	$url_reporte	= "Report/rpt_ingresos_x_periodo.php";
	$url_recuperar	= "rpt_ingresos_x_periodo.php";

	// Instanciar clase
	$crud = new crud();

	// Listado de Resultados
	$array = $crud->fila_rpt_ingresosxperiodo($an_cod_sede, $adt_fini, $adt_ffin);

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

			<!-- Fila Principal -->
			<div class="row">

				<!-- Columna Izquierda -->
				<section class="col-lg-12 connectedSortable">

					<!-- Panel -->
					<div class="panel panel-primary">

						<!-- Cabecera -->
						<div class="box-header">
							<i class="ion ion-clipboard"></i>
							<?php echo DEF_MSG_FORM_REPORTE;?>
						</div>

						<!-- Cuerpo -->
						<div class="panel-body">

							<!-- Filtro -->
							<form action="" id="form_mtto" method="post" class="d-flex align-self-center">
								
								<div class="row">
									
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_sede">Sede</label>
											<select class="form-control" id="id_cod_sede" name="id_cod_sede" required>
												<?php												
												// Recuperando datos para combo
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_tipo     = $crud->fila_listar(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE', 'A', 0, 100);
												// Mostrando combo
												while ($this_tipo = mysqli_fetch_assoc($array_tipo)) {
													echo "<option value=\"";
													echo $this_tipo["N_COD_SEDE"];
													echo "\"";
													// Si existen registros, ponerlo en el combo
													if ($this_tipo["N_COD_SEDE"] == $an_cod_sede){
														echo " selected";
													}
													echo ">";
													echo $this_tipo["V_NOMBRE"];
													echo "\n";
												}
												?>
											</select>
										</div>
									</div>

									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fini">Fecha de Inicio</label>
											<input type="date" class="form-control input-sm" id="id_fini" name="id_fini" "
												title = "Formato Fecha" value="<?php echo $adt_fini; ?>"> 
										</div>
									</div>

									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_ffin">Fecha de Término</label>
											<input type="date" class="form-control input-sm" id="id_ffin" name="id_ffin" "
												title = "Formato Fecha" value="<?php echo $adt_ffin; ?>"> 
										</div>
									</div>
								
								</div>
								
								<input class="btn btn-primary" type="submit" id ="btn_recuperar" value="Recuperar" onclick=this.form.action="<?php echo $url_recuperar?>">
								<input class="btn btn-primary" type="submit" id ="btn_exportar" value="Exportar" formtarget = "_blank" onclick=this.form.action="<?php echo $url_reporte?>">

            				</form>
            				
							<!-- Separador -->
							<hr id="separator-filter">

							<!-- Resultados -->
							<?php
							if (!($array)) {
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
												<th>Pago en Efectivo</th>									
												<th>Pago Transferencia</th>	
												
												<th>Pago con Tarjeta</th>
												
												<th>Pago Móvil (Yape/Plin)</th>
												
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {												
												?>
												<tr>
													<?php													

													echo "<td>";
													echo $row["FECHA"];
													echo "</td>";
											
													echo "<td>";
													echo $row["MONTO_EFECTIVO"];
													echo "</td>";
													
													echo "<td>";
													echo $row["MONTO_TRANSFERENCIA"];
													echo "</td>";
													
													echo "<td>";
													echo $row["MONTO_TARJETA"];
													echo "</td>";
													
													echo "<td>";
													echo $row["MONTO_PAGOMOVIL"];
													echo "</td>";

												echo "</tr>";
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

?>