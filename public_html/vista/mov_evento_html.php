<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Usuarios (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta){

	// Enlaces
	$url_nuevo		= "mov_evento_nuevo.php";
	$url_editar		= "mov_evento_editar.php";
	$url_lista		= "mov_evento_lista.php";
	$url_prog		= "mov_eventoprog_editar.php";
	$url_rpt_invitados	= "Report/rpt_evento_invitados.php";
	$url_rpt_asistentes	= "Report/rpt_evento_asistentes.php";
	$url_eliminar	= "../controlador/mov_evento_eliminar.php";

	// Instanciar clase
	$crud = new crud();

	// Armar Estructura
	$array_campo_pk = [];
	$array_valor_pk = [];

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_EVENTO, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION', 'A', 0, 100);

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
												<th>Nombre de Evento</th>
												<th>Lugar</th>
												<th>Fecha</th>
												<th>Hora</th>
												<th>Nº Invitados</th>
												<th>Estado</th>
												<th>Acciones</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$url_editar_fila   	 = $url_editar."?id_codigo=".($row["N_COD_EVENTO"]);
												$url_eliminar_fila   = $url_eliminar."?id_codigo=".($row["N_COD_EVENTO"]);
												$url_prog_fila		 = $url_prog."?id_codigo=".($row["N_COD_EVENTO"]);
												$url_rpt_invitados_fila  = $url_rpt_invitados."?id_codigo=".($row["N_COD_EVENTO"]);
												$url_rpt_asistentes_fila = $url_rpt_asistentes."?id_codigo=".($row["N_COD_EVENTO"]);
												?>
												<tr>
													<?php

													echo "<td>";
													echo f_r_url($url_editar_fila, $row["V_DESCRIPCION"],$row["N_COD_EVENTO"]);
													echo "</td>";

													echo "<td>";
													echo $row["V_LUGAR"];
													echo "</td>";

													echo "<td>";
													echo $row["D_FECHA"];
													echo "</td>";

													echo "<td>";
													echo substr($row["D_HORA"],0,5);
													echo "</td>";

													echo "<td>";
														// Cantidad 
														$array_campo_pk	= array('N_COD_EVENTO', 'V_FLAG_PROG');
														$array_valor_pk	= array($row["N_COD_EVENTO"], '1');
														$li_cantidad	= $crud->fila_contar(DEF_TABLA_EVENTOPROG, $array_campo_pk, $array_valor_pk);
														echo $li_cantidad;
													echo "</td>";

													echo "<td>";
														$ls_estado =  $row["V_FLAG_ESTADO"];
														if ($ls_estado =='0') $estado = 'Inactivo';
														if ($ls_estado =='1') $estado = 'Activo';
														echo $estado;
													echo "</td>";

													echo "<td align='center'>";
													?>
													<a href="<?php echo $url_editar_fila?>">
														<i class="fa fa-edit" title = "Editar registro"></i>
													</a>&nbsp;
													<a href="#deleteModal<?php echo $row["N_COD_EVENTO"]; ?>" data-toggle="modal" ><i class="fa fa-trash-o" title = "Eliminar registro"></i></a>&nbsp;&nbsp;
													<a href="<?php echo $url_prog_fila?>">
														<i class="fa fa-calendar-check-o" title = "Programación de Invitados"></i>
													</a>&nbsp;
													<a href="<?php echo $url_rpt_invitados_fila?>" target="_blank">
														<i class="fa fa-file-pdf-o" title = "Ver Reporte de Invitados"></i>
													</a>&nbsp;
													<a href="<?php echo $url_rpt_asistentes_fila?>" target="_blank">
														<i class="fa fa-file-pdf-o" title = "Ver Reporte de Asistentes"></i>
													</a>
													<?php
													echo "</td>";

													?>
													<div id="deleteModal<?php echo $row["N_COD_EVENTO"]; ?>" class="modal fade">
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
						<span class="glyphicon glyphicon-plus"></span>&nbsp;Nuevo Registro
					</a>

					</br>

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
function f_formulario($as_titulo, $as_icono, $as_msgRpta, $array = "") {

	//Inicalizando variables
	$lb_edit = is_array($array);
	$inhabilitado = "disabled='disabled'";

	// Enlaces
	$url_lista		= "mov_evento_lista.php";
	$url_registrar	= "../controlador/mov_evento_registrar.php";
	$url_actualizar	= "../controlador/mov_evento_actualizar.php";
	$url_eliminar	= "../controlador/mov_evento_eliminar.php";

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
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo;?>
			</h1>
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
											<input type="text" class="form-control" id="id_codigo" name="id_codigo" maxlength="5" disabled
												title = "Generado por el sistema" placeholder="Codigo autogenerado" value="<?php echo $lb_edit?$array["N_COD_EVENTO"]:""; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fecha">Fecha de Evento</label>
											<input type="date" class="form-control input-sm" id="id_fecha" name="id_fecha" required" autofocus
												title = "Formato Fecha" value="<?php echo $lb_edit?$array["D_FECHA"]:""; ?>"> 
										</div>	
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_hora">Hora de Evento</label>
											<input type="time" class="form-control input-sm" id="id_hora" name="id_hora" required"
												title = "Formato Hora" value="<?php echo $lb_edit?$array["D_HORA"]:""; ?>"> 
										</div>	
									</div>
								</div>

								<div class="form-group">
									<label for="id_descripcion">Descripción del Evento</label>
									<input type="text" class="form-control" id="id_descripcion" name="id_descripcion" maxlength="150" required pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s0-9 ]{1,150}" 
										title = "Letras y Números. Tamaño máximo: 150" placeholder="(*) Ejemplo : Concurso Lima 2024" value="<?php echo $lb_edit?$array["V_DESCRIPCION"]:""; ?>">
								</div>

								<div class="row">
									<div class="col-sm-8">
										<div class="form-group">
											<label for="id_lugar">Lugar</label>
											<input type="text" class="form-control" id="id_lugar" name="id_lugar" maxlength="150" required pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s0-9 ]{1,150}"
												title = "Letras y Números. Tamaño máximo: 150" placeholder="(*) Ejemplo : Los Olivos, Lima" value="<?php echo $lb_edit?$array["V_LUGAR"]:""; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_referencia">Referencia</label>
											<input type="text" class="form-control" id="id_referencia" name="id_referencia" maxlength="100" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s0-9 ]{1,150}"
												title = "Letras y Números. Tamaño máximo: 150" placeholder="Ejemplo : Frente a Municipalidad de los Olivos" value="<?php echo $lb_edit?$array["V_REFERENCIA"]:""; ?>">
										</div>
									</div>
								</div>

								<div class="form-group">
									<label for="id_observacion">Observaciones</label>
									<textarea class="form-control" rows="3" id="id_observacion" name="id_observacion" placeholder="Ejemplo : Llevar protectores"><?php echo $lb_edit?$array["V_OBSERVACION"]:""; ?></textarea>
								</div>	
								
								<div class="checkbox">
									<?php
									if($lb_edit) {
										$ls_estado = $array['V_FLAG_ESTADO'];
									}else{
										$ls_estado = '1';
									}
									?>
									<label>
									<input type="checkbox" id="id_flag_estado" name="id_flag_estado" <?php if("0" <> $ls_estado) echo "checked";?> value="1"> Activo ?
									</label>
								</div>

								<?php
								if ($lb_edit) {
									echo "<input type=hidden name=id_codigo value=\"".$array["N_COD_EVENTO"]."\">";
									?>
									<input class="btn btn-success" type="submit" id ="btn_actualizar" value="Actualizar" onclick=this.form.action="<?php echo $url_actualizar?>">
									<input class="btn btn-danger" type="submit" id ="btn_eliminar" value="Eliminar" formnovalidate onclick=this.form.action="<?php echo $url_eliminar?>">
									<input class="btn btn-primary" type="submit" id ="btn_cancelar"  value="Cancelar" formnovalidate onclick=this.form.action="<?php echo $url_lista?>">
									<?php
								} else {
									?>
									<input class="btn btn-success" type="submit" id ="btn_agregar" value="Guardar" onclick=this.form.action="<?php echo $url_registrar?>">
									<input class="btn btn-primary" type="submit" id ="btn_cancelar"  value="Cancelar" formnovalidate onclick=this.form.action="<?php echo $url_lista?>">
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

// Lista de Invitados (Cuerpo - Drcha)
function f_listado_invitados($as_titulo, $as_icono, $as_msgRpta, $id_codigo_padre){

	// Instanciar clase
	$crud = new crud();

	// Enlaces
	$url_cancelar = "mov_evento_lista.php";

	// Armar Estructura
	$array_campo_pk = ['N_COD_EVENTO', 'V_FLAG_PROG'];
	$array_valor_pk = [$id_codigo_padre, '1'];

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_EVENTOPROG, $array_campo_pk, $array_valor_pk, 'N_COD_ESTUDIANTE', 'A', 0, 100);

	// Datos del Padre
	$array_campo_pk = ['N_COD_EVENTO'];
	$array_valor_pk = [$id_codigo_padre];
	$ls_subtitulo	= $crud->fila_recuperar_campo(DEF_TABLA_EVENTO, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');

	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<!-- Cabecera de Sección Contenido -->
		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo.' - Invitados';?>
			</h1>
			</br>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item active" aria-current="page"><b>Evento : </b><?php echo $ls_subtitulo;?></li>
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
												<th>Nº</th>
												<th>Apellidos y Nombres</th>
												<th>Edad</th>
												<th>Género</th>
												<th>Cinturón</th>
												<th>Móvil</th>
											</tr>
										</thead>

										<tbody>
											<?php
											$li_cuenta = 0;
											while ($row = mysqli_fetch_assoc($array)) {
												$li_cuenta = $li_cuenta + 1;
												// Cinturon
												$array_campo_pk	= array('N_COD_ESTUDIANTE');
												$array_valor_pk	= array($row["N_COD_ESTUDIANTE"]);
												$row_padron		= $crud->fila_recuperar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk);
												?>
												<tr>
													<?php

													echo "<td>";
													echo $li_cuenta;
													echo "</td>";

													echo "<td>";
													echo $row_padron["V_APE_PATERNO"].' '.$row_padron["V_APE_MATERNO"].' '.$row_padron["V_NOMBRES"];
													echo "</td>";

													echo "<td>";
													echo f_get_edad($row_padron["D_FEC_NACIMIENTO"]);
													echo "</td>";

													echo "<td>";
														$ls_fg_sexo =  $row_padron["V_FG_SEXO"];
														if ($ls_fg_sexo =='M') $ls_des_sexo = 'Masculino';
														if ($ls_fg_sexo =='F') $ls_des_sexo = 'Femenino';
														echo $ls_des_sexo;
													echo "</td>";

													// Cinturon
													$array_campo_pk	= array('N_COD_CINTURON');
													$array_valor_pk	= array($row_padron["N_COD_CINTURON_ING"]);
													$ls_cinturon	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_cinturon;
													echo "</td>";

													echo "<td>";
													echo $row_padron["V_MOVIL"];
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
					
					<a class="btn btn-primary" href="<?php echo $url_cancelar?>" role="button">
						Regresar
					</a>

					</br>

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

// Lista de Asistentes (Cuerpo - Drcha)
function f_listado_asistentes($as_titulo, $as_icono, $as_msgRpta, $id_codigo_padre){

	// Instanciar clase
	$crud = new crud();

	// Enlaces
	$url_cancelar = "mov_evento_lista.php";

	// Armar Estructura
	$array_campo_pk = ['N_COD_EVENTO', 'V_FLAG_EJEC'];
	$array_valor_pk = [$id_codigo_padre, '1'];

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_EVENTOPROG, $array_campo_pk, $array_valor_pk, 'N_COD_ESTUDIANTE', 'A', 0, 100);

	// Datos del Padre
	$array_campo_pk = ['N_COD_EVENTO'];
	$array_valor_pk = [$id_codigo_padre];
	$ls_subtitulo	= $crud->fila_recuperar_campo(DEF_TABLA_EVENTO, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');

	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<!-- Cabecera de Sección Contenido -->
		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo.' - Asistentes';?>
			</h1>
			</br>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item active" aria-current="page"><b>Evento : </b><?php echo $ls_subtitulo;?></li>
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
												<th>Nº</th>
												<th>Nombre Estudiante</th>
												<th>Edad</th>
												<th>Género</th>
												<th>Cinturón (Grado)</th>
												<th>Móvil</th>
											</tr>
										</thead>

										<tbody>
											<?php
											$li_cuenta = 0;
											while ($row = mysqli_fetch_assoc($array)) {
												$li_cuenta = $li_cuenta + 1;
												// Padron Estudiante
												$array_campo_pk	= array('N_COD_ESTUDIANTE');
												$array_valor_pk	= array($row["N_COD_ESTUDIANTE"]);
												$row_padron		= $crud->fila_recuperar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk);
												?>
												<tr>
													<?php

													echo "<td>";
													echo $li_cuenta;
													echo "</td>";

													echo "<td>";
													echo $row_padron["V_APE_PATERNO"].' '.$row_padron["V_APE_MATERNO"].' '.$row_padron["V_NOMBRES"];
													echo "</td>";

													echo "<td>";
													echo f_get_edad($row_padron["D_FEC_NACIMIENTO"]);
													echo "</td>";

													echo "<td>";
														$ls_fg_sexo =  $row_padron["V_FG_SEXO"];
														if ($ls_fg_sexo =='M') $ls_des_sexo = 'Masculino';
														if ($ls_fg_sexo =='F') $ls_des_sexo = 'Femenino';
														echo $ls_des_sexo;
													echo "</td>";

													// Cinturon
													$array_campo_pk	= array('N_COD_CINTURON');
													$array_valor_pk	= array($row_padron["N_COD_CINTURON_ING"]);
													$ls_cinturon	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_cinturon;
													echo "</td>";

													echo "<td>";
													echo $row_padron["V_MOVIL"];
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
					
					<a class="btn btn-primary" href="<?php echo $url_cancelar?>" role="button">
						Regresar
					</a>

					</br>

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

// Formulario de Programación de Invitados
function f_formulario_prog($as_titulo, $as_icono, $as_msgRpta, $id_codigo_padre = "") {

	//Inicalizando variables
	$inhabilitado = "disabled='disabled'";

	// Instanciar clase
	$crud = new crud();

	// Definir Estructura
	$array_campo_pk = array('N_COD_EVENTO');
	$array_valor_pk = array($id_codigo_padre);

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_EVENTOPROG, $array_campo_pk, $array_valor_pk, 'N_COD_ESTUDIANTE', 'A', 0, 1000);

	// Datos Padre
	$ls_subtitulo	= $crud->fila_recuperar_campo(DEF_TABLA_EVENTO, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');

	// Enlaces
	$url_lista		= "mov_evento_lista.php";
	$ls_modo 		= DEF_MSG_FORM_SELECCION;

	// Formulario HTML
	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<!-- Cabecera de Sección Contenido -->
		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo.' - Programar Invitados';?>
			</h1>
			</br>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item active" aria-current="page"><b>Evento : </b><?php echo $ls_subtitulo;?></li>
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

							<?php
							if (!($array)) {
								?>
								<div class="alert alert-warning">
									<?php echo DEF_MSG_SIN_REGISTROS;?>
								</div>
								<?php
							}else {
								?>
								<form  id="form_mtto" role="form" method="post" action="" autocomplete="off">

								<div class="table-responsive">

									<table id="lista" class="table table-striped table-bordered">

										<thead>
											<tr>
												<th>Marca (S/N)</th>
												<th>Estudiante</th>
												<th>Edad</th>
												<th>Género</th>
												<th>Cinturón (Grado)</th>
												<th>Móvil</th>
											</tr>
										</thead>

										<tbody id='detalle'>
											<?php
											$li_check = 0;
											while ($row = mysqli_fetch_assoc($array)) {
												// Padron Estudiante
												$array_campo_pk	= array('N_COD_ESTUDIANTE');
												$array_valor_pk	= array($row["N_COD_ESTUDIANTE"]);
												$row_padron		= $crud->fila_recuperar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk);
												?>
												<tr>
													
													<td>
														<div class="checkbox">
															<label>
															<input type="checkbox" data-idRegistro = "<?php echo $row["N_COD_ESTUDIANTE"];?>" name="id_flag_prog" <?php if("0" <> $row["V_FLAG_PROG"]) echo "checked";?> value="<?php echo $li_check; ?>">
															</label>
														</div>
													</td>

													<?php
													echo "<input type=hidden id = id_codigo_padre name=id_codigo_padre value=\"".$id_codigo_padre."\">";
													
													echo "<td>";
													echo $row_padron["V_APE_PATERNO"].' '.$row_padron["V_APE_MATERNO"].' '.$row_padron["V_NOMBRES"];
													echo "</td>";

													echo "<td>";
													echo f_get_edad($row_padron["D_FEC_NACIMIENTO"]);
													echo "</td>";

													echo "<td>";
														$ls_fg_sexo =  $row_padron["V_FG_SEXO"];
														if ($ls_fg_sexo =='M') $ls_des_sexo = 'Masculino';
														if ($ls_fg_sexo =='F') $ls_des_sexo = 'Femenino';
														echo $ls_des_sexo;
													echo "</td>";

													// Cinturon
													$array_campo_pk	= array('N_COD_CINTURON');
													$array_valor_pk	= array($row_padron["N_COD_CINTURON_ING"]);
													$ls_cinturon	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_cinturon;
													echo "</td>";

													echo "<td>";
													echo $row_padron["V_MOVIL"];
													echo "</td>";

												echo "</tr>";
												$li_check++;
											}
											?>
										</tbody>

									</table>

								</div>
								
								<input class="btn btn-primary" type="submit" id ="btn_cancelar"  value="Regresar" formnovalidate onclick=this.form.action="<?php echo $url_lista?>">
								&nbsp;&nbsp;&nbsp;<span id="span_rpta"></span>

							</form>

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

	<script src="../recursos/js/jquery-1.11.2.min.js"></script>
	
	<script>
		$(function(){
			$('body').on('click', '#detalle input[type=checkbox]', function(event){
				var id_codigo = $(this).attr('data-idRegistro');
				var id_codigo_padre = $('#id_codigo_padre').val()
				id_marca = '0';
				if($(this).is(':checked')){
					id_marca = '1';
				}
				var parametros = {
					"id_codigo_padre" : id_codigo_padre,	
					"id_codigo" : id_codigo,
					"id_marca" : id_marca
				};
				$.ajax({
						data:  parametros,
						url:   '../controlador/mov_eventoprog_actualizar.php',
						type:  'post',
						beforeSend: function () {
							$('#span_rpta').text(''); // Limpieza
							console.log("Procesando, espere por favor...");
						},
						success:  function (response) {
							if(response == '0'){
								$('#span_rpta').text('Estado : Acción completada con éxito...');
								alert('Ocurrio un problema, vuelva a intentar.');
							}else{
								$('#span_rpta').text('Estado : Acción completada con éxito...');
							}							
						}
				});
			});		
		});
	</script>	

	<?php
}

?>