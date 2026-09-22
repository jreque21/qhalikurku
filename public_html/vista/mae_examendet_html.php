<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Aspectos (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta, $an_id_codigo_padre){

	// Enlaces
	$url_nuevo		= "mae_examendet_nuevo.php?id_codigo_padre=".$an_id_codigo_padre;
	$url_editar		= "mae_examendet_editar.php?id_codigo_padre=".$an_id_codigo_padre;
	$url_lista		= "mae_examen_lista.php";
	$url_eliminar	= "../controlador/mae_examendet_eliminar.php?id_codigo_padre=".$an_id_codigo_padre;

	// Instanciar clase
	$crud = new crud();

	// Armar Estructura
	$array_campo_pk = ['N_COD_EXAMEN'];
	$array_valor_pk = [$an_id_codigo_padre];

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_EXAMENDET, $array_campo_pk, $array_valor_pk, 'N_ITEM', 'A', 0, 999);

	// Datos Padre
	$ls_subtitulo	= $crud->fila_recuperar_campo(DEF_TABLA_EXAMEN, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');
	
	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<!-- Cabecera de Sección Contenido -->
		<section class="content-header">
			<h1>
				<i class="<?php echo $as_icono;?>"></i> <?php echo $as_titulo.' - Aspectos';?>
			</h1>
			</br>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item active" aria-current="page"><b>Exámen Nª : </b><?php echo $ls_subtitulo;?></li>
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
												<th>Item</th>
												<th>Descripción</th>
												<th>Peso</th>
												<th>% Mínimo</th>
												<th>Estado</th>												
												<th>Editar</th>
												<th>Eliminar</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$ls_pago   = '';
												$url_editar_fila   = $url_editar."&id_codigo=".($row["N_ITEM"]);
												$url_eliminar_fila = $url_eliminar."&id_codigo=".($row["N_ITEM"]);
												?>
												<tr>
													<?php

													echo "<td>";
													echo $row["N_ITEM"];
													echo "</td>";

													echo "<td>";
													echo $row["V_DESCRIPCION"];
													echo "</td>";

													echo "<td>";
													echo $row["N_PESO"];
													echo "</td>";

													echo "<td>";
													echo $row["N_MINIMO"];
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
													</a>
													<?php
													echo "</td>";
													
													echo "<td align='center'>";
													?>
													<a href="#deleteModal<?php echo $row["N_ITEM"]; ?>" data-toggle="modal" ><i class="fa fa-trash-o"></i></a>
													<div id="deleteModal<?php echo $row["N_ITEM"]; ?>" class="modal fade">
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
						<span class="glyphicon glyphicon-plus"></span>&nbsp;Agregar Ítem
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
		$array_campo_pk	= array('N_COD_EXAMEN');
		$array_valor_pk	= array($li_id_codigo_padre);
		$li_item		= $crud->fila_recuperar_lastIdPar(DEF_TABLA_EXAMENDET, $array_campo_pk, $array_valor_pk, 'N_ITEM')+1;
	}else{
		$li_id_codigo_padre = $array["N_COD_EXAMEN"];
		$li_item			= $array["N_ITEM"];
		// Enlaces
		$url_eliminar	= "../controlador/mae_examendet_eliminar.php?id_codigo_padre=".$li_id_codigo_padre."&id_codigo=".$array['N_ITEM'];
	}

	// Enlaces
	$url_lista		= "mae_examendet_lista.php?id_codigo_padre=".$li_id_codigo_padre;
	$url_registrar	= "../controlador/mae_examendet_registrar.php";
	$url_actualizar	= "../controlador/mae_examendet_actualizar.php";

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
									<div class="col-sm-12">
										<div class="form-group">
											<label for="id_cod_examen">Exámen</label>
											<select class="form-control" id="id_cod_examen" name="id_cod_examen" required disabled>
												<?php												
												// Recuperando datos para combo
												$array_campo_pk	= array();
												$array_valor_pk	= array();
												$array_tipo     = $crud->fila_listar(DEF_TABLA_EXAMEN, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION', 'A', 0, 100);
												
												// Mostrando combo
												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_tipo = mysqli_fetch_assoc($array_tipo)) {
													echo "<option value=\"";
													echo $this_tipo["N_COD_EXAMEN"];
													echo "\"";
													// Si existen registros, ponerlo en el combo
													if ($this_tipo["N_COD_EXAMEN"] == $li_id_codigo_padre){
														echo " selected";
													}
													echo ">";
													echo $this_tipo["V_DESCRIPCION"];
													echo "\n";
												}
												?>
											</select>
											<input type="hidden" class="form-control" id="id_cod_examen_hide" name="id_cod_examen_hide"  
												 value="<?php echo $li_id_codigo_padre; ?>"> 
											<input type="hidden" class="form-control" id="id_item_hide" name="id_item_hide"  
												 value="<?php echo $li_item; ?>">
										</div>
									</div>
								</div>
								
								<div class="form-group">
									<label for="id_descripcion">Descripción</label>
									<input type="text" class="form-control" id="id_descripcion" name="id_descripcion" maxlength="150" required pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s0-9 ]{1,150}" 
										title = "Letras y Números. Tamaño máximo: 150" placeholder="(*) Ejemplo : Aspectos Técnicos" value="<?php echo $lb_edit?$array["V_DESCRIPCION"]:""; ?>">
								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_peso">Peso (%)</label>
											<input type="number" class="form-control" id="id_peso" name="id_peso" maxlength="9" min="0" required
											title = "Campo numérico" placeholder="(*) Ejemplo : 40" value="<?php echo $lb_edit?$array["N_PESO"]:''; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_minimo">Mínimo (%)</label>
											<input type="number" class="form-control" id="id_minimo" name="id_minimo" maxlength="9" min="0" required
											title = "Campo numérico" placeholder="(*) Ejemplo : 50" value="<?php echo $lb_edit?$array["N_MINIMO"]:''; ?>">
										</div>
									</div>
									<div class="col-sm-4">
									</div>
								</div>
	
								<div class="form-group">
									<label for="id_observacion">Observaciones</label>
									<textarea class="form-control" rows="3" id="id_observacion" name="id_observacion" placeholder="Ejemplo : Aspecto o competencia que evalúa.."><?php echo $lb_edit?$array["V_OBSERVACION"]:""; ?></textarea>
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
									echo "<input type=hidden name=id_codigo value=\"".$array["N_ITEM"]."\">";
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