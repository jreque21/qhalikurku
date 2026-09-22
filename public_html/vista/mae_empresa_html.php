<?php

// Incluye Librería BD
require_once("../config/funciones.php");

// Formulario de Registro
function f_formulario($as_titulo, $as_icono, $as_msgRpta, $array = "") {

	//Inicalizando variables
	$lb_edit = is_array($array);
	$inhabilitado = "disabled='disabled'";

	// Enlaces
	$url_lista		= DEF_URL_LOGIN;
	$url_actualizar	= "../controlador/mae_empresa_actualizar.php";

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
												title = "Generado por el sistema" placeholder="Codigo autogenerado" value="<?php echo $lb_edit?$array["V_ID"]:""; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_descripcion">Descripción</label>
											<input type="text" class="form-control" id="id_descripcion" name="id_descripcion" maxlength="150" required pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s0-9 ]{1,150}" autofocus
												title = "Letras y Números. Tamaño máximo: 150" placeholder="(*) Escribe nombre de empresa" value="<?php echo $lb_edit?$array["V_DESCRIPCION"]:""; ?>">	 
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_responsable">Responsable</label>
											<input type="text" class="form-control" id="id_responsable" name="id_responsable" maxlength="150" required pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s0-9 ]{1,150}"
												title = "Letras y Números. Tamaño máximo: 150" placeholder="(*) Escribe nombre de responsable" value="<?php echo $lb_edit?$array["V_RESPONSABLE"]:""; ?>">	 
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_direccion">Dirección</label>
											<input type="text" class="form-control" id="id_direccion" name="id_direccion" maxlength="150" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s0-9 ]{1,150}"
												title = "Letras y Números. Tamaño máximo: 150" placeholder="Escribe dirección" value="<?php echo $lb_edit?$array["V_DIRECCION"]:""; ?>">	 
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_referencia">Referencia</label>
											<input type="text" class="form-control" id="id_referencia" name="id_referencia" maxlength="150" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s0-9 ]{1,150}"
												title = "Letras y Números. Tamaño máximo: 150" placeholder="Escribe referencia de dirección" value="<?php echo $lb_edit?$array["V_REFERENCIA"]:""; ?>">	 
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_lema">Lema</label>
											<input type="text" class="form-control" id="id_lema" name="id_lema" maxlength="150" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s0-9 ]{1,150}"
												title = "Letras y Números. Tamaño máximo: 150" placeholder="Escribe lema" value="<?php echo $lb_edit?$array["V_LEMA"]:""; ?>">	 
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_email">Email</label>
											<input type="email" class="form-control" id="id_email" name="id_email" maxlength="80" pattern="[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*@[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{1,5}"
											title = "Letras, Números y carácteres de email. Tamaño máximo: 80" placeholder="Ejemplo : jperez@gmail.com" value="<?php echo $lb_edit?$array["V_EMAIL"]:""; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fono">Fono</label>
											<input type="text" class="form-control" id="id_fono" name="id_fono" maxlength="20" pattern="[0-9 ]{1,20}"
												title = "Letras y Números. Tamaño máximo: 20" placeholder="Ejemplo : 043555555" value="<?php echo $lb_edit?$array["V_FONO"]:""; ?>"> 
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_movil">Movil</label>
											<input type="text" class="form-control" id="id_movil" name="id_movil" maxlength="20" pattern="[0-9 ]{1,20}"
												title = "Números. Tamaño máximo: 20" placeholder="Ejemplo : 977137699" value="<?php echo $lb_edit?$array["V_MOVIL"]:""; ?>"> 
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_url_web">Enlace Web</label>
											<input type="url" class="form-control" id="id_url_web" name="id_url_web" maxlength="80" pattern="http://[A-Za-z]+[A-Za-z0-9\.-]*[^\.]\.com"
											title = "Letras, Números y carácteres de url. Tamaño máximo: 80" placeholder="Ejemplo : https://www.miempresa.com" value="<?php echo $lb_edit?$array["V_URL_WEB"]:""; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_url_fb">Enlace Facebook</label>
											<input type="url" class="form-control" id="id_url_fb" name="id_url_fb" maxlength="80" pattern="http://[A-Za-z]+[A-Za-z0-9\.-]*[^\.]\.com"
											title = "Letras, Números y carácteres de url. Tamaño máximo: 80" placeholder="Ejemplo : https://www.miempresa.com" value="<?php echo $lb_edit?$array["V_URL_FB"]:""; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_url_yt">Enlace YouTube</label>
											<input type="url" class="form-control" id="id_url_yt" name="id_url_yt" maxlength="80" pattern="http://[A-Za-z]+[A-Za-z0-9\.-]*[^\.]\.com"
											title = "Letras, Números y carácteres de url. Tamaño máximo: 80" placeholder="Ejemplo : https://www.miempresa.com" value="<?php echo $lb_edit?$array["V_URL_YT"]:""; ?>">
										</div>
									</div>
								</div>
								
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_url_tw">Enlace Twitter</label>
											<input type="url" class="form-control" id="id_url_tw" name="id_url_tw" maxlength="80" pattern="http://[A-Za-z]+[A-Za-z0-9\.-]*[^\.]\.com"
											title = "Letras, Números y carácteres de url. Tamaño máximo: 80" placeholder="Ejemplo : https://www.miempresa.com" value="<?php echo $lb_edit?$array["V_URL_TW"]:""; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_url_ig">Enlace Instagram</label>
											<input type="url" class="form-control" id="id_url_ig" name="id_url_ig" maxlength="80" pattern="http://[A-Za-z]+[A-Za-z0-9\.-]*[^\.]\.com"
											title = "Letras, Números y carácteres de url. Tamaño máximo: 80" placeholder="Ejemplo : https://www.miempresa.com" value="<?php echo $lb_edit?$array["V_URL_IG"]:""; ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_url_tk">Enlace TikTok</label>
											<input type="url" class="form-control" id="id_url_tk" name="id_url_tk" maxlength="80" pattern="http://[A-Za-z]+[A-Za-z0-9\.-]*[^\.]\.com"
											title = "Letras, Números y carácteres de url. Tamaño máximo: 80" placeholder="Ejemplo : https://www.miempresa.com" value="<?php echo $lb_edit?$array["V_URL_TK"]:""; ?>">
										</div>
									</div>
								</div>
																
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_nombre_soporte">Responsable Soporte</label>
											<input type="text" class="form-control" id="id_nombre_soporte" name="id_nombre_soporte" maxlength="150" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s0-9 ]{1,150}"
												title = "Letras y Números. Tamaño máximo: 150" placeholder="Escribe nombre de soporte" value="<?php echo $lb_edit?$array["V_NOMBRE_SOPORTE"]:""; ?>">	 
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_fono_soporte">Fono Soporte</label>
											<input type="text" class="form-control" id="id_fono_soporte" name="id_fono_soporte" maxlength="20" pattern="[0-9 ]{1,20}"
												title = "Números. Tamaño máximo: 20" placeholder="Ejemplo : 977137699" value="<?php echo $lb_edit?$array["V_FONO_SOPORTE"]:""; ?>"> 
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="id_email_soporte">Email Soporte</label>
											<input type="email" class="form-control" id="id_email_soporte" name="id_email_soporte" maxlength="80" pattern="[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*@[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{1,5}"
											title = "Letras, Números y carácteres de email. Tamaño máximo: 80" placeholder="Ejemplo : jperez@gmail.com" value="<?php echo $lb_edit?$array["V_EMAIL"]:""; ?>">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-3">
										<div class="form-group">
											<label for="id_horas">Horas por clase</label>
											<input type="number" class="form-control" id="id_horas" name="id_horas" maxlength="3" pattern="[0-9 ]{1,3}"
												title = "Números. Tamaño máximo: 3" placeholder="Ejemplo : 1" value="<?php echo $lb_edit?$array["N_HORASXCLASE"]:""; ?>"> 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="id_clases">Numero de Clases</label>
											<input type="number" class="form-control" id="id_clases" name="id_clases" maxlength="3" pattern="[0-9 ]{1,3}"
												title = "Números. Tamaño máximo: 3" placeholder="Ejemplo : 12" value="<?php echo $lb_edit?$array["N_CLASESXHORARIO"]:""; ?>"> 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="id_estudiantes_min">Min. Estudiantes</label>
											<input type="number" class="form-control" id="id_estudiantes_min" name="id_estudiantes_min" maxlength="3" pattern="[0-9 ]{1,3}"
												title = "Números. Tamaño máximo: 3" placeholder="Ejemplo : 15" value="<?php echo $lb_edit?$array["N_MIN_ESTUDIANTES"]:""; ?>"> 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="id_estudiantes_max">Max. Estudiantes</label>
											<input type="number" class="form-control" id="id_estudiantes_max" name="id_estudiantes_max" maxlength="3" pattern="[0-9 ]{1,3}"
												title = "Números. Tamaño máximo: 3" placeholder="Ejemplo : 50" value="<?php echo $lb_edit?$array["N_MAX_ESTUDIANTES"]:""; ?>"> 
										</div>
									</div>
								</div>

								<div class="form-group">
									<label for="id_bienvenida">Bienvenida</label>
									<textarea class="form-control" rows="2" id="id_bienvenida" name="id_bienvenida" placeholder="Ejemplo : ..."><?php echo $lb_edit?$array["V_BIENVENIDA"]:""; ?></textarea>
								</div>	
								
								<div class="form-group">
									<label for="id_somos">Somos</label>
									<textarea class="form-control" rows="2" id="id_somos" name="id_somos" placeholder="Ejemplo : ..."><?php echo $lb_edit?$array["V_SOMOS"]:""; ?></textarea>
								</div>	

								<div class="form-group">
									<label for="id_mision">Misión</label>
									<textarea class="form-control" rows="2" id="id_mision" name="id_mision" placeholder="Ejemplo : ..."><?php echo $lb_edit?$array["V_MISION"]:""; ?></textarea>
								</div>	

								<div class="form-group">
									<label for="id_vision">Visión</label>
									<textarea class="form-control" rows="2" id="id_vision" name="id_vision" placeholder="Ejemplo : ..."><?php echo $lb_edit?$array["V_VISION"]:""; ?></textarea>
								</div>	
								
								<div class="form-group">
									<label for="id_imagen">Adjuntar una Imagen</label>
									<input type="file" id="archivo" name="archivo">					
									<?php if($lb_edit){
										if(strlen($array["V_FOTO"])>1){
										?>	
										</br>
										<img class="img-responsive img-thumbnail" src="../../upload/<?php echo DEF_UPLOAD_EMPRESA_DIR.'/'.$array["V_FOTO"];?>" width="150px">
										<?php
										}	
									}?>
								</div>

								<?php
								if ($lb_edit) {
									echo "<input type=hidden name=id_codigo value=\"".$array["V_ID"]."\">";
									?>
									<input class="btn btn-success" type="submit" id ="btn_actualizar" value="Actualizar" onclick=this.form.action="<?php echo $url_actualizar?>">
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