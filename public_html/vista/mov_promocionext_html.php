<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Usuarios (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta){

	// Enlaces
	$url_nuevo		= "mov_promocionext_nuevo.php";
	$url_editar		= "mov_promocionext_editar.php";
	$url_lista		= "mov_promocionext_lista.php";
	$url_prog		= "mov_promocionadoext_lista.php";
	$url_rpt_promocionados	= "Report/rpt_promoext_promocionados.php";
	$url_rpt_graduados	= "Report/rpt_promoext_graduados.php";
	$url_eliminar	= "../controlador/mov_promocionext_eliminar.php";

	// Instanciar clase
	$crud = new crud();

	// Armar Estructura
	$array_campo_pk = [];
	$array_valor_pk = [];

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_PROMOCIONEXT, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION', 'A', 0, 9999);

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
												<th>Descripción</th>
												<th>Lugar</th>
												<th>Fecha Programada</th>												
												<th>Nº de Promocionados</th>
												<th>Estado</th>
												<th>Acciones</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$url_editar_fila   	 	= $url_editar."?id_codigo=".($row["N_COD_PROMOCION"]);
												$url_eliminar_fila   	= $url_eliminar."?id_codigo=".($row["N_COD_PROMOCION"]);
												$url_prog_fila		 	= $url_prog."?id_codigo_padre=".($row["N_COD_PROMOCION"]);
												$url_rpt_promocionados_fila	= $url_rpt_promocionados."?id_codigo=".($row["N_COD_PROMOCION"]);
												$url_rpt_graduados_fila	= $url_rpt_graduados."?id_codigo=".($row["N_COD_PROMOCION"]);
												?>
												<tr>
													<?php

													echo "<td>";
													echo f_r_url($url_editar_fila, $row["V_DESCRIPCION"],$row["N_COD_PROMOCION"]);
													echo "</td>";

													echo "<td>";
													echo $row["V_LUGAR"];
													echo "</td>";

													echo "<td>";
													echo $row["D_FEC_PROG"];
													echo "</td>";

													echo "<td>";
														// Cantidad 
														$array_campo_pk	= array('N_COD_PROMOCION', 'V_FLAG_PROG');
														$array_valor_pk	= array($row["N_COD_PROMOCION"], '1');
														$li_cantidad	= $crud->fila_contar(DEF_TABLA_PROMOCIONEXTPROG, $array_campo_pk, $array_valor_pk);
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
													<a href="#deleteModal<?php echo $row["N_COD_PROMOCION"]; ?>" data-toggle="modal" ><i class="fa fa-trash-o" title = "Eliminar registro"></i></a>&nbsp;&nbsp;
													<a href="<?php echo $url_prog_fila?>">
														<i class="fa fa-calendar-check-o" title = "Programación de Promocionados"></i>
													</a>&nbsp;
													<a href="<?php echo $url_rpt_promocionados_fila?>" target="_blank">
														<i class="fa fa-file-pdf-o" title = "Ver Reporte de Promocinados"></i>
													</a>&nbsp;
													<a href="<?php echo $url_rpt_graduados_fila?>" target="_blank">
														<i class="fa fa-file-pdf-o" title = "Ver Reporte de Graduados"></i>
													</a>
													<?php
													echo "</td>";

													?>
													<div id="deleteModal<?php echo $row["N_COD_PROMOCION"]; ?>" class="modal fade">
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

	// Instanciar clase
	$crud = new crud();

	// Enlaces
	$url_lista		= "mov_promocionext_lista.php";
	$url_registrar	= "../controlador/mov_promocionext_registrar.php";
	$url_actualizar	= "../controlador/mov_promocionext_actualizar.php";
	$url_eliminar	= "../controlador/mov_promocionext_eliminar.php";

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
												title = "Generado por el sistema" placeholder="Codigo autogenerado" value="<?php echo $lb_edit?$array["N_COD_PROMOCION"]:""; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fecha">Fecha</label>
											<input type="date" class="form-control input-sm" id="id_fecha" name="id_fecha" required"
												title = "Formato Fecha" value="<?php echo $lb_edit?$array["D_FEC_PROG"]:""; ?>"> 
										</div>
									</div>
									<div class="col-sm-4">
									</div>
								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_descripcion">Descripción</label>
											<input type="text" class="form-control" id="id_descripcion" name="id_descripcion" maxlength="150" required pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s0-9 ]{1,150}" 
												title = "Letras y Números. Tamaño máximo: 150" placeholder="(*) Ejemplo : Promoción 2024" value="<?php echo $lb_edit?$array["V_DESCRIPCION"]:""; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_moneda">Moneda</label>
											<select class="form-control" id="id_cod_moneda" name="id_cod_moneda" required>
												<?php												
												// Recuperando datos para combo
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_tipo     = $crud->fila_listar(DEF_TABLA_MONEDA, $array_campo_pk, $array_valor_pk, 'N_COD_MONEDA', 'A', 0, 100);

												// Mostrando combo
												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_tipo = mysqli_fetch_assoc($array_tipo)) {
													echo "<option value=\"";
													echo $this_tipo["N_COD_MONEDA"];
													echo "\""; 
													// Si existen registros, ponerlo en el combo
													if ($lb_edit && $this_tipo["N_COD_MONEDA"] == $array["N_COD_MONEDA"]){
														echo " selected";
													}
													echo ">";
													echo $this_tipo["V_DES_CORTA"];
													echo "\n";
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_monto">Monto</label>
											<input type="number" class="form-control" id="id_monto" name="id_monto" maxlength="9" min="0" required
											title = "Campo numérico" placeholder="(*) Ejemplo : 12.75" value="<?php echo $lb_edit?$array["N_MONTO"]:$li_monto; ?>">
										</div>
									</div>
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
									echo "<input type=hidden name=id_codigo value=\"".$array["N_COD_PROMOCION"]."\">";
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

// Lista de Promocionados (Cuerpo - Drcha)
function f_listado_promocionados($as_titulo, $as_icono, $as_msgRpta, $id_codigo_padre){

	// Instanciar clase
	$crud = new crud();

	// Enlaces
	$url_cancelar = "mov_promocionext_lista.php";

	// Armar Estructura
	$array_campo_pk = ['N_COD_PROMOCION'];
	$array_valor_pk = [$id_codigo_padre];

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_PROMOCIONEXTPROG, $array_campo_pk, $array_valor_pk, 'N_COD_ESTUDIANTE', 'A', 0, 100);

	// Datos del Padre
	$ls_subtitulo	= $crud->fila_recuperar_campo(DEF_TABLA_PROMOCIONEXT, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');

	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<!-- Cabecera de Sección Contenido -->
		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo.' [ '.$ls_subtitulo.' ] - Promocionados';?>
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
												<th>Nº</th>
												<th>Apellidos y Nombres</th>
												<th>Edad</th>
												<th>Genero</th>
												<th>Cinturón Actual</th>
												<th>Cinturón Promoción</th>
												<th>Fecha Programada</th>
												<th>Hora Programada</th>
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

													// Cinturon Actual
													$array_campo_pk	= array('N_COD_CINTURON');
													$array_valor_pk	= array($row["N_COD_CINTURON_ACTUAL"]);
													$ls_cinturon	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_cinturon;
													echo "</td>";

													// Cinturon Nuevo
													$array_campo_pk	= array('N_COD_CINTURON');
													$array_valor_pk	= array($row["N_COD_CINTURON_NUEVO"]);
													$ls_cinturon	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_cinturon;
													echo "</td>";

													echo "<td>";
													echo $row["D_FEC_PROG"];
													echo "</td>";

													echo "<td>";
													echo substr($row["D_HORA_PROG"],0,5);
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
	$url_cancelar = "mov_promocionext_lista.php";

	// Armar Estructura
	$array_campo_pk = ['N_COD_PROMOCION'];
	$array_valor_pk = [$id_codigo_padre];

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_PROMOCIONEXTPROG, $array_campo_pk, $array_valor_pk, 'N_COD_ESTUDIANTE', 'A', 0, 100);

	// Datos del Padre
	$array_campo_pk = ['N_COD_PROMOCION'];
	$array_valor_pk = [$id_codigo_padre];
	$ls_subtitulo	= $crud->fila_recuperar_campo(DEF_TABLA_PROMOCIONEXT, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');

	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<!-- Cabecera de Sección Contenido -->
		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo.' [ '.$ls_subtitulo.' ] - Asistentes';?>
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
												<th>Nº</th>
												<th>Nombre Estudiante</th>
												<th>Edad</th>
												<th>Genero</th>
												<th>Cinturón Actual</th>
												<th>Cinturón Promoción</th>
												<th>Fecha Programada</th>
												<th>Hora Programada</th>
												<th>Asistencia</th>
												<th>Aprobación</th>
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

													// Cinturon Actual
													$array_campo_pk	= array('N_COD_CINTURON');
													$array_valor_pk	= array($row["N_COD_CINTURON_ACTUAL"]);
													$ls_cinturon	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_cinturon;
													echo "</td>";

													// Cinturon Nuevo
													$array_campo_pk	= array('N_COD_CINTURON');
													$array_valor_pk	= array($row["N_COD_CINTURON_NUEVO"]);
													$ls_cinturon	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_cinturon;
													echo "</td>";

													echo "<td>";
													echo $row["D_FEC_PROG"];
													echo "</td>";

													echo "<td>";
													echo substr($row["D_HORA_PROG"],0,5);
													echo "</td>";

													echo "<td>";
														$ls_fg_asis =  $row["V_FLAG_EJEC"];
														if ($ls_fg_asis =='1') $ls_des_asis = 'Asistió';
														if ($ls_fg_asis =='0') $ls_des_asis = 'No Asistió';
														echo $ls_des_asis;
													echo "</td>";

													echo "<td>";
														$ls_fg_aprob =  $row["V_FLAG_APROB"];
														if ($ls_fg_aprob =='1') $ls_des_aprob = 'Aprobado';
														if ($ls_fg_aprob =='0') $ls_des_aprob = 'No Aprobado';
														echo $ls_des_aprob;
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

?>