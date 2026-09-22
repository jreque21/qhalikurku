<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Usuarios (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta){

	// Enlaces
	$url_detalle	= "mov_matricula_lista.php";
	$url_lista		= "mov_matriculahorario_lista.php";

	// Instanciar clase
	$crud = new crud();

	// Armar Estructura
	$array_campo_pk = 'V_FLAG_ESTADO';
	$array_valor_pk = ['1','2'];

	// Listado
	$array = $crud->fila_listar_in(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'D_FEC_INICIO', 'D', 0, 999);

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
				echo "<div class='alert alert-danger'>";
					echo DEF_MSG_FORM_AVISO.$as_msgRpta;
				echo "</div>";
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
							<i class="ion ion-clipboard"></i>
							MANTENIMIENTO - <?php echo DEF_MSG_FORM_LISTADO;?>
						</div>

						<!-- Cuerpo -->
						<div class="panel-body">

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
												<th>Horario</th>
												<th>Sede</th>
												<th>Instructor</th>
												<th>Fecha Inicio</th>
												<th>Turno</th>
												<th>Hora</th>
												<th>Categoría</th>
												<th>Nº de Inscritos</th>
												<th>Estado</th>
												<th>Acciones</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$url_detalle_fila   = $url_detalle."?id_codigo_padre=".($row["N_COD_HORARIO"]);
												?>
												<tr>
													<?php
													echo "<td>";
													echo $row["V_DESCRIPCION"];
													echo "</td>";

													// Sede
													$array_campo_pk	= array('N_COD_SEDE');
													$array_valor_pk	= array($row["N_COD_SEDE"]);
													$ls_sede		= $crud->fila_recuperar_campo(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE');
													echo "<td>";
													echo $ls_sede;
													echo "</td>";

													// Instructor
													$array_campo_pk	= array('N_COD_INSTRUCTOR');
													$array_valor_pk	= array($row["N_COD_INSTRUCTOR"]);
													$ls_instructor_ape_p	= $crud->fila_recuperar_campo(DEF_TABLA_INSTRUCTOR, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO');
													$ls_instructor_ape_m	= $crud->fila_recuperar_campo(DEF_TABLA_INSTRUCTOR, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');
													$ls_instructor_nombres	= $crud->fila_recuperar_campo(DEF_TABLA_INSTRUCTOR, $array_campo_pk, $array_valor_pk, 'V_NOMBRES');
													echo "<td>";
													echo $ls_instructor_ape_p.' '.$ls_instructor_ape_m.' '.$ls_instructor_nombres;
													echo "</td>";

													echo "<td>";
													echo $row["D_FEC_INICIO"];
													echo "</td>";

													// Turno
													$array_campo_pk	= array('N_COD_CATURNO');
													$array_valor_pk	= array($row["N_COD_CATURNO"]);
													$ls_turno		= $crud->fila_recuperar_campo(DEF_TABLA_CATTURNO, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_turno;
													echo "</td>";

													echo "<td>";
													echo substr($row["D_HORA_INICIO"],0,5);
													//echo date("H:i");
													echo "</td>";
													
													// Categoria Estudiante
													$array_campo_pk	= array('N_COD_CATESTUDIANTE');
													$array_valor_pk	= array($row["N_COD_CATESTUDIANTE"]);
													$ls_catestudiante= $crud->fila_recuperar_campo(DEF_TABLA_CATESTUDIANTE, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_catestudiante;
													echo "</td>";

													// Instritos
													$array_campo_pk	= array('N_COD_HORARIO', 'V_FLAG_ESTADO');
													$array_valor_pk	= array($row["N_COD_HORARIO"], '1');
													$li_inscritos 	= $crud->fila_contar(DEF_TABLA_MATRICULA, $array_campo_pk, $array_valor_pk);
													echo "<td>";
													echo $li_inscritos;
													echo "</td>";

													echo "<td>";
														$ls_estado =  $row["V_FLAG_ESTADO"];
														if ($ls_estado =='0') $ls_des_estado = 'Cancelado';
														if ($ls_estado =='1') $ls_des_estado = 'Registrado';
														if ($ls_estado =='2') $ls_des_estado = 'Aperturado';
														if ($ls_estado =='3') $ls_des_estado = 'Terminado';
														echo $ls_des_estado;
													echo "</td>";

													echo "<td align='center'>";
													?>
													<a href="<?php echo $url_detalle_fila?>">
														<i class="fa fa-search" title = "Ver Inscritos"></i>
													</a>
													<?php
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