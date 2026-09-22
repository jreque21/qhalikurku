<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Usuarios (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta){

	// Enlaces
	$url_editar		= "mae_rolusuario_editar.php";
	$url_lista		= "mae_rolusuario_lista.php";

	// Instanciar clase
	$crud = new crud();

	// Definir Estructura
	$array_campo_pk = array('V_FLAG_ESTADO');
	$array_valor_pk = array('1');

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_ROL, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA', 'A', 0, 100);

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
												<th>Descripcion Larga</th>
												<th>Descripcion Corta</th>
												<th># de Usuarios</th>
												<th>Ver Detalle</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$url_editar_fila   = $url_editar."?id_codigo=".($row["V_COD_ROL"]);
												?>
												<tr>
													<?php

													echo "<td>";
													echo $row["V_COD_ROL"];
													echo "</td>";

													echo "<td>";
													echo $row["V_DES_LARGA"];
													echo "</td>";

													echo "<td>";
													echo $row["V_DES_CORTA"];
													echo "</td>";

													echo "<td>";
														// Datos Padre
														$array_campo_pk	= array('V_COD_ROL', 'V_FLAG_ESTADO');
														$array_valor_pk	= array($row["V_COD_ROL"], '1');
														$li_cantidad	= $crud->fila_contar(DEF_TABLA_ROLUSUARIO, $array_campo_pk, $array_valor_pk);
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
	$array_campo_pk = array('V_COD_ROL');
	$array_valor_pk = array($id);

	// Datos Padre
	$ls_subtitulo	= $crud->fila_recuperar_campo(DEF_TABLA_ROL, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
	$ls_tipouser	= $crud->fila_recuperar_campo(DEF_TABLA_ROL, $array_campo_pk, $array_valor_pk, 'V_COD_TIPOUSER');

	// Listado
	$array_campo_pk = array('V_COD_ROL', 'V_COD_TIPOUSER');
	$array_valor_pk = array($id, $ls_tipouser);
	$array = $crud->fila_listar(DEF_TABLA_ROLUSUARIO, $array_campo_pk, $array_valor_pk, 'V_COD_ROL', 'A', 0, 100);	

	// Enlaces
	$url_lista		= "mae_rolusuario_lista.php";
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
					<li class="breadcrumb-item active" aria-current="page"><b>Rol : </b><?php echo $ls_subtitulo;?></li>
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

									<table id="lista" class="table table-striped table-bordered table-hover">

										<thead>
											<tr>
												<th>Marca (S/N)</th>
												<th>Codigo Usuario</th>
												<th>Nombres Usuario</th>
												<th>Tipo de Usuario</th>
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
															<input type="checkbox" data-idRegistro = "<?php echo $row["V_COD_USER"];?>" name="id_flag_estado" <?php if("0" <> $row["V_FLAG_ESTADO"]) echo "checked";?> value="<?php echo $li_check; ?>">
															</label>
														</div>
													</td>

													<?php
													echo "<input type=hidden id = id_codigo_padre name=id_codigo_padre value=\"".$id."\">";
													
													echo "<td>";
													echo $row["V_COD_USER"];
													echo "</td>";

													// Datos Usuario
													$array_campo_pk	= array('V_COD_USER');
													$array_valor_pk	= array($row["V_COD_USER"]);
													$ls_datos		= $crud->fila_recuperar_campo(DEF_TABLA_USUARIO, $array_campo_pk, $array_valor_pk, 'V_NOMBRES');
													echo "<td>";
													echo $ls_datos;
													echo "</td>";

													// Datos Tipo Usuario
													$array_campo_pk	= array('V_COD_TIPO');
													$array_valor_pk	= array($row["V_COD_TIPOUSER"]);
													$ls_tipouser	= $crud->fila_recuperar_campo(DEF_TABLA_TIPOUSUARIO, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_tipouser;
													echo "</td>";

												echo "</tr>";
												$li_check++;
											}
											?>
										</tbody>

									</table>

								</div>
								
								<input class="btn btn-primary" type="submit" id ="btn_cancelar"  value="Cancelar" formnovalidate onclick=this.form.action="<?php echo $url_lista?>">
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
						url:   '../controlador/mae_rolusuario_actualizar.php',
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