<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Usuarios (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $an_cod_sede, $an_cod_horario){

	// Enlaces	
	$url_reporte	= "Report/rpt_asistencias_x_horario.php";
	$url_recuperar	= "rpt_asistencia_x_horario.php";

	// Instanciar clase
	$crud = new crud();

	// Listado de Resultados
	$array = $crud->fila_rpt_matxhorario($an_cod_horario);

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
											<label for="id_cod_horario">Horario (Aperturado)</label>
											<select class="form-control" id="id_cod_horario" name="id_cod_horario" required>
												<?php												
												// Recuperando datos para combo
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('2');
												$array_tipo     = $crud->fila_listar(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION', 'A', 0, 100);
												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												// Mostrando combo
												while ($this_tipo = mysqli_fetch_assoc($array_tipo)) {
													echo "<option value=\"";
													echo $this_tipo["N_COD_HORARIO"];
													echo "\"";
													// Si existen registros, ponerlo en el combo
													if ($this_tipo["N_COD_HORARIO"] == $an_cod_horario){
														echo " selected";
													}
													echo ">";
													echo $this_tipo["V_DESCRIPCION"];
													echo "\n";
												}
												?>
											</select>
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
												<th>Estudiante</th>	
												<?php
												// Programación
												$array_campo_pk = ['N_COD_HORARIO', 'V_FLAG_ESTADO'];
												$array_valor_pk = [$an_cod_horario, '1'];
												$arrayProg 		= $crud->fila_listar(DEF_TABLA_HORARIOPROG, $array_campo_pk, $array_valor_pk, 'D_FEC_PROG', 'A', 0, 999);
												if ($arrayProg) {
													while ($row_dia = mysqli_fetch_assoc($arrayProg)) {	
														echo '<th>'.date_format(date_create($row_dia['D_FEC_PROG']), 'd-M').'</th>';	
													}
												}	
												?>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {												
												?>
												<tr>
													<?php

													// Padrón de Estudiante
													$array_campo_pk	= array('N_COD_ESTUDIANTE');
													$array_valor_pk	= array($row["N_COD_ESTUDIANTE"]);
													$row_padron		= $crud->fila_recuperar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk);

													echo "<td>";
													echo $row_padron["V_APE_PATERNO"].' '.$row_padron["V_APE_MATERNO"].' '.$row_padron["V_NOMBRES"];
													echo "</td>";

													// Ejecución
													$array_campo_pk = ['N_COD_HORARIO', 'V_FLAG_ESTADO'];
													$array_valor_pk = [$an_cod_horario, '1'];
													$arrayProg 		= $crud->fila_listar(DEF_TABLA_HORARIOPROG, $array_campo_pk, $array_valor_pk, 'D_FEC_PROG', 'A', 0, 999);
													
													if ($arrayProg) {
														while ($row_dia = mysqli_fetch_assoc($arrayProg)) {	
															$ls_marca = $crud->f_get_asistEstxClase($an_cod_horario, $row_dia['N_CLASE'], $row['N_COD_ESTUDIANTE']);
															echo '<td>';
															echo $ls_marca;
															echo '</td>';	
														}
													}
																									
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