<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Usuarios (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta){

	// Enlaces
	$url_editar		= "mae_sedeespecialista_editar.php";
	$url_lista		= "mae_sedeespecialista_lista.php";

	// Instanciar clase
	$crud = new crud();

	// Definir Estructura
	$array_campo_pk = array('V_FLAG_ESTADO');
	$array_valor_pk = array('1');

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE', 'A', 0, 100);

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
												<th>Código</th>
												<th>Nombre</th>
												<th>Dirección</th>
												<th>Móvil</th>
												<th>Nº de Especialistas</th>
												<th>Ver Detalle</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$url_editar_fila   = $url_editar."?id_codigo=".($row["N_COD_SEDE"]);
												?>
												<tr>
													<?php
													echo "<td>";
													echo $row["N_COD_SEDE"];
													echo "</td>";

													echo "<td>";
													echo $row["V_NOMBRE"];
													echo "</td>";

													echo "<td>";
													echo $row["V_DIRECCION"];
													echo "</td>";

													echo "<td>";
													echo $row["V_MOVIL"];
													echo "</td>";

													echo "<td>";
														// Contador
														$array_campo_pk	= array('N_COD_SEDE', 'V_FLAG_ESTADO');
														$array_valor_pk	= array($row["N_COD_SEDE"], '1');
														$li_cantidad	= $crud->fila_contar(DEF_TABLA_SEDEESPECIALISTA, $array_campo_pk, $array_valor_pk);
														echo $li_cantidad;
													echo "</td>";

													echo "<td align='center'>";
													?>
													<a href="<?php echo $url_editar_fila?>">
														<i class="fa fa-search"></i>
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

// Formulario de Registro
function f_formulario($as_titulo, $as_icono, $as_msgRpta, $id = "") {

	//Inicalizando variables
	$inhabilitado = "disabled='disabled'";

	// Instanciar clase
	$crud = new crud();

	// Definir Estructura
	$array_campo_pk = array('N_COD_SEDE');
	$array_valor_pk = array($id);

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_SEDEESPECIALISTA, $array_campo_pk, $array_valor_pk, 'N_COD_ESPECIALISTA', 'A', 0, 100);

	// Datos Padre
	$array_campo_pk	= array('N_COD_SEDE');
	$array_valor_pk	= array($id);
	$ls_subtitulo	= $crud->fila_recuperar_campo('MAE_SEDE', $array_campo_pk, $array_valor_pk, 'V_NOMBRE');

	// Enlaces
	$url_lista		= "mae_sedeespecialista_lista.php";
	$ls_modo 		= DEF_MSG_FORM_SELECCION;

	// Formulario HTML
	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<!-- Cabecera de Sección Contenido -->
		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo;?>				
			</h1>
			</br>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item active" aria-current="page"><b>Sede : </b><?php echo $ls_subtitulo;?></li>
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
												<th>Nombre Especialista</th>
												<th>Especialidad</th>
											</tr>
										</thead>

										<tbody id='detalle'>
											<?php
											$li_check = 0;
											while ($row = mysqli_fetch_assoc($array)) {
												?>
												<tr>
													
													<td>
														<div class="checkbox">
															<label>
															<input type="checkbox" data-idRegistro = "<?php echo $row["N_COD_ESPECIALISTA"];?>" name="id_flag_estado" <?php if("0" <> $row["V_FLAG_ESTADO"]) echo "checked";?> value="<?php echo $li_check; ?>">
															</label>
														</div>
													</td>

													<?php
													echo "<input type=hidden id = id_codigo_padre name=id_codigo_padre value=\"".$id."\">";
													
													// Datos Especialista
													$array_campo_pk	= array('N_COD_ESPECIALISTA');
													$array_valor_pk	= array($row["N_COD_ESPECIALISTA"]);
													$ls_apepaterno	= $crud->fila_recuperar_campo('MAE_ESPECIALISTA', $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO');
													$ls_apematerno	= $crud->fila_recuperar_campo('MAE_ESPECIALISTA', $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');
													$ls_nombres		= $crud->fila_recuperar_campo('MAE_ESPECIALISTA', $array_campo_pk, $array_valor_pk, 'V_NOMBRES');
													$ls_datos		= $ls_apepaterno.' '.$ls_apematerno.' '.$ls_nombres;
													$ls_cod_especialidad= $crud->fila_recuperar_campo('MAE_ESPECIALISTA', $array_campo_pk, $array_valor_pk, 'N_COD_ESPECIALIDAD');
													echo "<td>";
													echo $ls_datos;
													echo "</td>";

													// Datos Especialidad
													$array_campo_pk	= array('N_COD_ESPECIALIDAD');
													$array_valor_pk	= array($ls_cod_especialidad);
													$ls_grado		= $crud->fila_recuperar_campo('MAE_ESPECIALIDAD', $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_grado;
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
						url:   '../controlador/mae_sedeespecialista_actualizar.php',
						type:  'post',
						beforeSend: function () {
							$('#span_rpta').text(''); // Limpieza
							console.log("Procesando, espere por favor...");
						},
						success:  function (response) {
							if(response == '0'){
								$('#span_rpta').text('Estado : Acción completada(0) con éxito...');
								
							}else{
								$('#span_rpta').text('Estado : Acción completada(1) con éxito...');
							}							
						}
				});
			});		
		});
	</script>	

	<?php
}

?>