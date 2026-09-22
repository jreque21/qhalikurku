<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Créditos (Cuerpo - Drcha)
function f_listado_horarios($as_titulo, $as_icono, $as_msgRpta){

	// Enlaces
	$url_lista		= "mov_horarioprog_lista.php";
	$url_detalle	= "mov_horarioclase_lista.php";
	$url_generar	= "../controlador/mov_horarioclase_generar.php";

	// Instanciar clase
	$crud = new crud();

	// Armar Estructura
	$array_campo_pk = ['V_FLAG_ESTADO'];
	$array_valor_pk = ['2']; // Aperturado

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'N_COD_HORARIO', 'D', 0, 999);

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
			if ($as_msgRpta) {
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
												<th>Nº de Clases</th>
												<th>Estado</th>
												<th>Acciones</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$url_generar_fila	= $url_generar."?id_codigo_padre=".($row["N_COD_HORARIO"]);
												$url_detalle_fila	= $url_detalle."?id_codigo_padre=".($row["N_COD_HORARIO"]);
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
													echo "</td>";

													// Categoria Estudiante
													$array_campo_pk	= array('N_COD_CATESTUDIANTE');
													$array_valor_pk	= array($row["N_COD_CATESTUDIANTE"]);
													$ls_catestudiante= $crud->fila_recuperar_campo(DEF_TABLA_CATESTUDIANTE, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_catestudiante;
													echo "</td>";
													
													// # de Clases
													$array_campo_pk	= array('N_COD_HORARIO', 'V_FLAG_ESTADO');
													$array_valor_pk	= array($row["N_COD_HORARIO"], '1');
													$li_clases 	= $crud->fila_contar(DEF_TABLA_HORARIOPROG, $array_campo_pk, $array_valor_pk);
													echo "<td>";
													echo $li_clases;
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
													<a href="<?php echo $url_generar_fila?>">
														<i class="fa fa-cogs" title = "Generar Programación"></i>
													</a>&nbsp;
													<a href="<?php echo $url_detalle_fila?>">
														<i class="fa fa-search" title = "Programación de Clases"></i>  
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

// Lista (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta, $an_id_codigo_padre){

	// Enlaces
	$url_nuevo		= "mov_horarioclase_nuevo.php?id_codigo_padre=".$an_id_codigo_padre;
	$url_editar		= "mov_horarioclase_editar.php?id_codigo_padre=".$an_id_codigo_padre;
	$url_lista		= "mov_horarioprog_lista.php";
	$url_eliminar	= "../controlador/mov_horarioclase_eliminar.php?id_codigo_padre=".$an_id_codigo_padre;

	// Instanciar clase
	$crud = new crud();

	// Armar Estructura
	$array_campo_pk = ['N_COD_HORARIO'];
	$array_valor_pk = [$an_id_codigo_padre];

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_HORARIOPROG, $array_campo_pk, $array_valor_pk, 'D_FEC_PROG', 'A', 0, 1000);

	// Horario
	$array_campo_pk	= array('N_COD_HORARIO');
	$array_valor_pk	= array($an_id_codigo_padre);
	$array_horario 	= $crud->fila_recuperar(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk);
	
	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<!-- Cabecera de Sección Contenido -->
		<section class="content-header">
			<h1>
			<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo.' - Programación';
			?>
			</h1>
			</br>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item active" aria-current="page"><b>Horario : </b><?php echo $array_horario['V_DESCRIPCION'];?></li>
				</ol>
			</nav>
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
												<th>Nº Clase</th>
												<th>Fecha</th>
												<th>Día</th>
												<th>Hora Clase</th>
												<th>Nº Horas</th>
												<th>Instructor</th>												
												<th>Editar</th>
												<th>Eliminar</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$url_editar_fila   = $url_editar."&id_codigo=".($row["N_CLASE"]);
												$url_eliminar_fila = $url_eliminar."&id_codigo=".($row["N_CLASE"]);
												?>
												<tr>
													<?php
													
													echo "<td>";
													echo $row["N_CLASE"];
													echo "</td>";

													echo "<td>";
													echo $row["D_FEC_PROG"];
													echo "</td>";

													echo "<td>";
													echo f_r_diasemana($row["D_FEC_PROG"]);
													echo "</td>";

													echo "<td>";
													echo substr($row["D_HORA_PROG"],0,5);
													echo "</td>";

													echo "<td>";
													echo $row["N_HORAS"];
													echo "</td>";

													// Instructor
													$array_campo_pk	= array('N_COD_INSTRUCTOR');
													$array_valor_pk	= array($row["N_COD_INSTRUCTOR"]);
													$array_instructor	= $crud->fila_recuperar(DEF_TABLA_INSTRUCTOR, $array_campo_pk, $array_valor_pk);
													echo "<td>";
													echo $array_instructor['V_APE_PATERNO'].' '.$array_instructor['V_APE_MATERNO'].' '.$array_instructor['V_NOMBRES'];
													echo "</td>";
													
													echo "<td align='center'>";
													?>
													<a href="<?php echo $url_editar_fila?>">
														<i class="fa fa-edit" title = "Editar registro"></i>
													</a>
													<?php
													echo "</td>";
													
													echo "<td align='center'>";
													?>
													<a href="#deleteModal<?php echo $row["N_CLASE"]; ?>" data-toggle="modal" ><i class="fa fa-trash-o"></i></a>
													<div id="deleteModal<?php echo $row["N_CLASE"]; ?>" class="modal fade">
														<div class="modal-dialog">
															<div class="modal-content">
																<div class="modal-header">
																	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
																	<h4 class="modal-title">Aviso de Confirmación</h4>
																</div>
																<div class="modal-body">
																	<p>¿ Seguro que quieres borrar este elemento ?</p>
																	<p class="text-warning"><small>Si lo borras, nunca podrás recuperarlo.</small></p>
																</div>
																<div class="modal-footer">
																	<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
																	<a class="btn btn-danger" href="<?php echo $url_eliminar_fila?>" role="button">
																		Eliminar
																	</a>
																</div>
															</div>
														</div>
													</div>												
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

					<a class="btn btn-primary" href="<?php echo $url_nuevo?>" role="button">
						<span class="glyphicon glyphicon-plus"></span>&nbsp;Nueva Clase
					</a>
					
					<a class="btn btn-primary" href="<?php echo $url_lista?>" role="button">
						Regresar
					</a>

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

// Formulario de Registro
function f_formulario($as_titulo, $as_icono, $as_msgRpta, $array = "", $an_id_codigo_padre) {

	//Inicalizando variables
	$lb_edit = is_array($array);
	$inhabilitado = "disabled='disabled'";

	// Instanciar clase
	$crud = new crud();

	// Gestionar código de padre
	if($an_id_codigo_padre) {
		$li_id_codigo_padre = $an_id_codigo_padre;
		$array_campo_pk	= array('N_COD_HORARIO');
		$array_valor_pk	= array($li_id_codigo_padre);
		$li_cod_instructor = $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'N_COD_INSTRUCTOR');
		$li_clase		= $crud->fila_recuperar_lastIdPar(DEF_TABLA_HORARIOPROG, $array_campo_pk, $array_valor_pk, 'N_CLASE')+1;
		$ld_fecha_hoy   = date('Y-m-d');
	}else{
		$li_id_codigo_padre = $array["N_COD_HORARIO"];
		$li_clase			= $array["N_CLASE"];
		$li_cod_instructor  = $array["N_COD_INSTRUCTOR"];
		// Enlaces
		$url_eliminar	= "../controlador/mov_horarioclase_eliminar.php?id_codigo_padre=".$li_id_codigo_padre."&id_codigo=".$array['N_CLASE'];
	}

	// Horario
	$array_campo_pk	= array('N_COD_HORARIO');
	$array_valor_pk	= array($li_id_codigo_padre);
	$array_horario 	= $crud->fila_recuperar(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk);
	
	// Enlaces
	$url_lista		= "mov_horarioclase_lista.php?id_codigo_padre=".$li_id_codigo_padre;
	$url_registrar	= "../controlador/mov_horarioclase_registrar.php";
	$url_actualizar	= "../controlador/mov_horarioclase_actualizar.php";

	if($lb_edit) {
		$ls_modo = DEF_MSG_FORM_EDICION;
	}else{
		$ls_modo = DEF_MSG_FORM_NUEVO;
	}

	// Formulario HTML
	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<!-- Cabecera de Sección Contenido -->
		<section class="content-header">
			<h1>
			<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo;
			?>
			</h1>
			</br>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item active" aria-current="page"><b>Horario : </b><?php echo $array_horario['V_DESCRIPCION'];?></li>
				</ol>
			</nav>
			<ol class="breadcrumb">
			<li><a href="<?php echo DEF_URL_LOGIN;?>"><i class="fa fa-home"></i> Inicio</a></li>
				<li class="active"><a href="<?php echo $url_lista?>">Volver a Lista</a></li>
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
							<i class="fa fa-edit"></i>
							MANTENIMIENTO - <?php echo strtoupper($ls_modo);?>
						</div>

						<!-- Cuerpo -->
						<div class="panel-body">

							<form  id="form_mtto" role="form" method="post" action="" enctype="multipart/form-data" autocomplete="off">
								
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_codigo">Código</label>
											<input type="text" class="form-control" id="id_codigo" name="id_codigo" maxlength="10" disabled
												title = "Generado por el sistema" placeholder="Codigo autogenerado" value="<?php echo $lb_edit?$array["N_CLASE"]:$li_clase; ?>"> 
											<input type="hidden" class="form-control" id="id_cod_horario_hide" name="id_cod_horario_hide"  
												 value="<?php echo $li_id_codigo_padre; ?>"> 
											<input type="hidden" class="form-control" id="id_clase_hide" name="id_clase_hide"  
												 value="<?php echo $li_clase; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fec_prog">Fecha Programada</label>
											<input type="date" class="form-control input-sm" id="id_fec_prog" name="id_fec_prog" required"
												title = "Formato Fecha" value="<?php echo $lb_edit?$array["D_FEC_PROG"]:$ld_fecha_hoy; ?>"> 
										</div>	
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_hora_prog">Hora de Inicio</label>
											<input type="time" class="form-control input-sm" id="id_hora_prog" name="id_hora_prog" required"
												title = "Formato Hora" value="<?php echo $lb_edit?$array["D_HORA_PROG"]:$array_horario['D_HORA_INICIO']; ?>"> 
										</div>	
									</div>
								</div>
								
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_horas">Horas por clase</label>
											<input type="number" class="form-control" id="id_horas" name="id_horas" maxlength="3" required pattern="[0-9 ]{1,3}"
												title = "Números. Tamaño máximo: 3" placeholder="(*) Ejemplo : 1" value="<?php echo $lb_edit?$array["N_HORAS"]:$array_horario['N_HORAS']; ?>"> 
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_instructor">Instructor</label>
											<select class="form-control" id="id_cod_instructor" name="id_cod_instructor" required>
												<?php												
												// Recuperando datos para combo
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_tipo     = $crud->fila_listar(DEF_TABLA_INSTRUCTOR, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO, V_APE_MATERNO', 'A', 0, 100);

												// Mostrando combo
												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_tipo = mysqli_fetch_assoc($array_tipo)) {
													echo "<option value=\"";
													echo $this_tipo["N_COD_INSTRUCTOR"];
													echo "\"";
													// Si existen registros, ponerlo en el combo
													if ($this_tipo["N_COD_INSTRUCTOR"] == $li_cod_instructor){
														echo " selected";
													}
													echo ">";
													echo $this_tipo["V_APE_PATERNO"].' '.$this_tipo["V_APE_MATERNO"].' '.$this_tipo["V_NOMBRES"];
													echo "\n";
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-sm-4">
										
									</div>
								</div>
								
								<div class="form-group">
									<label for="id_observacion">Observaciones</label>
									<textarea class="form-control" rows="3" id="id_observacion" name="id_observacion" placeholder="Ejemplo : Programación Manual por feriado"><?php echo $lb_edit?$array["V_OBSERVACION"]:""; ?></textarea>
								</div>

								<?php
								if ($lb_edit) {
									echo "<input type=hidden name=id_codigo value=\"".$array["N_CLASE"]."\">";
									?>
									<input class="btn btn-success" type="submit" id ="btn_actualizar" value="Actualizar" onclick=this.form.action="<?php echo $url_actualizar?>">
									<input class="btn btn-danger" type="submit" id ="btn_eliminar" value="Eliminar" formnovalidate onclick=this.form.action="<?php echo $url_eliminar?>">
									<a class="btn btn-primary" href="<?php echo $url_lista?>" role="button">Cancelar</a>
									<?php
								} else {
									?>
									<input class="btn btn-success" type="submit" id ="btn_agregar" value="Guardar" onclick=this.form.action="<?php echo $url_registrar?>">
									<a class="btn btn-primary" href="<?php echo $url_lista?>" role="button">Cancelar</a>
									<?php
								}
								?>
			
							</form>
							
						</div>
						<!-- Fin Cuerpo -->

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

	<script src="../recursos/js/jquery-1.11.2.min.js"></script>
	
	<?php
}

?>