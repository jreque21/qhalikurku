<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Usuarios (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $an_cod_sede){

	// Enlaces	
	$url_recuperar	= "rpt_traslados_pendientes.php";

	// Instanciar clase
	$crud = new crud();

	// Listado de Resultados
	$array = $crud->fila_rpt_trasladospend($an_cod_sede);

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
											<label for="id_cod_sede">Sede Destino</label>
											<select class="form-control" id="id_cod_sede" name="id_cod_sede" required>
												<?php												
												// Recuperando datos para combo
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_tipo     = $crud->fila_listar(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE', 'A', 0, 100);
												// Mostrando combo
												echo "<option value='0' selected disabled hidden>Seleccione opción</option>";
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
								
								</div>
								
								<input class="btn btn-primary" type="submit" id ="btn_recuperar" value="Recuperar" onclick=this.form.action="<?php echo $url_recuperar?>">

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
												<th>N°</th>
												<th>N° Documento</th>
												<th>Estudiante</th>
												<th>Fecha Inicio</th>
												<th>Fecha Fin</th>
												<th>Sede Origen</th>
												<th>Sede Destino</th>
											</tr>
										</thead>

										<tbody>
											<?php
											$li_x = 0;
											while ($row = mysqli_fetch_assoc($array)) {
												$li_x = $li_x + 1;
												?>
												<tr>
													<?php

													echo "<td>";
													echo $li_x;
													echo "</td>";
													
													// Datos Estudiante
													$array_campo_pk	= array('N_COD_ESTUDIANTE');
													$array_valor_pk	= array($row["N_COD_ESTUDIANTE"]);
													$array_estudiante = $crud->fila_recuperar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk);
													echo "<td>";
													echo $array_estudiante['V_NRO_DOC'];
													echo "</td>";
													
													echo "<td>";
													echo $array_estudiante['V_APE_PATERNO'].' '.$array_estudiante['V_APE_MATERNO'].' '.$array_estudiante['V_NOMBRES'];
													echo "</td>";

													echo "<td>";
													echo $row["D_FEC_INICIO"];
													echo "</td>";

													echo "<td>";
													echo $row["D_FEC_FIN"];
													echo "</td>";
													
													// Sede Origen
													$array_campo_pk	= array('N_COD_SEDE');
													$array_valor_pk	= array($row["N_COD_SEDE_ORIGEN"]);
													$ls_sede_origen	= $crud->fila_recuperar_campo(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE');
													echo "<td>";
														echo $ls_sede_origen;
													echo "</td>";
													
													// Sede Destino
													$array_campo_pk	= array('N_COD_SEDE');
													$array_valor_pk	= array($row["N_COD_SEDE_DESTINO"]);
													$ls_sede_destino= $crud->fila_recuperar_campo(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE');
													echo "<td>";
														echo $ls_sede_destino;
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