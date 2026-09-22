<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Promocionados (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta, $an_id_codigo_padre){

	// Enlaces
	$url_nuevo		= "mov_promocionado_nuevo.php?id_codigo_padre=".$an_id_codigo_padre;
	$url_editar		= "mov_promocionado_editar.php?id_codigo_padre=".$an_id_codigo_padre;
	$url_lista		= "mov_promocion_lista.php";
	$url_eliminar	= "../controlador/mov_promocionado_eliminar.php?id_codigo_padre=".$an_id_codigo_padre;

	// Instanciar clase
	$crud = new crud();

	// Armar Estructura
	$array_campo_pk = ['N_COD_PROMOCION'];
	$array_valor_pk = [$an_id_codigo_padre];

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_PROMOCIONPROG, $array_campo_pk, $array_valor_pk, 'N_COD_ESTUDIANTE', 'A', 0, 9999);

	// Datos Padre
	$ls_subtitulo	= $crud->fila_recuperar_campo(DEF_TABLA_PROMOCION, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');
	
	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<!-- Cabecera de Sección Contenido -->
		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo.' - Promocionados';?>
			</h1>
			</br>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item active" aria-current="page"><b>Promoción Nª : </b><?php echo $ls_subtitulo;?></li>
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
												<th>Nombre del Estudiante</th>
												<th>Edad</th>
												<th>Cinturón Actual</th>
												<th>Cinturón Promoción</th>
												<th>Fecha Programada</th>
												<th>Hora</th>
												<th>Pago ?</th>												
												<th>Editar</th>
												<th>Eliminar</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$ls_pago   = '';
												$url_editar_fila   = $url_editar."&id_codigo=".($row["N_COD_ESTUDIANTE"]);
												$url_eliminar_fila = $url_eliminar."&id_codigo=".($row["N_COD_ESTUDIANTE"]);
												?>
												<tr>
													<?php

													// Datos Estudiante
													$array_campo_pk	= array('N_COD_ESTUDIANTE');
													$array_valor_pk	= array($row["N_COD_ESTUDIANTE"]);
													$array_estudiante = $crud->fila_recuperar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk);
													echo "<td>";
													echo $array_estudiante['V_APE_PATERNO'].' '.$array_estudiante['V_APE_MATERNO'].' '.$array_estudiante['V_NOMBRES'];
													echo "</td>";

													echo "<td>";
													echo f_get_edad($array_estudiante["D_FEC_NACIMIENTO"]);
													echo "</td>";

													// Cinturón Actual
													$array_campo_pk	= array('N_COD_CINTURON');
													$array_valor_pk	= array($row["N_COD_CINTURON_ACTUAL"]);
													$ls_cinturonActual	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_cinturonActual;
													echo "</td>";

													// Cinturón Nuevo
													$array_campo_pk	= array('N_COD_CINTURON');
													$array_valor_pk	= array($row["N_COD_CINTURON_NUEVO"]);
													$ls_cinturonNuevo	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_cinturonNuevo;
													echo "</td>";

													echo "<td>";
													echo $row["D_FEC_PROG"];
													echo "</td>";

													echo "<td>";
													echo substr($row["D_HORA_PROG"],0,5);
													echo "</td>";
													
													// Pago
													echo "<td>";
														$ls_fg_pago =  $row["V_FLAG_PAGO"];
														if ($ls_fg_pago =='0') $ls_pago = 'Pendiente';
														if ($ls_fg_pago =='1') $ls_pago = 'Pagado';
														echo $ls_pago;
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
													<a href="#deleteModal<?php echo $row["N_COD_ESTUDIANTE"]; ?>" data-toggle="modal" ><i class="fa fa-trash-o"></i></a>
													<div id="deleteModal<?php echo $row["N_COD_ESTUDIANTE"]; ?>" class="modal fade">
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
						<span class="glyphicon glyphicon-plus"></span>&nbsp;Agregar Estudiante
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
		$array_campo_pk	= array('N_COD_PROMOCION');
		$array_valor_pk	= array($li_id_codigo_padre);
		$li_cod_sede	= $crud->fila_recuperar_campo(DEF_TABLA_PROMOCION, $array_campo_pk, $array_valor_pk, 'N_COD_SEDE');
		$ld_fec_prog	= $crud->fila_recuperar_campo(DEF_TABLA_PROMOCION, $array_campo_pk, $array_valor_pk, 'D_FEC_PROG');		
		$li_cod_moneda	= $crud->fila_recuperar_campo(DEF_TABLA_PROMOCION, $array_campo_pk, $array_valor_pk, 'N_COD_MONEDA');
		$li_monto		= $crud->fila_recuperar_campo(DEF_TABLA_PROMOCION, $array_campo_pk, $array_valor_pk, 'N_MONTO');
	}else{
		$li_id_codigo_padre = $array["N_COD_PROMOCION"];
		$li_cod_moneda 		= $array["N_COD_MONEDA"];
		$li_cod_estudiante	= $array["N_COD_ESTUDIANTE"];
		$li_cod_cinturon_actual = $array["N_COD_CINTURON_ACTUAL"];
		// Datos Estudiante
		$array_campo_pk	= array('N_COD_ESTUDIANTE');
		$array_valor_pk	= array($li_cod_estudiante);
		$row_est		= $crud->fila_recuperar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk);
		$ls_nombres 	= $row_est['V_APE_PATERNO'].' '.$row_est['V_APE_MATERNO'].' '.$row_est['V_NOMBRES'];
		// Sede
		$array_campo_pk	= array('N_COD_PROMOCION');
		$array_valor_pk	= array($li_id_codigo_padre);
		$li_cod_sede	= $crud->fila_recuperar_campo(DEF_TABLA_PROMOCION, $array_campo_pk, $array_valor_pk, 'N_COD_SEDE');
		// Enlaces
		$url_eliminar	= "../controlador/mov_promocionado_eliminar.php?id_codigo_padre=".$li_id_codigo_padre."&id_codigo=".$array['N_COD_ESTUDIANTE'];
	}

	// Enlaces
	$url_lista		= "mov_promocionado_lista.php?id_codigo_padre=".$li_id_codigo_padre;
	$url_registrar	= "../controlador/mov_promocionado_registrar.php";
	$url_actualizar	= "../controlador/mov_promocionado_actualizar.php";

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
											<label for="id_cod_promocion">Promoción</label>
											<select class="form-control" id="id_cod_promocion" name="id_cod_promocion" required disabled>
												<?php												
												// Recuperando datos para combo
												$array_campo_pk	= array();
												$array_valor_pk	= array();
												$array_tipo     = $crud->fila_listar(DEF_TABLA_PROMOCION, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION', 'A', 0, 9999);
												
												// Mostrando combo
												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_tipo = mysqli_fetch_assoc($array_tipo)) {
													echo "<option value=\"";
													echo $this_tipo["N_COD_PROMOCION"];
													echo "\"";
													// Si existen registros, ponerlo en el combo
													if ($this_tipo["N_COD_PROMOCION"] == $li_id_codigo_padre){
														echo " selected";
													}
													echo ">";
													echo $this_tipo["V_DESCRIPCION"];
													echo "\n";
												}
												?>
											</select>
											<input type="hidden" class="form-control" id="id_cod_promocion_hide" name="id_cod_promocion_hide"  
												 value="<?php echo $li_id_codigo_padre; ?>"> 
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fec_prog">Fecha Programada</label>
											<input type="date" class="form-control input-sm" id="id_fec_prog" name="id_fec_prog" required"
												title = "Formato Fecha" value="<?php echo $lb_edit?$array["D_FEC_PROG"]:$ld_fec_prog; ?>"> 
										</div>	
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_hora_prog">Hora Programada</label>
											<input type="time" class="form-control input-sm" id="id_hora_prog" name="id_hora_prog" required"
												title = "Formato Hora" value="<?php echo $lb_edit?$array["D_HORA_PROG"]:""; ?>"> 
										</div>	
									</div>
								</div>
								
								<div class="row">
									<div class="col-sm-8">
										<label for="id_nombres">Estudiante</label>
										<div class="input-group">									
											<input type="text" class="form-control" id="id_nombres" name="id_nombres" maxlength="150" required pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s ]{1,150}" disabled 
												title = "Letras. Tamaño máximo: 150" placeholder="(*) Seleccionar Lista" value="<?php echo $lb_edit?$ls_nombres:""; ?>"> 
											<span class="input-group-btn">
												<button id="btn_lista_estudiante" class="btn btn-primary" type="button" data-toggle="modal" data-target="#buscarEstudianteModal<?php echo $li_id_codigo_padre; ?>"> 
												Lista de Estudiantes &nbsp; <span class="fa fa-address-book-o icon"> </span>
												</button>
											</span>
											<input type="hidden" class="form-control" id="id_cod_estudiante" name="id_cod_estudiante" required 
												 value="<?php echo $lb_edit?$array["N_COD_ESTUDIANTE"]:""; ?>"> 
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_cinturon_actual">Cinturón Actual</label>
											<select class="form-control" id="id_cod_cinturon_actual" name="id_cod_cinturon_actual">
												<?php												
												// Recuperando datos para combo
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_tipo     = $crud->fila_listar(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'N_ORDEN', 'A', 0, 100);

												// Mostrando combo
												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_tipo = mysqli_fetch_assoc($array_tipo)) {
													echo "<option value=\"";
													echo $this_tipo["N_COD_CINTURON"];
													echo "\"";
													// Si existen registros, ponerlo en el combo
													if ($lb_edit && $this_tipo["N_COD_CINTURON"] == $li_cod_cinturon_actual){
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
								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_cinturon_nuevo">Cinturón Promoción</label>
											<select class="form-control" id="id_cod_cinturon_nuevo" name="id_cod_cinturon_nuevo">
												<?php												
												// Recuperando datos para combo
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_tipo     = $crud->fila_listar(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'N_ORDEN', 'A', 0, 100);

												// Mostrando combo
												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_tipo = mysqli_fetch_assoc($array_tipo)) {
													echo "<option value=\"";
													echo $this_tipo["N_COD_CINTURON"];
													echo "\"";
													// Si existen registros, ponerlo en el combo
													if ($lb_edit && $this_tipo["N_COD_CINTURON"] == $array["N_COD_CINTURON_NUEVO"]){
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
													if ($this_tipo["N_COD_MONEDA"] == $li_cod_moneda){
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
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_mediopago">Medio de Pago</label>
											<select class="form-control" id="id_cod_mediopago" name="id_cod_mediopago">
												<?php												
												// Recuperando datos para combo
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_tipo     = $crud->fila_listar(DEF_TABLA_MEDIOPAGO, $array_campo_pk, $array_valor_pk, 'N_COD_MEDIOPAGO', 'A', 0, 100);

												// Mostrando combo
												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_tipo = mysqli_fetch_assoc($array_tipo)) {
													echo "<option value=\"";
													echo $this_tipo["N_COD_MEDIOPAGO"];
													echo "\"";
													// Si existen registros, ponerlo en el combo
													if ($lb_edit && $this_tipo["N_COD_MEDIOPAGO"] == $array["N_COD_MEDIOPAGO"]){
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
											<label for="id_comprobante">Comprobante</label>
											<input type="text" class="form-control" id="id_comprobante" name="id_comprobante" maxlength="100" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s0-9 -_()]{1,100}"
												title = "Letras y Números. Tamaño máximo: 100" placeholder="Ejemplo : FACT-2023-001" value="<?php echo $lb_edit?$array["V_COMPROBANTE"]:""; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="checkbox">
											<?php
											if($lb_edit) {
												$ls_fg_pago = $array["V_FLAG_PAGO"];
											}else{
												$ls_fg_pago = '0';
											}
											?>
											<label>
											<input type="checkbox" id="id_flag_pago" name="id_flag_pago" <?php if("0" <> $ls_fg_pago) echo "checked";?> value="1"> Realizó Pago ?
											</label>
										</div>
									</div>
								</div>
								
								<div class="form-group">
									<label for="id_observacion">Observaciones</label>
									<textarea class="form-control" rows="3" id="id_observacion" name="id_observacion" placeholder="Ejemplo : Llegar 15 min antes"><?php echo $lb_edit?$array["V_OBSERVACION"]:""; ?></textarea>
								</div>

								<?php
								// Armar Estructura
								$array_campo_pk = ['V_FLAG_ESTADO','V_TIPO_EST','N_COD_SEDE'];
								$array_valor_pk = ['1', 'EST_INT', $li_cod_sede];

								// Padrón de Estudiantes
								$array_padron = $crud->fila_listar_not_in(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk, DEF_TABLA_PROMOCIONPROG, 'N_COD_ESTUDIANTE', 'N_COD_PROMOCION', $li_id_codigo_padre,'V_APE_PATERNO, V_APE_MATERNO, V_NOMBRES', 'A', 0, 99999);
								
								?>

								<div id="buscarEstudianteModal<?php echo $li_id_codigo_padre; ?>" class="modal fade">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
												<h4 class="modal-title">Lista de Estudiantes</h4>
											</div>
											<div class="modal-body">
												<?php
												if (!($array_padron)) {
													?>
													<div class="alert alert-warning">
														<?php echo DEF_MSG_SIN_REGISTROS;?>
													</div>
													<?php
												}else {
													?>
													<div class="table-responsive">

														<table id="listaDetalle" class="table table-striped table-bordered table-hover">

															<thead>
																<tr>
																	<th>Marca (S/N)</th>
																	<th>Apellidos y Nombres</th>
																	<th>Edad</th>
																	<th>Cinturón Actual</th>
																</tr>
															</thead>

															<tbody>
																<?php
																$li_contador = 0;
																while ($row_padron = mysqli_fetch_assoc($array_padron)) {
																$li_contador = $li_contador + 1;
																$li_cinturon_actual = $crud->f_get_cinturonActual($row_padron["N_COD_ESTUDIANTE"]);
																$li_cinturon_new    = $crud->f_get_cinturonNuevo($row_padron["N_COD_ESTUDIANTE"]);
																?>
																<tr class = "tr_listaDetalle" data-id="<?php echo $row_padron["N_COD_ESTUDIANTE"];?>" 
																							  id="<?php echo $row_padron["V_APE_PATERNO"].' '.$row_padron["V_APE_MATERNO"].' '.$row_padron["V_NOMBRES"];?>"
																							  data-cintact="<?php echo $li_cinturon_actual;?>"
																							  data-cintnew="<?php echo $li_cinturon_new;?>"
																							  >

																	<?php
																	echo "<td>";
																	echo "</td>";
																					
																	echo "<td>";
																	echo $row_padron["V_APE_PATERNO"].' '.$row_padron["V_APE_MATERNO"].' '.$row_padron["V_NOMBRES"];
																	echo "</td>";
																	
																	echo "<td>";
																	echo f_get_edad($row_padron["D_FEC_NACIMIENTO"]);
																	echo "</td>";

																	// Cinturón Actual
																	$array_campo_pk	= array('N_COD_CINTURON');
																	$array_valor_pk	= array($li_cinturon_actual);
																	$ls_cinturonActual	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
																	echo "<td>";
																	echo $ls_cinturonActual;
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
											</div>
											<div class="modal-footer">
											<button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
											<button type="button" class="btn btn-success" data-dismiss="modal" id="btn_sel_aceptar">Aceptar</button>
											</div>
										</div>
									</div>
								</div>		

								<?php
								if ($lb_edit) {
									echo "<input type=hidden name=id_codigo value=\"".$array["N_COD_ESTUDIANTE"]."\">";
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
	
	<script>

		// Capturar Valores
		let mybtn_sel_aceptar = document.getElementById("btn_sel_aceptar");	
		let myRow_Codigo	  = 0; // Código
		let myRow_Data		  = 0; // Nombres
		let myRow_CintAct	  = 0; // Cinturón Actual
		let myRow_CintNew	  = 0; // Cinturón New

		// Eventos
		mybtn_sel_aceptar.addEventListener("click", f_js_seleccionar);			

		// Evento Fila TR
		$(".tr_listaDetalle" ).click(function(e) {	
			e.preventDefault();
			myRow_Codigo 	= $(this).attr("data-id");
			myRow_Data 		= $(this).attr("id");
			myRow_CintAct 	= $(this).attr("data-cintact");
			myRow_CintNew 	= $(this).attr("data-cintnew");
		});

		// Función de selección de registro
		function f_js_seleccionar(){
			
			// Setear datos en formulario origen
			$('#id_cod_estudiante').val(myRow_Codigo);
			$('#id_nombres').val(myRow_Data);
			$('#id_cod_cinturon_actual').val(myRow_CintAct);			
			$('#id_cod_cinturon_nuevo').val(myRow_CintNew);	
			
		} 

		// Evento de Formulario
		document.addEventListener("DOMContentLoaded", function() {
			document.getElementById('form_mtto').addEventListener('submit', f_js_agregar); 
		});

		// Función de agregar registro
		function f_js_agregar(event){			
			// Detener
			event.preventDefault();
			// Validar Ingreso de Estudiante
			var val_id_estudiante = id_cod_estudiante.value;
			if (val_id_estudiante.trim() === "") {  
				alert('Falta seleccionar estudiante!');  
				return;
			}
			this.submit();			
		}

	</script>	

	<?php
}

// Lista de Graduados (Cuerpo - Drcha)
function f_listado_ejec($as_titulo, $as_icono, $as_msgRpta){

	// Enlaces
	$url_asistencia	= "mov_promocionadoejec_editar.php";
	$url_graduado	= "mov_graduado_editar.php";
	$url_lista		= "mov_graduado_lista.php";

	// Instanciar clase
	$crud = new crud();

	// Armar Estructura
	$array_campo_pk = ['V_FLAG_ESTADO'];
	$array_valor_pk = ['1'];

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_PROMOCION, $array_campo_pk, $array_valor_pk, 'D_FEC_PROG', 'D', 0, 999);

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
												<th>Sede</th>
												<th>Nº de Promocionados</th>
												<th>Nº de Asistentes</th>
												<th>Nº de Graduados</th>
												<th>Acciones</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$url_asistencia_fila = $url_asistencia."?id_codigo=".($row["N_COD_PROMOCION"]);
												$url_graduado_fila   = $url_graduado."?id_codigo=".($row["N_COD_PROMOCION"]);
												?>
												<tr>
													<?php

													echo "<td>";
													echo $row["V_DESCRIPCION"];
													echo "</td>";

													echo "<td>";
													echo $row["V_LUGAR"];
													echo "</td>";

													echo "<td>";
													echo $row["D_FEC_PROG"];
													echo "</td>";

													// Sede
													$array_campo_pk	= array('N_COD_SEDE');
													$array_valor_pk	= array($row["N_COD_SEDE"]);
													$ls_sede		= $crud->fila_recuperar_campo(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE');
													echo "<td>";
													echo $ls_sede;
													echo "</td>";

													echo "<td>";
														// Cantidad Promocionados
														$array_campo_pk	= array('N_COD_PROMOCION', 'V_FLAG_PROG');
														$array_valor_pk	= array($row["N_COD_PROMOCION"], '1');
														$li_cantidad	= $crud->fila_contar(DEF_TABLA_PROMOCIONPROG, $array_campo_pk, $array_valor_pk);
														echo $li_cantidad;
													echo "</td>";

													echo "<td>";
														// Cantidad Asistentes
														$array_campo_pk	= array('N_COD_PROMOCION', 'V_FLAG_PROG', 'V_FLAG_EJEC');
														$array_valor_pk	= array($row["N_COD_PROMOCION"], '1', '1');
														$li_cantidad	= $crud->fila_contar(DEF_TABLA_PROMOCIONPROG, $array_campo_pk, $array_valor_pk);
														echo $li_cantidad;
													echo "</td>";

													echo "<td>";
														// Cantidad Graduados
														$array_campo_pk	= array('N_COD_PROMOCION', 'V_FLAG_PROG', 'V_FLAG_EJEC', 'V_FLAG_APROB');
														$array_valor_pk	= array($row["N_COD_PROMOCION"], '1','1','1');
														$li_cantidad	= $crud->fila_contar(DEF_TABLA_PROMOCIONPROG, $array_campo_pk, $array_valor_pk);
														echo $li_cantidad;
													echo "</td>";

													echo "<td align='center'>";
													?>
													<a href="<?php echo $url_asistencia_fila?>">
														<i class="fa fa-check-square-o" title = "Registrar Asistencia"></i>
													</a>&nbsp;
													<a href="<?php echo $url_graduado_fila?>">
														<i class="fa fa-graduation-cap" title = "Registrar Graduados"></i>
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

// Formulario de Asistencia de Promocionados
function f_formulario_asistencia($as_titulo, $as_icono, $as_msgRpta, $id_codigo_padre = "") {

	//Inicalizando variables
	$inhabilitado = "disabled='disabled'";

	// Instanciar clase
	$crud = new crud();

	// Definir Estructura
	$array_campo_pk = array('N_COD_PROMOCION', 'V_FLAG_PROG');
	$array_valor_pk = array($id_codigo_padre, '1');

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_PROMOCIONPROG, $array_campo_pk, $array_valor_pk, 'N_COD_ESTUDIANTE', 'A', 0, 1000);

	// Datos Padre
	$array_campo_pk = array('N_COD_PROMOCION');
	$array_valor_pk = array($id_codigo_padre);
	$ls_subtitulo	= $crud->fila_recuperar_campo(DEF_TABLA_PROMOCION, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');

	// Enlaces
	$url_lista		= "mov_graduado_lista.php";
	$ls_modo 		= DEF_MSG_FORM_SELECCION;

	// Formulario HTML
	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<!-- Cabecera de Sección Contenido -->
		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo.' - Asistencia';?>
			</h1>
			</br>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item active" aria-current="page"><b>Promoción Nª : </b><?php echo $ls_subtitulo;?></li>
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
												<th>Asistencia (S/N)</th>
												<th>Nombre Estudiante</th>
												<th>Edad</th>
												<th>Género</th>
												<th>Cinturón Actual</th>
												<th>Cinturón Promoción</th>
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
															<input type="checkbox" data-idRegistro = "<?php echo $row["N_COD_ESTUDIANTE"];?>" name="id_flag_ejec" <?php if("0" <> $row["V_FLAG_EJEC"]) echo "checked";?> value="<?php echo $li_check; ?>">
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

													// Cinturón Actual
													$array_campo_pk	= array('N_COD_CINTURON');
													$array_valor_pk	= array($row["N_COD_CINTURON_ACTUAL"]);
													$ls_cinturon	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_cinturon;
													echo "</td>";

													// Cinturón Nuevo
													$array_campo_pk	= array('N_COD_CINTURON');
													$array_valor_pk	= array($row["N_COD_CINTURON_NUEVO"]);
													$ls_cinturon	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_cinturon;
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
						url:   '../controlador/mov_promocionadoejec_actualizar.php',
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

// Formulario de Graduados
function f_formulario_graduacion($as_titulo, $as_icono, $as_msgRpta, $id_codigo_padre = "") {

	//Inicalizando variables
	$inhabilitado = "disabled='disabled'";

	// Instanciar clase
	$crud = new crud();

	// Definir Estructura
	$array_campo_pk = array('N_COD_PROMOCION', 'V_FLAG_PROG', 'V_FLAG_EJEC');
	$array_valor_pk = array($id_codigo_padre, '1', '1');

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_PROMOCIONPROG, $array_campo_pk, $array_valor_pk, 'N_COD_ESTUDIANTE', 'A', 0, 99999);

	// Datos Padre
	$array_campo_pk = array('N_COD_PROMOCION');
	$array_valor_pk = array($id_codigo_padre);
	$ls_subtitulo	= $crud->fila_recuperar_campo(DEF_TABLA_PROMOCION, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');

	// Enlaces
	$url_lista		= "mov_graduado_lista.php";
	$ls_modo 		= DEF_MSG_FORM_SELECCION;

	// Formulario HTML
	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<!-- Cabecera de Sección Contenido -->
		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo.' - Graduación';?>
			</h1>
			</br>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item active" aria-current="page"><b>Promoción Nª : </b><?php echo $ls_subtitulo;?></li>
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
												<th>Aprobación (S/N)</th>
												<th>Nombre Estudiante</th>
												<th>Edad</th>
												<th>Genero</th>
												<th>Cinturón Actual</th>
												<th>Cinturón Promoción</th>
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
															<input type="checkbox" data-idRegistro = "<?php echo $row["N_COD_ESTUDIANTE"];?>" name="id_flag_aprob" <?php if("0" <> $row["V_FLAG_APROB"]) echo "checked";?> value="<?php echo $li_check; ?>">
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

													// Cinturón Actual
													$array_campo_pk	= array('N_COD_CINTURON');
													$array_valor_pk	= array($row["N_COD_CINTURON_ACTUAL"]);
													$ls_cinturon	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_cinturon;
													echo "</td>";

													// Cinturón Nuevo
													$array_campo_pk	= array('N_COD_CINTURON');
													$array_valor_pk	= array($row["N_COD_CINTURON_NUEVO"]);
													$ls_cinturon	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_cinturon;
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
						url:   '../controlador/mov_graduado_actualizar.php',
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