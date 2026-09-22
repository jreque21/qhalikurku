<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta, $an_id_codigo_padre){

	// Enlaces
	$url_nuevo		= "mov_matricula_nuevo.php?id_codigo_padre=".$an_id_codigo_padre;
	$url_editar		= "mov_matricula_editar.php";
	$url_lista		= "mov_matriculahorario_lista.php";
	$url_eliminar	= "../controlador/mov_matricula_eliminar.php?id_codigo_padre=".$an_id_codigo_padre;

	// Instanciar clase
	$crud = new crud();

	// Armar Estructura
	$array_campo_pk = ['N_COD_HORARIO'];
	$array_valor_pk = [$an_id_codigo_padre];

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_MATRICULA, $array_campo_pk, $array_valor_pk, 'N_COD_ESTUDIANTE', 'A', 0, 1000);

	// Datos Padre
	$ls_subtitulo	= $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');

	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<!-- Cabecera de Sección Contenido -->
		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo.' - Estudiantes';?>
			</h1>
			</br>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item active" aria-current="page"><b>Horario : </b><?php echo $ls_subtitulo;?></li>
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
												<th>Estudiante</th>
												<th>Edad</th>
												<th>Cinturón</th>
												<th>Fecha Matrícula</th>
												<th>Fecha Inicio</th>
												<th>Forma de Pago</th>
												<th>Pago ?</th>												
												<th>Editar</th>
												<th>Eliminar</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$url_editar_fila   = $url_editar."?id_codigo=".($row["N_COD_MATRICULA"]);
												$url_eliminar_fila = $url_eliminar."&id_codigo=".($row["N_COD_MATRICULA"]);
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
													$li_cinturon_actual = $crud->f_get_cinturonActual($row["N_COD_ESTUDIANTE"]);
													$array_campo_pk	= array('N_COD_CINTURON');
													$array_valor_pk	= array($li_cinturon_actual);
													$ls_cinturon	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_cinturon;
													echo "</td>";
													
													echo "<td>";
													echo $row["D_FEC_MATRICULA"];
													echo "</td>";

													echo "<td>";
													echo $row["D_FEC_INICIO"];
													echo "</td>";
													
													// Tipo de Pago
													$array_campo_pk	= array('N_COD_FORMAPAGO');
													$array_valor_pk	= array($row["N_COD_FORMAPAGO"]);
													$ls_formapago	= $crud->fila_recuperar_campo(DEF_TABLA_FORMAPAGO, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_formapago;
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
						<span class="glyphicon glyphicon-plus"></span>&nbsp;Nuevo Estudiante
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
		$ld_fec_inicio	= $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'D_FEC_INICIO');
		$li_cod_sede	= $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'N_COD_SEDE');
		$li_cod_tarifa	= $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'N_COD_TARIFA');
		$array_campo_pk	= array('N_COD_TARIFA');
		$array_valor_pk	= array($li_cod_tarifa);
		$li_cod_moneda	= $crud->fila_recuperar_campo(DEF_TABLA_TARIFA, $array_campo_pk, $array_valor_pk, 'N_COD_MONEDA');
		$li_monto		= $crud->fila_recuperar_campo(DEF_TABLA_TARIFA, $array_campo_pk, $array_valor_pk, 'N_MONTO');
		$li_descuento	= $crud->fila_recuperar_campo(DEF_TABLA_TARIFA, $array_campo_pk, $array_valor_pk, 'N_DESCUENTO');
		$li_neto 		= $li_monto - $li_descuento;
	}else{
		$li_id_codigo_padre = $array["N_COD_HORARIO"];
		$li_cod_moneda 		= $array["N_COD_MONEDA"];
		$li_cod_estudiante	= $array["N_COD_ESTUDIANTE"];
		// Sede
		$array_campo_pk	= array('N_COD_HORARIO');
		$array_valor_pk	= array($li_id_codigo_padre);
		$li_cod_sede	= $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'N_COD_SEDE');
		// Nombres
		$array_campo_pk	= array('N_COD_ESTUDIANTE');
		$array_valor_pk	= array($li_cod_estudiante);
		$row_est		= $crud->fila_recuperar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk);
		$ls_nombres 	= $row_est['V_APE_PATERNO'].' '.$row_est['V_APE_MATERNO'].' '.$row_est['V_NOMBRES'];
		$url_eliminar	= "../controlador/mov_matricula_eliminar.php?id_codigo_padre=".$li_id_codigo_padre."&id_codigo=".$array['N_COD_MATRICULA'];
	}

	// Enlaces
	$url_lista		= "mov_matricula_lista.php?id_codigo_padre=".$li_id_codigo_padre;
	$url_registrar	= "../controlador/mov_matricula_registrar.php";
	$url_actualizar	= "../controlador/mov_matricula_actualizar.php";

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
											<input type="text" class="form-control" id="id_codigo" name="id_codigo" maxlength="10" disabled
												title = "Generado por el sistema" placeholder="Codigo autogenerado" value="<?php echo $lb_edit?$array["N_COD_MATRICULA"]:""; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_horario">Horario</label>
											<select class="form-control" id="id_cod_horario" name="id_cod_horario" required disabled>
												<?php												
												// Recuperando datos para combo
												$array_campo_pk	= array();
												$array_valor_pk	= array();
												$array_tipo     = $crud->fila_listar(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION', 'A', 0, 100);
												
												// Mostrando combo
												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_tipo = mysqli_fetch_assoc($array_tipo)) {
													echo "<option value=\"";
													echo $this_tipo["N_COD_HORARIO"];
													echo "\"";
													// Si existen registros, ponerlo en el combo
													if ($this_tipo["N_COD_HORARIO"] == $li_id_codigo_padre){
														echo " selected";
													}
													echo ">";
													echo $this_tipo["V_DESCRIPCION"];
													echo "\n";
												}
												?>
											</select>
											<input type="hidden" class="form-control" id="id_cod_horario_hide" name="id_cod_horario_hide"  
												 value="<?php echo $li_id_codigo_padre; ?>"> 
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fec_inicio">Fecha de Inicio de Clases</label>
											<input type="date" class="form-control input-sm" id="id_fec_inicio" name="id_fec_inicio" required"
												title = "Formato Fecha" value="<?php echo $lb_edit?$array["D_FEC_INICIO"]:$ld_fec_inicio; ?>"> 
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
									
									<?php
									// Armar Estructura
									$array_campo_pk = ['V_FLAG_ESTADO','V_TIPO_EST','N_COD_SEDE'];
									$array_valor_pk = ['1', 'EST_INT', $li_cod_sede];

									// Padrón de Estudiantes
									//$array_padron = $crud->fila_listar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO, V_APE_MATERNO, V_NOMBRES', 'A', 0, 100);
									$array_padron = $crud->fila_listar_not_in(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk, DEF_TABLA_MATRICULA, 'N_COD_ESTUDIANTE', 'N_COD_HORARIO', $li_id_codigo_padre,'V_APE_PATERNO, V_APE_MATERNO, V_NOMBRES', 'A', 0, 9999);
									
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
																		<th>Grado</th>
																	</tr>
																</thead>

																<tbody>
																	<?php
																	$li_contador = 0;
																	while ($row_padron = mysqli_fetch_assoc($array_padron)) {
																	$li_contador = $li_contador + 1;
																	?>
																	<tr class = "tr_listaDetalle" data-id="<?php echo $row_padron["N_COD_ESTUDIANTE"];?>" id="<?php echo $row_padron["V_APE_PATERNO"].' '.$row_padron["V_APE_MATERNO"].' '.$row_padron["V_NOMBRES"];?>">

																		<?php
																		echo "<td>";
																		echo "</td>";
																						
																		echo "<td>";
																		echo $row_padron["V_APE_PATERNO"].' '.$row_padron["V_APE_MATERNO"].' '.$row_padron["V_NOMBRES"];
																		echo "</td>";
																		
																		echo "<td>";
																		echo f_get_edad($row_padron["D_FEC_NACIMIENTO"]);
																		echo "</td>";

																		// Cinturon
																		$array_campo_pk	= array('N_COD_CINTURON');
																		$array_valor_pk	= array($row_padron["N_COD_CINTURON_ING"]);
																		$ls_cinturon	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
																		echo "<td>";
																		echo $ls_cinturon;
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

								</div>
								
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_monto_tarifa">Monto</label>
											<input type="number" class="form-control" id="id_monto_tarifa" name="id_monto_tarifa" maxlength="9" min="0" required
											title = "Campo numérico" placeholder="(*) Ejemplo : 12.75" value="<?php echo $lb_edit?$array["N_MONTO_TARIFA"]:$li_monto; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_monto_descuento">Descuento</label>
											<input type="number" class="form-control" id="id_monto_descuento" name="id_monto_descuento" maxlength="9" step="0.01" min="0"  
											title = "Campo numérico" placeholder="Ejemplo : 12.75" value="<?php echo $lb_edit?$array["N_MONTO_DSCTO"]:$li_descuento; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_monto_neto">Neto</label>
											<input type="number" class="form-control" id="id_monto_neto" name="id_monto_neto" maxlength="9" step="0.01" min="0" disabled 
											title = "Campo numérico" placeholder="Ejemplo : 12.75" value="<?php echo $lb_edit?$array["N_MONTO_NETO"]:$li_neto; ?>">
										</div>
									</div>
									
								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_formapago">Forma de Pago</label>
											<select class="form-control" id="id_cod_formapago" name="id_cod_formapago" required>
												<?php												
												// Recuperando datos para combo
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_tipo     = $crud->fila_listar(DEF_TABLA_FORMAPAGO, $array_campo_pk, $array_valor_pk, 'N_COD_FORMAPAGO', 'A', 0, 100);

												// Mostrando combo
												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_tipo = mysqli_fetch_assoc($array_tipo)) {
													echo "<option value=\"";
													echo $this_tipo["N_COD_FORMAPAGO"];
													echo "\"";
													// Si existen registros, ponerlo en el combo
													if ($lb_edit && $this_tipo["N_COD_FORMAPAGO"] == $array["N_COD_FORMAPAGO"]){
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
								</div>

								<?php
								if ($lb_edit) {
									echo "<input type=hidden name=id_codigo value=\"".$array["N_COD_MATRICULA"]."\">";
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

		// Eventos
		mybtn_sel_aceptar.addEventListener("click", f_js_seleccionar);			

		// Evento Fila TR
		$(".tr_listaDetalle" ).click(function(e) {	
			e.preventDefault();
			myRow_Codigo = $(this).attr("data-id");
			myRow_Data = $(this).attr("id");
		});

		// Función de selección de registro
		function f_js_seleccionar(){
			
			// Setear datos en formulario origen
			$('#id_cod_estudiante').val(myRow_Codigo);
			$('#id_nombres').val(myRow_Data);
			
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

		// Actualizar Neto
		id_monto_tarifa.oninput = function() {
			val_monto = id_monto_tarifa.value;
			val_dscto = id_monto_descuento.value;
			val_neto  = parseFloat(val_monto) - parseFloat(val_dscto)
			$('#id_monto_neto').val(val_neto);
		};
		id_monto_descuento.oninput = function() {
			val_monto = id_monto_tarifa.value;
			val_dscto = id_monto_descuento.value;
			val_neto  = parseFloat(val_monto) - parseFloat(val_dscto)
			$('#id_monto_neto').val(val_neto);
		};									

		/*$('#combo1').on('change', function(){			
			// Capturar Valor
			var valorCodigo = this.value;
			var valorTexto  = $(this).find('option:selected').text();
			//alert(valorTexto);
			// Mostrarlo en Formulario
			$('#span1').text(valorTexto);
			// myRowDatos   = document.getElementById("listaDetalle").rows[3].cells[1].innerText;
		});*/

	</script>	

	<?php
}

?>