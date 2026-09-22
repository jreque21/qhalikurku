<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Usuarios (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta){

	// Enlaces
	$url_nuevo		= "mae_usuario_nuevo.php";
	$url_editar		= "mae_usuario_editar.php";
	$url_lista		= "mae_usuario_lista.php";
	$url_eliminar	= "../controlador/mae_usuario_eliminar.php";

	// Instanciar clase
	$crud = new crud();

	// Armar Estructura
	$array_campo_pk = [];
	$array_valor_pk = [];

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_USUARIO, $array_campo_pk, $array_valor_pk, 'V_NOMBRES', 'A', 0, 99999);

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
												<th>Nombres</th>
												<th>Email</th>
												<th>Tipo Usuario</th>
												<th>Fecha Alta</th>
												<th>Estado</th>
												<th>Editar</th>
												<th>Eliminar</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$url_editar_fila   = $url_editar."?id_codigo=".($row["V_COD_USER"]);
												$url_eliminar_fila = $url_eliminar."?id_codigo=".($row["V_COD_USER"]);
												?>
												<tr>
													<?php
													echo "<td>";
													echo f_r_url($url_editar_fila, $row["V_COD_USER"],$row["V_NOMBRES"]);
													echo "</td>";

													echo "<td>";
													echo $row["V_NOMBRES"];
													echo "</td>";

													echo "<td>";
													echo $row["V_EMAIL"];
													echo "</td>";

													// Tipo Usuario
													$array_campo_pk	= array('V_COD_TIPO');
													$array_valor_pk	= array($row["V_COD_TIPO"]);
													$ls_tipouser	= $crud->fila_recuperar_campo('MAE_TIPO_USUARIO', $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');

													echo "<td>";
													echo $ls_tipouser;
													echo "</td>";

													echo "<td>";
													echo $row["D_FEC_ALTA"];
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
														<i class="fa fa-edit"></i>
													</a>
													<?php
													echo "</td>";

													echo "<td align='center'>";
													?>
													<a href="#deleteModal<?php echo $row["V_COD_USER"]; ?>" data-toggle="modal" ><i class="fa fa-trash-o"></i></a>
													<div id="deleteModal<?php echo $row["V_COD_USER"]; ?>" class="modal fade">
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
	$url_lista		= "mae_usuario_lista.php";
	$url_registrar	= "../controlador/mae_usuario_registrar.php";
	$url_actualizar	= "../controlador/mae_usuario_actualizar.php";
	$url_eliminar	= "../controlador/mae_usuario_eliminar.php";

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
									<div class="col-sm-8">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label for="id_codigo">Código</label>
													<input type="text" class="form-control" id="id_codigo" name="id_codigo" maxlength="8" required pattern="[A-Za-z0-9]{1,8}" <?php if(!$lb_edit) echo "autofocus";?>
														title = "Letras y Números. Tamaño mínimo: 1. Tamaño máximo: 10" placeholder="(*) Ejemplo : jperez" value="<?php echo $lb_edit?$array["V_COD_USER"]:""; ?>" 
														<?php if($lb_edit) echo "disabled";?>>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label for="id_fec_alta">Fecha de Alta</label>
													<input type="date" class="form-control input-sm" id="id_fec_alta" name="id_fec_alta" required"
														title = "Formato Fecha" value="<?php echo $lb_edit?$array["D_FEC_ALTA"]:""; ?>"> 
												</div>										
											</div>
										</div>
										<div class="row">
											<div class="col-sm-12">
												<div class="form-group">
													<label for="id_nombres">Nombres</label>
													<input type="text" class="form-control" id="id_nombres" name="id_nombres" maxlength="150" required pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s0-9 ]{1,150}"
														title = "Letras y Números. Tamaño máximo: 150" placeholder="(*) Ejemplo : Johnny Reque" value="<?php echo $lb_edit?$array["V_NOMBRES"]:""; ?>"> 
												</div>
											</div>
										</div>	
										<div class="row">
											<div class="col-sm-12">
												<div class="form-group">
												<label for="id_email">Email</label>
												<input type="email" class="form-control" id="id_email" name="id_email" maxlength="60" pattern="[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*@[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{1,5}"
												title = "Letras, Números y carácteres de email. Tamaño máximo: 60" placeholder="Ejemplo : jperez@empresa.com" value="<?php echo $lb_edit?$array["V_EMAIL"]:""; ?>">
												</div>
											</div>
										</div>	
									</div>	
									<div class="col-sm-4 text-center">
										<div class="form-group">			
											<?php if($lb_edit){
												if(strlen($array["V_FOTO"])>1){
												?>	
												</br>
												<img class="img-responsive img-thumbnail" src="../../upload/<?php echo DEF_UPLOAD_USUARIO_DIR.'/'.$array["V_FOTO"];?>" width="160px">
												<?php
												}	
											}?>
										</div>
									</div>
								</div>	
								
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_clave">Clave</label>
											<input type="password" class="form-control" id="id_clave" name="id_clave" maxlength="60" 
											title = "Tamaño máximo: 100" placeholder="**********" value="<?php echo $lb_edit?$array["V_CLAVE"]:""; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fec_baja">Fecha de Baja</label>
											<input type="date" class="form-control input-sm" id="id_fec_baja" name="id_fec_baja" "
												title = "Formato Fecha" value="<?php echo $lb_edit?$array["D_FEC_BAJA"]:""; ?>"> 
										</div>										
									</div>
									<div class="col-sm-4">											
										<div class="form-group">
											<label for="id_cod_tipo">Tipo de Usuario</label>
											<select class="form-control" id="id_cod_tipo" name="id_cod_tipo" required>
												<?php
												
												// Recuperando datos para combo
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_tipo     = $crud->fila_listar(DEF_TABLA_TIPOUSUARIO, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA', 'A', 0, 100);

												// Mostrando combo
												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_tipo = mysqli_fetch_assoc($array_tipo)) {
													echo "<option value=\"";
													echo $this_tipo["V_COD_TIPO"];
													echo "\"";
													// Si existen registros, ponerlo en el combo
													if ($lb_edit && $this_tipo["V_COD_TIPO"] == $array["V_COD_TIPO"]){
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

								<div class="form-group">
									<label for="id_observacion">Observacion</label>
									<div id="id_observacion">										
										<textarea class="form-control" rows="3" id="id_observacion" name="id_observacion" placeholder="Introduce resumen"><?php echo $lb_edit?$array["V_OBSERVACION"]:""; ?></textarea>
									</div>
								</div>
								
								<div class="form-group">
									<label for="id_imagen">Adjuntar una Imagen</label>
									<input type="file" id="archivo" name="archivo">	
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
									echo "<input type=hidden name=id_codigo value=\"".$array["V_COD_USER"]."\">";
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

?>