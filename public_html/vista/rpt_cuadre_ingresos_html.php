<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Usuarios (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $an_cod_sede, $adt_fecha){

	// Enlaces	
	$url_recuperar	= "rpt_cuadre_ingresos.php";

	// Instanciar clase
	$crud = new crud();

	// Listado de Resultados
	$array = $crud->fila_rpt_cuadreingresos($an_cod_sede, $adt_fecha);

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
									
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fecha">Fecha</label>
											<input type="date" class="form-control input-sm" id="id_fecha" name="id_fecha"
												title = "Formato Fecha" value="<?php echo $adt_fecha; ?>"> 
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
												<th>Fecha</th>
												<th>Tipo</th>
												<th>Cód.Mátricula</th>
												<th>Fecha Matrícula</th>
												<th>Cód. Horario</th>
												<th>Horario</th>
												<th>Hora</th>
												<th>Cód. Estudiante</th>
												<th>Estudiante</th>
												<th>Monto Tarifa</th>
												<th>Monto Dscto.</th>
												<th>Monto Pagado</th>
												<th>Forma Pago</th>
												<th>Medio Pago</th>
												<th>Usuario</th>
												<th>Fecha Reg.</th>
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
													echo $row["D_FECHA"];
													echo "</td>";

													echo "<td>";
													echo $row["V_TIPO"];
													echo "</td>";
													
													echo "<td>";
													echo $row["N_COD_MATRICULA"];
													echo "</td>";
													
													echo "<td>";
													echo $row["D_FEC_MATRICULA"];
													echo "</td>";
													
													echo "<td>";
													echo $row["N_COD_HORARIO"];
													echo "</td>";
													
													echo "<td>";
													echo $row["HORARIO"];
													echo "</td>";
													
													echo "<td>";
													echo SUBSTR($row["HORA"],0,5);
													echo "</td>";
													
													echo "<td>";
													echo $row["V_NRO_DOC"];
													echo "</td>";
													
													echo "<td>";
													echo $row["DATOS"];
													echo "</td>";
													
													echo "<td>";
													echo $row["N_MONTO_TARIFA"];
													echo "</td>";
													
													echo "<td>";
													echo $row["N_MONTO_DSCTO"];
													echo "</td>";
													
													echo "<td>";
													echo $row["N_MONTO_PAGADO"];
													echo "</td>";
														  
													echo "<td>";
													echo $row["DES_FORMAPAGO"];
													echo "</td>";
													
													echo "<td>";
													echo $row["DES_MEDIOPAGO"];
													echo "</td>";
													
													echo "<td>";
													echo $row["V_AUD_USR_REG"];
													echo "</td>";
													
													echo "<td>";
													echo $row["D_AUD_FEC_REG"];
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