<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista (Cuerpo - Drcha)
function f_listado_horarios($as_titulo, $as_icono, $as_msgRpta){

	// Enlaces
	$url_detalle	= "mov_asistclases_lista.php";
	$url_lista		= "mov_asisthorario_lista.php";
	
	// Instanciar clase
	$crud = new crud();

	// Datos Instructor
	$li_cod_instructor  = $_SESSION['N_COD_REFERENCIA'];

	// Armar Estructura
	$ls_condicion = "MOV_HORARIO H WHERE H.V_FLAG_ESTADO = '2' AND (H.N_COD_INSTRUCTOR = ".$li_cod_instructor." OR (0 < (SELECT COUNT(1) FROM MOV_HORARIO_AYUDANTE T WHERE T.N_COD_HORARIO = H.N_COD_HORARIO AND T.V_FLAG_ESTADO = '1' AND T.N_COD_INSTRUCTOR = ".$li_cod_instructor.") ) )";

	// Listado
	$array = $crud->fila_listar_solocondicion($ls_condicion, 'D_FEC_INICIO', 'D', 0, 1000);
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
					Horarios Aperturados
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
												<th>Horario</th>
												<th>Sede</th>
												<th>Instructor</th>
												<th>Fecha Inicio</th>
												<th>Turno</th>
												<th>Hora</th>
												<th>Categoría</th>
												<th>Estado</th>
												<th>Ver Detalle</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$url_detalle_fila    = $url_detalle."?id_codigo_padre=".($row["N_COD_HORARIO"]);
												?>
												<tr>
													<?php
													echo "<td>";
													echo $row["V_DESCRIPCION"];
													echo "</td>";

													// Sede
													$array_campo_pk	= array('N_COD_SEDE');
													$array_valor_pk	= array($row["N_COD_SEDE"]);
													$ls_sede		= $crud->fila_recuperar_campo(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE');
													echo "<td>";
													echo $ls_sede;
													echo "</td>";

													// Instructor
													$array_campo_pk	= array('N_COD_INSTRUCTOR');
													$array_valor_pk	= array($row["N_COD_INSTRUCTOR"]);
													$ls_instructor_ape_p	= $crud->fila_recuperar_campo(DEF_TABLA_INSTRUCTOR, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO');
													$ls_instructor_ape_m	= $crud->fila_recuperar_campo(DEF_TABLA_INSTRUCTOR, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');
													$ls_instructor_nombres	= $crud->fila_recuperar_campo(DEF_TABLA_INSTRUCTOR, $array_campo_pk, $array_valor_pk, 'V_NOMBRES');
													echo "<td>";
													echo $ls_instructor_ape_p.' '.$ls_instructor_ape_m.' '.$ls_instructor_nombres;
													echo "</td>";

													echo "<td>";
													echo $row["D_FEC_INICIO"];
													echo "</td>";

													// Turno
													$array_campo_pk	= array('N_COD_CATURNO');
													$array_valor_pk	= array($row["N_COD_CATURNO"]);
													$ls_turno		= $crud->fila_recuperar_campo(DEF_TABLA_CATTURNO, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_turno;
													echo "</td>";

													echo "<td>";
													echo substr($row["D_HORA_INICIO"],0,5);
													//echo date("H:i");
													echo "</td>";
													
													// Categoria Estudiante
													$array_campo_pk	= array('N_COD_CATESTUDIANTE');
													$array_valor_pk	= array($row["N_COD_CATESTUDIANTE"]);
													$ls_catestudiante= $crud->fila_recuperar_campo(DEF_TABLA_CATESTUDIANTE, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_catestudiante;
													echo "</td>";

													echo "<td>";
														$ls_estado =  $row["V_FLAG_ESTADO"];
														if ($ls_estado =='0') $ls_des_estado = 'Cancelado';
														if ($ls_estado =='1') $ls_des_estado = 'Registrado';
														if ($ls_estado =='2') $ls_des_estado = 'Aperturado';
														if ($ls_estado =='3') $ls_des_estado = 'Cerrado';
														echo $ls_des_estado;
													echo "</td>";

													echo "<td align='center'>";
													?>
													<a href="<?php echo $url_detalle_fila?>">
													<i class="fa fa-search" title = "Detalle de clases"></i>
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

// Lista (Cuerpo - Drcha)
function f_listado_clases($as_titulo, $as_icono, $as_msgRpta, $an_id_codigo_padre){

	// Enlaces
	$url_detalle	= "mov_asistencia_lista.php?id_codigo_padre=".$an_id_codigo_padre;
	$url_lista		= "mov_asisthorario_lista.php";
	$url_reporte	= "Report/rpt_asistencia_lista.php?id_codigo_padre=".$an_id_codigo_padre;

	// Instanciar clase
	$crud = new crud();	

	// Listado
	$ls_condicion = 'N_COD_HORARIO = '.$an_id_codigo_padre.' AND D_FEC_PROG <= CURDATE()';
	$array = $crud->fila_listar_condicion(DEF_TABLA_HORARIOPROG, $ls_condicion, 'D_FEC_PROG', 'D', 0, 1000);

	// Datos Padre
	$array_campo_pk = ['N_COD_HORARIO'];
	$array_valor_pk = [$an_id_codigo_padre];	
	$ls_horario  = $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');
	$li_cod_sede = $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'N_COD_SEDE');
	
	// Datos Sede
	$array_campo_pk = ['N_COD_SEDE'];
	$array_valor_pk = [$li_cod_sede];
	$ls_sede 		= $crud->fila_recuperar_campo(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE');
	
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
					<li class="breadcrumb-item active" aria-current="page">
						<b>Horario Aperturado : </b><?php echo $ls_horario;?> &nbsp;&nbsp;|&nbsp;&nbsp;
						<b>Sede : </b><?php echo $ls_sede;?> &nbsp;&nbsp;|&nbsp;&nbsp;Clases de Horario Aperturado
					</li>
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
												<th>Nº Clase</th>
												<th>Fecha</th>
												<th>Día</th>
												<th>Hora Clase</th>
												<th>Nº Horas</th>
												<th>Instructor</th>	
												<th>Estudiantes</th>
												<th>Asistentes</th>	
												<th>Faltas</th>
												<th>Reporte</th>
												<th>Asistencia</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$url_detalle_fila   = $url_detalle."&id_codigo=".($row["N_CLASE"]);
												$url_reporte_fila	= $url_reporte."&id_codigo=".($row["N_CLASE"]);
												?>
												<tr>
													<?php
													
													echo "<td>";
													echo $row["N_CLASE"];
													echo "</td>";

													echo "<td>";
													echo $row["D_FEC_PROG"];
													echo "</td>";

													echo "<td>";
													echo f_r_diasemana($row["D_FEC_PROG"]);
													echo "</td>";

													echo "<td>";
													echo substr($row["D_HORA_PROG"],0,5);
													echo "</td>";

													echo "<td>";
													echo $row["N_HORAS"];
													echo "</td>";

													// Instructor
													$array_campo_pk	= array('N_COD_INSTRUCTOR');
													$array_valor_pk	= array($row["N_COD_INSTRUCTOR"]);
													$array_instructor	= $crud->fila_recuperar(DEF_TABLA_INSTRUCTOR, $array_campo_pk, $array_valor_pk);
													echo "<td>";
													echo $array_instructor['V_APE_PATERNO'].' '.$array_instructor['V_APE_MATERNO'].' '.$array_instructor['V_NOMBRES'];
													echo "</td>";
													
													echo "<td>";
														// Cantidad Estudiantes
														$array_campo_pk	= array('N_COD_HORARIO', 'N_CLASE');
														$array_valor_pk	= array($an_id_codigo_padre, $row["N_CLASE"]);
														$li_cantidad	= $crud->fila_contar(DEF_TABLA_ASISTENCIA, $array_campo_pk, $array_valor_pk);
														echo $li_cantidad;
													echo "</td>";

													echo "<td>";
														// Cantidad Asistentes
														$array_campo_pk	= array('N_COD_HORARIO', 'N_CLASE', 'V_FLAG_ESTADO');
														$array_valor_pk	= array($an_id_codigo_padre, $row["N_CLASE"], '1');
														$li_cantidad	= $crud->fila_contar(DEF_TABLA_ASISTENCIA, $array_campo_pk, $array_valor_pk);
														echo $li_cantidad;
													echo "</td>";

													echo "<td>";
														// Cantidad Faltas
														$array_campo_pk	= array('N_COD_HORARIO', 'N_CLASE', 'V_FLAG_ESTADO');
														$array_valor_pk	= array($an_id_codigo_padre, $row["N_CLASE"], '-1');
														$li_cantidad	= $crud->fila_contar(DEF_TABLA_ASISTENCIA, $array_campo_pk, $array_valor_pk);
														echo $li_cantidad;
													echo "</td>";

													echo "<td align='center'>";
													?>		
													<a href="<?php echo $url_reporte_fila?>" target="_blank" >
														<i class="fa fa-file-pdf-o" title = "Ver reporte de asistencia"></i>
													</a>
													<?php
													echo "</td>";

													echo "<td align='center'>";
													?>
													<a href="<?php echo $url_detalle_fila?>">
														<i class="fa fa-search" title = "Ver detalle de asistencia"></i>
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

// Lista (Cuerpo - Drcha)
function f_listado_asistencia($as_titulo, $as_icono, $as_msgRpta, $an_id_codigo_padre, $an_id_codigo){

	// Enlaces
	$url_lista	= "mov_asistclases_lista.php?id_codigo_padre=".$an_id_codigo_padre;

	// Instanciar clase
	$crud = new crud();	

	// Listado
	$array_campo_pk = ['N_COD_HORARIO', 'N_CLASE'];
	$array_valor_pk = [$an_id_codigo_padre, $an_id_codigo];
	$array 			= $crud->fila_listar(DEF_TABLA_ASISTENCIA, $array_campo_pk, $array_valor_pk, 'N_COD_ESTUDIANTE', 'D', 0, 1000);
	$ld_fecha		= $crud->fila_recuperar_campo(DEF_TABLA_HORARIOPROG, $array_campo_pk, $array_valor_pk, 'D_FEC_PROG');
	$lt_hora		= SUBSTR($crud->fila_recuperar_campo(DEF_TABLA_HORARIOPROG, $array_campo_pk, $array_valor_pk, 'D_HORA_PROG'), 0, 5);
	
	// Datos Padre
	$array_campo_pk = ['N_COD_HORARIO'];
	$array_valor_pk = [$an_id_codigo_padre];
	$ls_horario  	= $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');
	$li_cod_sede 	= $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'N_COD_SEDE');
	
	// Datos Sede
	$array_campo_pk = ['N_COD_SEDE'];
	$array_valor_pk = [$li_cod_sede];
	$ls_sede 		= $crud->fila_recuperar_campo(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk, 'V_NOMBRE');

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
					<li class="breadcrumb-item active" aria-current="page">
						<b>Horario Aperturado : </b><?php echo $ls_horario;?> &nbsp;&nbsp;|&nbsp;&nbsp;
						<b>Sede : </b><?php echo $ls_sede;?> &nbsp;&nbsp;|&nbsp;&nbsp;
						<b>Fecha : </b><?php echo $ld_fecha;?> &nbsp;&nbsp;|&nbsp;&nbsp;
						<b>Hora : </b><?php echo $lt_hora;?>
					</li>
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
								<form  id="form_mtto" role="form" method="post" action="" autocomplete="off">
									<div class="table-responsive">

										<table id="lista" class="table table-striped table-bordered table-hover">

											<thead>
												<tr>
													<th>Foto</th>
													<th>Estudiante</th>											
													<th>Marcar Asistencia</th>
													<th>Estado</th>
													<th>Situación</th>
												</tr>
											</thead>

											<tbody id='detalle'>
												<?php
												$li_cuenta = 0;
												while ($row = mysqli_fetch_assoc($array)) {

													// Incrementar Contador
													$li_cuenta = $li_cuenta + 1;

													// Padrón de Estudiante
													$array_campo_pk = ['N_COD_ESTUDIANTE'];
													$array_valor_pk = [$row['N_COD_ESTUDIANTE']];
													$array_padron	= $crud->fila_recuperar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk);
													
													// Datos Estudiante
													$ls_estud_datos = $array_padron['V_APE_PATERNO'].' '.$array_padron['V_APE_MATERNO'].' '.$array_padron['V_NOMBRES'];
													
													// Género
													$ls_fg_sexo =  $array_padron["V_FG_SEXO"];
													if ($ls_fg_sexo =='M') {
														$ls_des_sexo = 'Masculino';
														$ls_foto_default = 'est_masculino.png';
													}
													if ($ls_fg_sexo =='F') {
														$ls_des_sexo = 'Femenino';
														$ls_foto_default = 'est_femenino.png';
													}	
													
													// Edad
													$li_estud_edad  = f_get_edad($array_padron["D_FEC_NACIMIENTO"]);

													// Cinturón Actual
													$li_cinturon_actual = $crud->f_get_cinturonActual($array_padron["N_COD_ESTUDIANTE"]);
													$ls_estud_cinturon	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, array('N_COD_CINTURON'), array($li_cinturon_actual), 'V_DES_CORTA');

													// Foto
													$ls_estud_foto		= $array_padron["V_FOTO"];

													?>
													<tr class = "tr_listaDetalle" data-id="<?php echo $row["N_COD_ESTUDIANTE"];?>" id="<?php echo $row["N_COD_ESTUDIANTE"];?>">
														<?php
														
														echo "<input type=hidden id = id_codigo_horario name=id_codigo_horario value=\"".$an_id_codigo_padre."\">";
														echo "<input type=hidden id = id_codigo_clase name=id_codigo_clase value=\"".$an_id_codigo."\">";
														
														echo "<td align='center'>";
															if(strlen($ls_estud_foto)>1){
															?>	
																<img class="img-responsive img-thumbnail" src="../../upload/<?php echo DEF_UPLOAD_ESTUDIANTE_DIR.'/'.$ls_estud_foto;?>" width="100px">
															<?php
															}else{
															?>	
																<img class="img-responsive img-thumbnail" src="../../upload/<?php echo DEF_UPLOAD_ESTUDIANTE_DIR.'/'.$ls_foto_default;?>" width="100px">
															<?php
															}
														echo "</td>";

														echo "<td>";
														echo '<b>'.$ls_estud_datos.'</b><br>Cinturón : '.$ls_estud_cinturon.'<br> Edad : '.$li_estud_edad.' años'.'<br>Género : '.$ls_des_sexo;
														echo "</td>";													
														
														echo "<td align='center'>";
														?>
														<a id = '1' data-idRegistro = "<?php echo $row["N_COD_ESTUDIANTE"];?>" href="javascript:function()">
														<img src="../recursos/images/marca_asistio.png" width="60px" title ='Si asistió'>
														</a>&nbsp;&nbsp;&nbsp;
														<a id = '-1' data-idRegistro = "<?php echo $row["N_COD_ESTUDIANTE"];?>" href="javascript:function()">
														<img src="../recursos/images/marca_noasistio.png" width="60px" title ='No asistió'>
														</a>
														<?php
														echo "</td>";
														
														echo "<td align = 'center'>";
														
														// Display
														if('1' == $row["V_FLAG_ESTADO"]){
															$ls_display_asistio = 'block';
														}else{
															$ls_display_asistio = 'none';
														}
														if('-1' == $row["V_FLAG_ESTADO"]){
															$ls_display_noasistio = 'block';
														}else{
															$ls_display_noasistio = 'none';
														}
														if('0' == $row["V_FLAG_ESTADO"]){
															$ls_display_pendiente = 'block';
														}else{
															$ls_display_pendiente = 'none';
														}
														?>
														<div id="btnPendiente_<?php echo $row["N_COD_ESTUDIANTE"];?>" style="display: <?php echo $ls_display_pendiente;?>" class="alert alert-info" role="alert">
															S I N &nbsp;&nbsp; M A R C A
														</div> 
														
														<div id="btnAsistio_<?php echo $row["N_COD_ESTUDIANTE"];?>" style="display: <?php echo $ls_display_asistio;?>" class="alert alert-success" role="alert">
															A S I S T I Ó
														</div> 
														<div id="btnNoAsistio_<?php echo $row["N_COD_ESTUDIANTE"];?>" style="display: <?php echo $ls_display_noasistio;?>" class="alert alert-danger" role="alert">
															N O &nbsp;&nbsp; A S I S T I Ó
														</div> 
														<?php
														echo "</td>";
														
														echo "<td>";
														?>
														<select onchange="f_tipoasistencia(<?php echo $row["N_COD_ESTUDIANTE"];?>, this.value);" class="form-control" id="id_cod_tipoasist" name="id_cod_tipoasist" required>
														<?php
														
														// Recuperando datos para combo
														$array_campo_pk	= array('V_FLAG_ESTADO');
														$array_valor_pk	= array('1');
														$array_tipo     = $crud->fila_listar(DEF_TABLA_TIPOASIS, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA', 'A', 0, 100);

														// Mostrando combo
														echo "<option value='' selected disabled hidden>Seleccione opción</option>";
															while ($this_tipo = mysqli_fetch_assoc($array_tipo)) {
																echo "<option value=\"";
																echo $this_tipo["N_COD_TIPOASIST"];
																echo "\"";
																// Si existen registros, ponerlo en el combo
																if ($this_tipo["N_COD_TIPOASIST"] == $row["N_COD_TIPOASIST"]){
																	echo " selected";
																}
																echo ">";
																echo $this_tipo["V_DES_CORTA"];
																echo "\n";
															}
															?>
														</select>
														<?php
														echo "</td>";	

													echo "</tr>";
												}
												?>
											</tbody>

										</table>

									</div>
								</form>
							<?php
							}
						?>
						<!-- Fin Cuerpo -->
						</div>

					</div>
					<!-- Fin Panel -->

					<a class="btn btn-primary" href="<?php echo $url_lista?>" role="button">
						Regresar
					</a>
					&nbsp;&nbsp;&nbsp;<span id="span_rpta"></span>
					
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
			$('body').on('click', '#detalle a', function(event){

				// Capturar Valores
				var id_codigo_horario 	= $('#id_codigo_horario').val()
				var id_codigo_clase		= $('#id_codigo_clase').val()
				var id_codigo_estudiante= $(this).attr('data-idRegistro');
				var id_accion 			= $(this).attr('id');
				var div_btnPendiente	= document.getElementById("btnPendiente_"+id_codigo_estudiante);			
				var div_btnAsistio		= document.getElementById("btnAsistio_"+id_codigo_estudiante);	
				var div_btnNoAsistio	= document.getElementById("btnNoAsistio_"+id_codigo_estudiante);		
				
				// Según botón
				if (id_accion == 1) {
					div_btnPendiente.style.display = "none";  	// Invisible
					div_btnAsistio.style.display   = "block";	// Visible
					div_btnNoAsistio.style.display = "none";	// Invisible
				}else{
					div_btnPendiente.style.display = "none";	// Invisible
					div_btnAsistio.style.display   = "none";	// Invisible
					div_btnNoAsistio.style.display = "block";	// Visible
				}
				var parametros = {
					"id_codigo_horario" : id_codigo_horario,	
					"id_codigo_clase" : id_codigo_clase,
					"id_codigo_estudiante" : id_codigo_estudiante,
					"id_accion" : id_accion
				};
				$.ajax({
						data:  parametros,
						url:   '../controlador/mov_asistencia_actualizar.php',
						type:  'post',
						beforeSend: function () {					
							console.log("Procesando, espere por favor...");
						},
						success:  function (response) {
							//alert('Hola');
							if(response == '0'){
								alert('Ocurrio un problema, vuelva a intentar.');
							}						
						}
				});
			});		
		});

		// Selección de combo
		function f_tipoasistencia(id_codigo_estudiante, id_tipoasist) {
			
			// Capturar Valores
			var id_codigo_horario 	= $('#id_codigo_horario').val()
			var id_codigo_clase		= $('#id_codigo_clase').val()
			
			// Parámetros
			var parametros = {
				"id_codigo_horario" : id_codigo_horario,	
				"id_codigo_clase" : id_codigo_clase,
				"id_codigo_estudiante" : id_codigo_estudiante,
				"id_tipoasist" : id_tipoasist
			};
			//alert(id_codigo_horario+'-'+id_tipoasist)
			$.ajax({
				data:  parametros,
				url:   '../controlador/mov_asistencia_actualizar_sit.php',
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

		}

	</script>	

<?php
}

?>