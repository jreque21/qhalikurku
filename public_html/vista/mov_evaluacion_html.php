<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Lista de Horarios(Cuerpo - Drcha)
function f_listado_horarios($as_titulo, $as_icono, $as_msgRpta){

	// Enlaces
	$url_detalle	= "mov_evaluacion_lista.php";
	
	// Instanciar clase
	$crud = new crud();
    
    // Identificar tipo de Usuario
    $ls_user        = $_SESSION['usr_conectado'];
    $array_campo_pk = ['V_COD_USER']; 
	$array_valor_pk = [$ls_user]; 
    $ls_tipoUser = $crud->fila_recuperar_campo('MAE_USUARIO', $array_campo_pk, $array_valor_pk, 'V_COD_TIPO');
    
    // Según Caso
    if ($ls_tipoUser == 'USER_INST' or $ls_user == 'admweb') {
    	// Preparar Estructura
    	$array_campo_pk = ['V_FLAG_ESTADO']; 
    	$array_valor_pk = ['2']; // Aperturado
        $array = $crud->fila_listar(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'D_FEC_INICIO', 'D', 0, 1000);
    } else {
    	// Armar Condición
    	$ls_condicion = "MOV_HORARIO H WHERE H.V_FLAG_ESTADO = '2' AND EXISTS (SELECT 1 FROM MOV_EVALUACION E WHERE E.N_COD_HORARIO = H.N_COD_HORARIO AND E.V_FLAG_ESTADO = '1' AND E.V_USR_EVAL = $ls_user)";
    	$array = $crud->fila_listar_solocondicion($ls_condicion, 'D_FEC_INICIO', 'D', 0, 1000);
    }

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
													<i class="fa fa-search" title = "Detalle de Evaluaciones"></i>
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

// Lista de EVALUACIONES (Cuerpo - Drcha)
function f_listado($as_titulo, $as_icono, $as_msgRpta, $an_id_codigo_padre){

	// Enlaces
	$url_nuevo		= "mov_evaluacion_nuevo.php?id_codigo_padre=".$an_id_codigo_padre;
	$url_editar		= "mov_evaluacion_editar.php";
	$url_lista		= "mov_evaluacionhorario_lista.php";
	$url_detalle	= "mov_evaluaciondet_lista.php";
	$url_eliminar	= "../controlador/mov_evaluacion_eliminar.php?id_codigo_padre = ".$an_id_codigo_padre;

	// Instanciar clase
	$crud = new crud();
    
    // Identificar tipo de Usuario
    $ls_user        = $_SESSION['usr_conectado'];
    $array_campo_pk = ['V_COD_USER']; 
	$array_valor_pk = [$ls_user]; 
    $ls_tipoUser = $crud->fila_recuperar_campo('MAE_USUARIO', $array_campo_pk, $array_valor_pk, 'V_COD_TIPO');

    // Según Caso
    if ($ls_tipoUser == 'USER_INST' or $ls_user == 'admweb') {
    	// Armar Estructura
    	$array_campo_pk = ['N_COD_HORARIO'];
    	$array_valor_pk = [$an_id_codigo_padre];
    
    	// Listado
    	$array = $crud->fila_listar(DEF_TABLA_EVALUACION, $array_campo_pk, $array_valor_pk, 'N_COD_ESTUDIANTE', 'A', 0, 999);
    } else {
        // Armar Estructura
    	$array_campo_pk = ['N_COD_HORARIO', 'V_USR_EVAL'];
    	$array_valor_pk = [$an_id_codigo_padre, $ls_user];
    
    	// Listado
    	$array = $crud->fila_listar(DEF_TABLA_EVALUACION, $array_campo_pk, $array_valor_pk, 'N_COD_ESTUDIANTE', 'A', 0, 999);
    }
    
	// Datos Padre
	// Armar Estructura
	$array_campo_pk = ['N_COD_HORARIO'];
	$array_valor_pk = [$an_id_codigo_padre];
	$ls_subtitulo	= $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');
	
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
												<th>Nombre del Estudiante</th>
												<th>Edad</th>
												<th>Cinturón</th>
												<th>Fecha</th>
												<th>Nota</th>
												<th>Exámen</th>												
												<th>Estado</th>
												<th>Evaluar</th>
												<th>Editar</th>
											</tr>
										</thead>

										<tbody>
											<?php
											while ($row = mysqli_fetch_assoc($array)) {
												$ls_pago   = '';
												$url_editar_fila   = $url_editar."?id_codigo=".($row["N_COD_EVALUACION"]);
												$url_detalle_fila  = $url_detalle."?id_codigo=".($row["N_COD_EVALUACION"]);
												$url_eliminar_fila = $url_eliminar."&id_codigo=".($row["N_COD_EVALUACION"]);
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
													$array_valor_pk	= array($row["N_COD_CINTURON"]);
													$ls_cinturonActual	= $crud->fila_recuperar_campo(DEF_TABLA_CINTURON, $array_campo_pk, $array_valor_pk, 'V_DES_CORTA');
													echo "<td>";
													echo $ls_cinturonActual;
													echo "</td>";

													echo "<td>";
													echo $row["D_FECHA"];
													echo "</td>";

													echo "<td>";
													echo $row["N_NOTA"];
													echo "</td>";
													
													// Exámen
													$array_campo_pk	= array('N_COD_EXAMEN');
													$array_valor_pk	= array($row["N_COD_EXAMEN"]);
													$ls_examen	= $crud->fila_recuperar_campo(DEF_TABLA_EXAMEN, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');
													echo "<td>";
													echo $ls_examen;
													echo "</td>";

													echo "<td>";
														$ls_estado =  $row["V_FLAG_ESTADO"];
														if ($ls_estado =='0') $estado = 'Inactivo';
														if ($ls_estado =='1') $estado = 'Activo';
														echo $estado;
													echo "</td>";

													echo "<td align='center'>";
													?>
													<a href="<?php echo $url_detalle_fila?>">
														<i class="fa fa-file-text-o" title = "Evaluar"></i>
													</a>
													<?php
													echo "</td>";

													echo "<td align='center'>";
													?>
													<a href="<?php echo $url_editar_fila?>">
														<i class="fa fa-edit" title = "Editar registro"></i>
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

					<a class="btn btn-primary" href="<?php echo $url_nuevo?>" role="button">
						<span class="glyphicon glyphicon-plus"></span>&nbsp;Nueva Evaluación
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
		$array_campo_pk		= array('N_COD_HORARIO');
		$array_valor_pk		= array($li_id_codigo_padre);
		$ls_des_horario		= $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');
		$li_cod_instructor  = $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'N_COD_INSTRUCTOR');
		$li_cod_sede		= $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'N_COD_SEDE');
		$array_campo_pk		= array('N_COD_INSTRUCTOR');
		$array_valor_pk		= array($li_cod_instructor);
		$ls_ape_paterno		= $crud->fila_recuperar_campo(DEF_TABLA_INSTRUCTOR, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO');
		$ls_ape_materno		= $crud->fila_recuperar_campo(DEF_TABLA_INSTRUCTOR, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');
		$ls_nombres			= $crud->fila_recuperar_campo(DEF_TABLA_INSTRUCTOR, $array_campo_pk, $array_valor_pk, 'V_NOMBRES');
		$ls_des_instructor	= $ls_ape_paterno.' '.$ls_ape_materno.' '.$ls_nombres;
		$ld_fecha 			= date('Y-m-d');
	}else{
		$li_id_codigo_padre = $array["N_COD_HORARIO"];
		$array_campo_pk		= array('N_COD_HORARIO');
		$array_valor_pk		= array($li_id_codigo_padre);
		$ls_des_horario		= $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');
		$li_cod_sede		= $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'N_COD_SEDE');

		$array_campo_pk		= array('N_COD_INSTRUCTOR');
		$array_valor_pk		= $array["N_COD_INSTRUCTOR"];
		$ls_ape_paterno		= $crud->fila_recuperar_campo(DEF_TABLA_INSTRUCTOR, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO');
		$ls_ape_materno		= $crud->fila_recuperar_campo(DEF_TABLA_INSTRUCTOR, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');
		$ls_nombres			= $crud->fila_recuperar_campo(DEF_TABLA_INSTRUCTOR, $array_campo_pk, $array_valor_pk, 'V_NOMBRES');
		$ls_des_instructor	= $ls_ape_paterno.' '.$ls_ape_materno.' '.$ls_nombres;

		// Datos Estudiante
		$li_cod_estudiante	= $array["N_COD_ESTUDIANTE"];
		$array_campo_pk	= array('N_COD_ESTUDIANTE');
		$array_valor_pk	= array($li_cod_estudiante);
		$row_est		= $crud->fila_recuperar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk);
		$ls_nombres 	= $row_est['V_APE_PATERNO'].' '.$row_est['V_APE_MATERNO'].' '.$row_est['V_NOMBRES'];
		$li_cod_cinturon = $array["N_COD_CINTURON"];
		
		// Enlaces
		$url_eliminar	= "../controlador/mov_evaluacion_eliminar.php?id_codigo_padre=".$li_id_codigo_padre."&id_codigo=".$array['N_COD_EVALUACION'];
	}

	// Enlaces
	$url_lista		= "mov_evaluacion_lista.php?id_codigo_padre=".$li_id_codigo_padre;
	$url_registrar	= "../controlador/mov_evaluacion_registrar.php";
	$url_actualizar	= "../controlador/mov_evaluacion_actualizar.php";

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
												title = "Generado por el sistema" placeholder="Codigo autogenerado" value="<?php echo $lb_edit?$array["N_COD_EVALUACION"]:""; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_horario">Horario</label>
											<input type="text" class="form-control" id="id_cod_horario" name="id_cod_horario" maxlength="150" required disabled 
												value="<?php echo $ls_des_horario; ?>"> 
											<input type="hidden" class="form-control" id="id_cod_horario_hide" name="id_cod_horario_hide"  
												 value="<?php echo $li_id_codigo_padre; ?>"> 
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_instructor">Instructor</label>
											<input type="text" class="form-control" id="id_cod_instructor" name="id_cod_instructor" maxlength="150" required disabled 
												value="<?php echo $ls_des_instructor; ?>"> 
											<input type="hidden" class="form-control" id="id_cod_instructor_hide" name="id_cod_instructor_hide"  
												 value="<?php echo $li_cod_instructor; ?>"> 
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
												data-target="#buscarEstudianteModal<?php echo $li_id_codigo_padre; ?>"> 
												Lista de Estudiantes &nbsp; <span class="fa fa-address-book-o icon"> </span>
												</button>
											</span>
											<input type="hidden" class="form-control" id="id_cod_estudiante" name="id_cod_estudiante" required 
												 value="<?php echo $lb_edit?$array["N_COD_ESTUDIANTE"]:""; ?>"> 
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_cinturon">Cinturón</label>
											<select class="form-control" id="id_cod_cinturon" name="id_cod_cinturon" disabled>
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
													if ($lb_edit && $this_tipo["N_COD_CINTURON"] == $li_cod_cinturon){
														echo " selected";
													}
													echo ">";
													echo $this_tipo["V_DES_CORTA"];
													echo "\n";
												}
												?>
											</select>
											<input type="hidden" class="form-control" id="id_cod_cinturon_hide" name="id_cod_cinturon_hide" required 
												 value="<?php echo $lb_edit?$array["N_COD_CINTURON"]:""; ?>">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fecha">Fecha</label>
											<input type="date" class="form-control input-sm" id="id_fecha" name="id_fecha" required"
												title = "Formato Fecha" value="<?php echo $lb_edit?$array["D_FECHA"]:$ld_fecha; ?>"> 
										</div>	
									</div>
									
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_examen">Exámen</label>
											<select class="form-control" id="id_cod_examen" name="id_cod_examen" required
											<?php echo $lb_edit?'disabled':""; ?> >
												<?php												
												// Recuperando datos para combo
												$array_campo_pk	= array('V_FLAG_ESTADO');
												$array_valor_pk	= array('1');
												$array_tipo     = $crud->fila_listar(DEF_TABLA_EXAMEN, $array_campo_pk, $array_valor_pk, 'N_COD_EXAMEN', 'D', 0, 100);

												// Mostrando combo
												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_tipo = mysqli_fetch_assoc($array_tipo)) {
													echo "<option value=\"";
													echo $this_tipo["N_COD_EXAMEN"];
													echo "\"";
													// Si existen registros, ponerlo en el combo
													if ($lb_edit && $this_tipo["N_COD_EXAMEN"] == $array["N_COD_EXAMEN"]){
														echo " selected";
													}
													echo ">";
													echo $this_tipo["V_DESCRIPCION"];
													echo "\n";
												}
												?>
											</select>
										</div>
									</div>
									
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_cod_evaluador">Evaluador (Opcional)</label>
											<select class="form-control" id="id_cod_evaluador" name="id_cod_evaluador">
												<?php												
												// Recuperando datos para combo
												$array_campo_pk	= array('V_FLAG_ESTADO','V_COD_TIPO');
												$array_valor_pk	= array('1','USER_EVAL');
												$array_tipo     = $crud->fila_listar(DEF_TABLA_USUARIO, $array_campo_pk, $array_valor_pk, 'V_NOMBRES', 'D', 0, 100);

												// Mostrando combo
												echo "<option value='' selected disabled hidden>Seleccione opción</option>";
												while ($this_tipo = mysqli_fetch_assoc($array_tipo)) {
													echo "<option value=\"";
													echo $this_tipo["V_COD_USER"];
													echo "\"";
													// Si existen registros, ponerlo en el combo
													if ($lb_edit && $this_tipo["V_COD_USER"] == $array["V_USR_EVAL"]){
														echo " selected";
													}
													echo ">";
													echo $this_tipo["V_NOMBRES"];
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
											<label for="id_nota">Nota</label>
											<input type="number" class="form-control" id="id_nota" name="id_nota" maxlength="9" min="0" disabled
											title = "Campo numérico" placeholder="(*) Ejemplo : 70" value="<?php echo $lb_edit?$array["N_NOTA"]:''; ?>">
										</div>
									</div>
									<div class="col-sm-4">
									</div>
									<div class="col-sm-4">
									</div>
									
								</div>
								
								<div class="form-group">
									<label for="id_observacion">Observaciones</label>
									<textarea class="form-control" rows="3" id="id_observacion" name="id_observacion" placeholder="Ejemplo : Mejoró coordinación de brazos.."><?php echo $lb_edit?$array["V_OBSERVACION"]:""; ?></textarea>
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
								// Armar Estructura
								$array_campo_pk = ['V_FLAG_ESTADO','V_TIPO_EST','N_COD_SEDE'];
								$array_valor_pk = ['1', 'EST_INT', $li_cod_sede];

								// Padrón de Estudiantes
								$array_padron = $crud->fila_listar_not_in(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk, DEF_TABLA_EVALUACION, 'N_COD_ESTUDIANTE', 'N_COD_HORARIO', $li_id_codigo_padre,'V_APE_PATERNO, V_APE_MATERNO, V_NOMBRES', 'A', 0, 9999);
								
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
		//let myRow_CintNew	  = 0; // Cinturón New

		// Eventos
		mybtn_sel_aceptar.addEventListener("click", f_js_seleccionar);			

		// Evento Fila TR
		$(".tr_listaDetalle" ).click(function(e) {	
			e.preventDefault();
			myRow_Codigo 	= $(this).attr("data-id");
			myRow_Data 		= $(this).attr("id");
			myRow_CintAct 	= $(this).attr("data-cintact");
			//myRow_CintNew 	= $(this).attr("data-cintnew");
		});

		// Función de selección de registro
		function f_js_seleccionar(){
			
			// Setear datos en formulario origen
			$('#id_cod_estudiante').val(myRow_Codigo);
			$('#id_nombres').val(myRow_Data);
			$('#id_cod_cinturon').val(myRow_CintAct);			
			$('#id_cod_cinturon_hide').val(myRow_CintAct);	
			
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

// Formulario de Evaluación
function f_listado_evaluacion($as_titulo, $as_icono, $as_msgRpta, $id_codigo_padre = "") {

	//Inicalizando variables
	$inhabilitado = "disabled='disabled'";

	// Instanciar clase
	$crud = new crud();

	// Definir Estructura
	$array_campo_pk = array('N_COD_EVALUACION');
	$array_valor_pk = array($id_codigo_padre);

	// Listado
	$array = $crud->fila_listar(DEF_TABLA_EVALUACIONDET, $array_campo_pk, $array_valor_pk, 'N_ITEM', 'A', 0, 99999);

	// Datos Padre
	$li_cod_horario		= $crud->fila_recuperar_campo(DEF_TABLA_EVALUACION, $array_campo_pk, $array_valor_pk, 'N_COD_HORARIO');
	$li_cod_estudiante	= $crud->fila_recuperar_campo(DEF_TABLA_EVALUACION, $array_campo_pk, $array_valor_pk, 'N_COD_ESTUDIANTE');

	// Horario
	$array_campo_pk = array('N_COD_HORARIO');
	$array_valor_pk = array($li_cod_horario);
	$ls_des_horario		= $crud->fila_recuperar_campo(DEF_TABLA_HORARIO, $array_campo_pk, $array_valor_pk, 'V_DESCRIPCION');

	// Estudiante
	$array_campo_pk = array('N_COD_ESTUDIANTE');
	$array_valor_pk = array($li_cod_estudiante);
	$row_padron		= $crud->fila_recuperar(DEF_TABLA_ESTUDIANTE, $array_campo_pk, $array_valor_pk);
	$ls_des_estudiante = $row_padron['V_APE_PATERNO'].' '.$row_padron['V_APE_MATERNO'].' '.$row_padron['V_NOMBRES'];

	// Enlaces
	$url_lista		= "mov_evaluacion_lista.php?id_codigo_padre=".$li_cod_horario;
	$url_actualizar	= "../controlador/mov_evaluaciondet_actualizar.php";
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
					<li class="breadcrumb-item active" aria-current="page"><b>Horario : </b><?php echo $ls_des_horario;?> | <b>Estudiante : </b><?php echo $ls_des_estudiante;?></li>
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
												<th>Item</th>
												<th>Descripción</th>
												<th>Mínimo (%)</th>
												<th>Nota (%)</th>
												<th>Diagnóstico</th>
											</tr>
										</thead>

										<tbody id='detalle'>
											<?php
											$li_check = 0;
											while ($row = mysqli_fetch_assoc($array)) {
												?>
												<tr>
													
													<td><?php echo $row['N_ITEM'];?></td>
													<td><?php echo $row['V_DESCRIPCION'];?></td>
													<td><?php echo $row['N_MINIMO'];?></td>

													<td>															
														<input type="number" name="id_nota[]"  maxlength="3" pattern="[0-9 ]{1,3}" min="0" required value="<?php echo $row["N_NOTA"]; ?>">
													</td>
                                                    
                                                    <td>															
														<input type="text" name="id_observacion[]" value="<?php echo $row["V_OBSERVACION"]; ?>">
													</td>
													
													<?php
													echo "<input type=hidden id = id_codigo_padre name=id_codigo_padre value=\"".$id_codigo_padre."\">";
													echo "<input type=hidden name=id_item[] value=\"".$row["N_ITEM"]."\">";
												echo "</tr>";
												$li_check++;
											}
											?>
										</tbody>

									</table>

								</div>

								<input class="btn btn-success" type="submit" id ="btn_actualizar" value="Guardar" onclick=this.form.action="<?php echo $url_actualizar?>">
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
	
	<?php
}

?>