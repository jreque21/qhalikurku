<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Usuarios (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta){

	// Enlaces
	$url_nuevo		= "mov_traslado_nuevo.php";
	$url_editar		= "mov_traslado_editar.php";
	$url_lista		= "mov_traslado_lista.php";

	// Instanciar clase
	$crud = new crud();

	// Armar Estructura
	$array_campo_pk = [];
	$array_valor_pk = [];

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_TRASLADO, $array_campo_pk, $array_valor_pk, 'N_COD_TRASLADO', 'D', 0, 99999);

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
												<th>Código</th>
												<th>Estudiante</th>
												<th>Fecha Inicio</th>
												<th>Fecha Fin</th>
												<th>Indefinido</th>
												<th>Sede Origen</th>
												<th>Sede Destino</th>
												<th>Estado</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$url_editar_fila   = $url_editar."?id_codigo=".($row["N_COD_TRASLADO"]);
												if($row['V_FLAG_INDEFINIDO']=='1') {
													$ls_indefinido = 'SI';
												}else{
													$ls_indefinido = 'NO';
												}
												?>
												<tr>
													<?php

													echo "<td>";
													echo f_r_url($url_editar_fila, $row["N_COD_TRASLADO"],$row["N_COD_TRASLADO"]);
													echo "</td>";
													
													// Datos Estudiante
													$array_campo_pk	= array('N_COD_ESTUDIANTE');
													$array_valor_pk	= array($row["N_COD_ESTUDIANTE"]);
													$array_estudiante = $crud->fila_recuperar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk);
													echo "<td>";
													echo $array_estudiante['V_APE_PATERNO'].' '.$array_estudiante['V_APE_MATERNO'].' '.$array_estudiante['V_NOMBRES'];
													echo "</td>";

													echo "<td>";
													echo $row["D_FEC_INICIO"];
													echo "</td>";

													echo "<td>";
													echo $row["D_FEC_FIN"];
													echo "</td>";
													
													echo "<td>";													
													echo $ls_indefinido;
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
													
													echo "<td>";
														$ls_estado =  $row["V_FLAG_ESTADO"];
														if ($ls_estado =='0') $estado = 'Rechazado';
														if ($ls_estado =='1') $estado = 'Registrado';
														if ($ls_estado =='2') $estado = 'Aprobado';
														echo $estado;
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
	$url_lista		= "mov_traslado_lista.php";
	$url_registrar	= "../controlador/mov_traslado_registrar.php";
	$url_actualizar	= "../controlador/mov_traslado_actualizar.php";
	$url_eliminar	= "../controlador/mov_traslado_eliminar.php";
	$ld_fecha 			= date('Y-m-d');
	
	// Según caso
	if($lb_edit) {
		$ls_modo = DEF_MSG_FORM_EDICION;
		// Datos Estudiante
		$li_cod_estudiante	= $array["N_COD_ESTUDIANTE"];
		$array_campo_pk		= array('N_COD_ESTUDIANTE');
		$array_valor_pk		= array($li_cod_estudiante);
		$row_est			= $crud->fila_recuperar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk);
		$ls_nombres 		= $row_est['V_APE_PATERNO'].' '.$row_est['V_APE_MATERNO'].' '.$row_est['V_NOMBRES'];
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
												title = "Generado por el sistema" placeholder="Codigo autogenerado" value="<?php echo $lb_edit?$array["N_COD_TRASLADO"]:""; ?>">
										</div>
									</div>
									
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fec_inicio">Fecha Inicio</label>
											<input type="date" class="form-control input-sm" id="id_fec_inicio" name="id_fec_inicio" required"
												title = "Formato Fecha" value="<?php echo $lb_edit?$array["D_FEC_INICIO"]:$ld_fecha; ?>"> 
										</div>	
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fec_fin">Fecha Término</label>
											<input type="date" class="form-control input-sm" id="id_fec_fin" name="id_fec_fin" required"
												title = "Formato Fecha" value="<?php echo $lb_edit?$array["D_FEC_FIN"]:$ld_fecha; ?>"> 
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
												<button id="btn_lista_estudiante" class="btn btn-primary" type="button" data-toggle="modal" 
												<?php echo $lb_edit?'disabled':""; ?>
												data-target="#buscarEstudianteModal"> 
												Lista de Estudiantes &nbsp; <span class="fa fa-address-book-o icon"> </span>
												</button>
											</span>
											<input type="hidden" class="form-control" id="id_cod_estudiante" name="id_cod_estudiante" required 
												 value="<?php echo $lb_edit?$array["N_COD_ESTUDIANTE"]:""; ?>"> 
										</div>
									</div>
									
									<div class="col-sm-4">
										<div class="checkbox">
											<?php
											if($lb_edit) {
												$ls_indefinido = $array['V_FLAG_INDEFINIDO'];
											}else{
												$ls_indefinido = '0';
											}
											?>
											<label>
											<input type="checkbox" id="id_flag_indefinido" name="id_flag_indefinido" <?php if("0" <> $ls_indefinido) echo "checked";?> value="1"> Indefinido ?
											</label>
										</div>
									</div>
									
								</div>

								<div class="row">
									
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_sede_origen">Sede Origen</label>
											<select class="form-control" id="id_cod_sede_origen" name="id_cod_sede_origen" disabled>
												<?php												
												// Recuperando datos para combo
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_tipo_origen = $crud->fila_listar(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE', 'A', 0, 100);

												// Mostrando combo
												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_tipo_origen = mysqli_fetch_assoc($array_tipo_origen)) {
													echo "<option value=\"";
													echo $this_tipo_origen["N_COD_SEDE"];
													echo "\"";
													// Si existen registros, ponerlo en el combo
													if ($lb_edit && $this_tipo_origen["N_COD_SEDE"] == $array["N_COD_SEDE_ORIGEN"]){
														echo " selected";
													}
													echo ">";
													echo $this_tipo_origen["V_NOMBRE"];
													echo "\n";
												}
												?>
											</select>
												 
											<input type="hidden" class="form-control" id="id_cod_sede_origen_hide" name="id_cod_sede_origen_hide" required 
												 value="<?php echo $lb_edit?$array["N_COD_SEDE_ORIGEN"]:""; ?>">
										</div>
									</div>
									
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_sede_destino">Sede Destino</label>
											<select class="form-control" id="id_cod_sede_destino" name="id_cod_sede_destino" required
											<?php echo $lb_edit?'disabled':""; ?> >
												<?php												
												// Recuperando datos para combo
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_tipo_destino = $crud->fila_listar(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE', 'A', 0, 100);

												// Mostrando combo
												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_tipo_destino = mysqli_fetch_assoc($array_tipo_destino)) {
													echo "<option value=\"";
													echo $this_tipo_destino["N_COD_SEDE"];
													echo "\"";
													// Si existen registros, ponerlo en el combo
													if ($lb_edit && $this_tipo_destino["N_COD_SEDE"] == $array["N_COD_SEDE_DESTINO"]){
														echo " selected";
													}
													echo ">";
													echo $this_tipo_destino["V_NOMBRE"];
													echo "\n";
												}
												?>
											</select>
										</div>
									</div>
																		
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_flag_estado">Estado</label>
											<?php
											if($lb_edit) {
												$ls_estado = $array['V_FLAG_ESTADO'];
											}else{
												$ls_estado = '1';
											}
											?>
											<select class="form-control" id="id_flag_estado" name="id_flag_estado" disabled>
											<option value='' selected disabled hidden>Seleccione opción</option>
											<option value="0"<?php if("0" == $ls_estado) echo "selected";?>>Rechazado</option>
											<option value="1"<?php if("1" == $ls_estado) echo "selected";?>>Registrado</option>
											<option value="2"<?php if("2" == $ls_estado) echo "selected";?>>Aprobado</option>
											</select>			
										</div>
									</div>
									
								</div>
								
								<div class="form-group">
									<label for="id_observacion">Observaciones</label>
									<textarea class="form-control" rows="3" id="id_observacion" name="id_observacion" placeholder="Ejemplo : Traslado temporal por vacaciones.."><?php echo $lb_edit?$array["V_OBSERVACION"]:""; ?></textarea>
								</div>

								<?php
								// Armar Estructura
								$array_campo_pk = ['V_FLAG_ESTADO','V_TIPO_EST'];
								$array_valor_pk = ['1', 'EST_INT'];

								// Padrón de Estudiantes
								$array_padron = $crud->fila_listar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk,'V_APE_PATERNO, V_APE_MATERNO, V_NOMBRES', 'A', 0, 9999);								
								?>

								<div id="buscarEstudianteModal" class="modal fade">
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
																	<th>Sede Actual</th>
																</tr>
															</thead>

															<tbody>
																<?php
																$li_contador = 0;
																while ($row_padron = mysqli_fetch_assoc($array_padron)) {
																$li_contador = $li_contador + 1;
																?>
																<tr class = "tr_listaDetalle" data-id="<?php echo $row_padron["N_COD_ESTUDIANTE"];?>" 
																							  id="<?php echo $row_padron["V_APE_PATERNO"].' '.$row_padron["V_APE_MATERNO"].' '.$row_padron["V_NOMBRES"];?>"
																							  data-sedeact="<?php echo $row_padron["N_COD_SEDE"];?>"
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
																																
																	// Sede Actual
																	$array_campo_pk	= array('N_COD_SEDE');
																	$array_valor_pk	= array($row_padron["N_COD_SEDE"]);
																	$ls_sedeActual	= $crud->fila_recuperar_campo(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE');
																	echo "<td>";
																	echo $ls_sedeActual;
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
									echo "<input type=hidden name=id_codigo value=\"".$array["N_COD_TRASLADO"]."\">";
									?>
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
		let myRow_SedeAct	  = 0; // Sede Actual

		// Eventos
		mybtn_sel_aceptar.addEventListener("click", f_js_seleccionar);			

		// Evento Fila TR
		$(".tr_listaDetalle" ).click(function(e) {	
			e.preventDefault();
			myRow_Codigo 	= $(this).attr("data-id");
			myRow_Data 		= $(this).attr("id");
			myRow_SedeAct 	= $(this).attr("data-sedeact")
		});

		// Función de selección de registro
		function f_js_seleccionar(){
			
			// Setear datos en formulario origen
			$('#id_cod_estudiante').val(myRow_Codigo);
			$('#id_nombres').val(myRow_Data);
			$('#id_cod_sede_origen').val(myRow_SedeAct);			
			$('#id_cod_sede_origen_hide').val(myRow_SedeAct);	
			
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

// Lista de pendientes de aprobación
function f_listado_aprob($as_titulo, $as_icono, $as_msgRpta){

	// Enlaces
	$url_lista		= "mov_traslado_aprob_lista.php";
	$url_aceptar	= "../controlador/mov_traslado_aceptar.php";
	$url_rechazar	= "../controlador/mov_traslado_rechazar.php";

	// Instanciar clase
	$crud = new crud();

	// Armar Estructura
	$array_campo_pk = [];
	$array_valor_pk = [];

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_TRASLADO, $array_campo_pk, $array_valor_pk, 'N_COD_TRASLADO', 'D', 0, 99999);

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
												<th>Código</th>
												<th>Estudiante</th>
												<th>Fecha Inicio</th>
												<th>Fecha Fin</th>
												<th>Indefinido</th>
												<th>Sede Origen</th>
												<th>Sede Destino</th>
												<th>Estado</th>
												<th>Acciones</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$url_aceptar_fila   = $url_aceptar."?id_codigo=".($row["N_COD_TRASLADO"]);
												$url_rechazar_fila   = $url_rechazar."?id_codigo=".($row["N_COD_TRASLADO"]);
												if($row['V_FLAG_INDEFINIDO']=='1') {
													$ls_indefinido = 'SI';
												}else{
													$ls_indefinido = 'NO';
												}
												?>
												<tr>
													<?php

													echo "<td>";
													echo $row["N_COD_TRASLADO"];
													echo "</td>";
													
													// Datos Estudiante
													$array_campo_pk	= array('N_COD_ESTUDIANTE');
													$array_valor_pk	= array($row["N_COD_ESTUDIANTE"]);
													$array_estudiante = $crud->fila_recuperar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk);
													echo "<td>";
													echo $array_estudiante['V_APE_PATERNO'].' '.$array_estudiante['V_APE_MATERNO'].' '.$array_estudiante['V_NOMBRES'];
													echo "</td>";

													echo "<td>";
													echo $row["D_FEC_INICIO"];
													echo "</td>";

													echo "<td>";
													echo $row["D_FEC_FIN"];
													echo "</td>";
													
													echo "<td>";
													echo $ls_indefinido;
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
													
													echo "<td>";
														$ls_estado =  $row["V_FLAG_ESTADO"];
														if ($ls_estado =='0') $estado = 'Rechazado';
														if ($ls_estado =='1') $estado = 'Registrado';
														if ($ls_estado =='2') $estado = 'Aprobado';
														echo $estado;
													echo "</td>";
													
													echo "<td align='center'>";
													?>
													<a href="#aceptarModal<?php echo $row["N_COD_TRASLADO"]; ?>" data-toggle="modal" ><i class="fa fa-thumbs-o-up" title = "Aceptar Traslado"></i></a>
													&nbsp;&nbsp;
													<a href="#rechazarModal<?php echo $row["N_COD_TRASLADO"]; ?>" data-toggle="modal" ><i class="fa fa-thumbs-o-down" title = "Rechazar Traslado"></i></a>
													<?php
													echo "</td>";
													
													?>
													
													<div id="aceptarModal<?php echo $row["N_COD_TRASLADO"]; ?>" class="modal fade">
														<div class="modal-dialog">
															<div class="modal-content">
																<div class="modal-header">
																	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
																	<h4 class="modal-title">Aviso de Confirmación</h4>
																</div>
																<div class="modal-body">
																	<p>¿ Seguro que quieres aceptar traslado ?</p>
																</div>
																<div class="modal-footer">
																	<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
																	<a class="btn btn-danger" href="<?php echo $url_aceptar_fila?>" role="button">
																		Aceptar
																	</a>
																</div>
															</div>
														</div>
													</div>	
													
													<div id="rechazarModal<?php echo $row["N_COD_TRASLADO"]; ?>" class="modal fade">
														<div class="modal-dialog">
															<div class="modal-content">
																<div class="modal-header">
																	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
																	<h4 class="modal-title">Aviso de Confirmación</h4>
																</div>
																<div class="modal-body">
																	<p>¿ Seguro que quieres rechazar traslado ?</p>
																</div>
																<div class="modal-footer">
																	<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
																	<a class="btn btn-danger" href="<?php echo $url_rechazar_fila?>" role="button">
																		Rechazar
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