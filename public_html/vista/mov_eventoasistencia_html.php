<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Usuarios (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta){

	// Enlaces
	$url_editar		= "mov_eventoejec_editar.php";
	$url_lista		= "mov_eventoasistencia_lista.php";

	// Instanciar clase
	$crud = new crud();

	// Armar Estructura
	$array_campo_pk = ['V_FLAG_ESTADO'];
	$array_valor_pk = ['1'];

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_EVENTO, $array_campo_pk, $array_valor_pk, 'D_FECHA', 'D', 0, 100);

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
												<th>Nº de Invitados</th>
												<th>Nº de Asistentes</th>
												<th>Ver Detalle</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$url_editar_fila   = $url_editar."?id_codigo=".($row["N_COD_EVENTO"]);
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
														// Cantidad 
														$array_campo_pk	= array('N_COD_EVENTO', 'V_FLAG_PROG', 'V_FLAG_EJEC');
														$array_valor_pk	= array($row["N_COD_EVENTO"], '1', '1');
														$li_cantidad	= $crud->fila_contar(DEF_TABLA_EVENTOPROG, $array_campo_pk, $array_valor_pk);
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

// Formulario de Asistentes
function f_formulario_ejec($as_titulo, $as_icono, $as_msgRpta, $id_codigo_padre = "") {

	//Inicalizando variables
	$inhabilitado = "disabled='disabled'";

	// Instanciar clase
	$crud = new crud();

	// Definir Estructura
	$array_campo_pk = array('N_COD_EVENTO', 'V_FLAG_PROG');
	$array_valor_pk = array($id_codigo_padre, '1');

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_EVENTOPROG, $array_campo_pk, $array_valor_pk, 'N_COD_ESTUDIANTE', 'A', 0, 1000);

	// Datos Padre
	$array_campo_pk = array('N_COD_EVENTO');
	$array_valor_pk = array($id_codigo_padre);
	$ls_subtitulo	= $crud->fila_recuperar_campo(DEF_TABLA_EVENTO, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');

	// Enlaces
	$url_lista		= "mov_eventoasistencia_lista.php";
	$ls_modo 		= DEF_MSG_FORM_SELECCION;

	// Formulario HTML
	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<!-- Cabecera de Sección Contenido -->
		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo.' - Registro';?>
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
						url:   '../controlador/mov_eventoejec_actualizar.php',
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