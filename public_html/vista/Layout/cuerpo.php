<?php

// Incluye Libreria BD
require_once("../config/class_crud.php");

// Cuerpo - Izda
function f_admin_cuerpo_izda($id_nivel1, $id_nivel2){

// Instanciar clase
$crud = new crud();

// Niveles
$array_campo_pk	= array('V_FLAG_ESTADO');
$array_valor_pk	= array('1');
$array_nivel	= $crud->fila_listar('MAE_MENU_NIVEL', $array_campo_pk, $array_valor_pk, 'N_ORDEN', 'A', 0, 10);

// Informacion
$array_campo_info_pk	= array('V_COD_USER');
$array_valor_info_pk	= array($_SESSION['usr_conectado']);
$ls_data_nombre = $crud->fila_recuperar_campo("MAE_USUARIO", $array_campo_info_pk, $array_valor_info_pk,"V_NOMBRES"); 
$ls_data_imagen = $crud->fila_recuperar_campo("MAE_USUARIO", $array_campo_info_pk, $array_valor_info_pk,"V_FOTO"); 

// Gestionar sin foto
if ($ls_data_imagen == ""){
	$ls_data_imagen ='unnamed.jpg';
}
	
?>
	<!-- Columna Izquierda -->
    <aside class="main-sidebar">

        <!-- Seccion sidebar -->
        <section class="sidebar">
			
            <!-- Seccion Panel - Usuario -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="../../upload/usuario/<?php echo $ls_data_imagen?>" class="img-rounded" alt="Usuario">
                </div>
                <div class="pull-left info">
                    <p><?php echo $ls_data_nombre;?></p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
            <!-- Fin Seccion Panel - Usuario -->

            <!-- Seccion items de Menu -->
            <ul class="sidebar-menu" data-widget="tree">
				
				<li class="header">PRINCIPAL</li>

				<!-- Recorre Niveles -->
				<?php				
				if ($array_nivel) {	
					while ($row_nivel = mysqli_fetch_assoc($array_nivel)) {
						
						// Treview
						if($row_nivel["N_COD_NIVEL"] == $id_nivel1){
							echo "<li class='active treeview'>";
						}else{
							echo "<li class='treeview'>";
						}
						?>
													
							<a href="#">
								<i class="<?php echo $row_nivel["V_ICONO"];?>"></i> 
								<span><?php echo $row_nivel["V_NOMBRE"];?></span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a>
							
							<ul class="treeview-menu">
								
								<?php
								
								// Almacenar nivel
								$nivel = $row_nivel["N_COD_NIVEL"];
								
								// Estructura de opciones
								$array_campo_pk	= array('V_FLAG_ESTADO', 'N_COD_NIVEL');
								$array_valor_pk	= array('1', $nivel);
								
								$array_opciones = $crud->fila_listar('MAE_MENU_OPCION', $array_campo_pk, $array_valor_pk, 'N_ORDEN', 'A', 0, 100);
								
								// Recorre opciones de Nivel
								if ($array_opciones) {	
									while ($row_opcion = mysqli_fetch_assoc($array_opciones)) {
										
										// Obtener permiso
										$li_permiso = $crud->f_usuario_acceso($_SESSION['usr_conectado'], $row_opcion["N_COD_OPCION"]);

										// <li> Opciones
										if($row_opcion["N_ORDEN"] == $id_nivel2 && $row_nivel["N_COD_NIVEL"] == $id_nivel1){
											echo "<li class='active'>";
										}else{
											echo "<li>";
										}
										
										// Gestionar permisos por opcion
										if ($li_permiso == 1) {
											?>
												<a href="<?php echo $row_opcion["V_URL"]?>">
													<i class="<?php echo $row_opcion["V_ICONO"]?>"></i> 
													<?php echo $row_opcion["V_NOMBRE"]?> 
												</a>
											</li>
											<?php
										}else{
											?>
												<a class="enlace_desactivado" href="<?php echo $row_opcion["V_URL"]?>">
													<i class="<?php echo $row_opcion["V_ICONO"]?>"></i> 
													<?php echo $row_opcion["V_NOMBRE"]?> 
												</a>
											</li>
											<?php	
										}													
										
									}
								}
								
								?>
							</ul>
					
						<?php
						echo "</li>";
					}
				}			
				?>
				    
            </ul>
            <!-- Fin Seccion items de Menu -->

        </section>
        <!-- Fin sidebar -->

    </aside>
    <!-- Fin Columna Izquierda -->
<?php
}

// Cuerpo - Drcha
function f_admin_cuerpo_drcha(){

	// Instanciar clase
	$crud = new crud();
	$ls_hoy      = date('Y-m-d');
	$ls_mes_ini  = date('Y-m-01');

	// ================================ CITAS DE HOY ================================ //
	$array_campo_pk = array('D_FEC_CITA', 'V_FLAG_ESTADO');
	$array_valor_pk = array($ls_hoy, '1');
	$array_citas_hoy = $crud->fila_listar(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk, 'D_HORA_INICIO', 'A', 0, 200);

	$li_citas_hoy_total      = 0;
	$li_citas_hoy_pendientes = 0;
	$li_citas_hoy_atendidas  = 0;
	$li_citas_hoy_canceladas = 0;
	$array_agenda_hoy        = array();

	if ($array_citas_hoy) {
		while ($row = mysqli_fetch_assoc($array_citas_hoy)) {
			$li_citas_hoy_total++;
			if ($row['V_ESTADO_CITA'] == 'ATE') {
				$li_citas_hoy_atendidas++;
			} elseif ($row['V_ESTADO_CITA'] == 'CAN') {
				$li_citas_hoy_canceladas++;
			} elseif ($row['V_ESTADO_CITA'] == 'PRO' || $row['V_ESTADO_CITA'] == 'CON') {
				$li_citas_hoy_pendientes++;
			}
			$array_agenda_hoy[] = $row;
		}
	}

	// ================================ INGRESOS DE HOY Y DEL MES ================================ //
	$ls_cond = "D_FEC_PAGO = '$ls_hoy' AND V_FLAG_ESTADO = '1'";
	$lr = $crud->fila_listar_solocondicion("(SELECT COALESCE(SUM(N_MONTO),0) AS TOTAL FROM " . DEF_TABLA_PAGO . " WHERE $ls_cond) t", '', '', -1, 0);
	$row_tmp = $lr ? mysqli_fetch_assoc($lr) : null;
	$ln_ingresos_hoy = $row_tmp ? floatval($row_tmp['TOTAL']) : 0;

	$ls_cond = "D_FEC_PAGO BETWEEN '$ls_mes_ini' AND '$ls_hoy' AND V_FLAG_ESTADO = '1'";
	$lr = $crud->fila_listar_solocondicion("(SELECT COALESCE(SUM(N_MONTO),0) AS TOTAL FROM " . DEF_TABLA_PAGO . " WHERE $ls_cond) t", '', '', -1, 0);
	$row_tmp = $lr ? mysqli_fetch_assoc($lr) : null;
	$ln_ingresos_mes = $row_tmp ? floatval($row_tmp['TOTAL']) : 0;

	// ================================ POR COBRAR (CRÉDITOS PENDIENTES) ================================ //
	$lr = $crud->fila_listar_solocondicion("(SELECT COALESCE(SUM(N_MONTO),0) AS TOTAL FROM " . DEF_TABLA_COMPROBANTE . " WHERE V_ESTADO_COMPROBANTE = 'PEN' AND V_FLAG_ESTADO = '1') t", '', '', -1, 0);
	$row_tmp = $lr ? mysqli_fetch_assoc($lr) : null;
	$ln_total_comprometido = $row_tmp ? floatval($row_tmp['TOTAL']) : 0;

	$lr = $crud->fila_listar_solocondicion(
			"(SELECT COALESCE(SUM(p.N_MONTO),0) AS TOTAL FROM " . DEF_TABLA_PAGO . " p
				INNER JOIN " . DEF_TABLA_COMPROBANTE . " c ON p.N_COD_COMPROBANTE = c.N_COD_COMPROBANTE
			  WHERE c.V_ESTADO_COMPROBANTE = 'PEN' AND c.V_FLAG_ESTADO = '1' AND p.V_FLAG_ESTADO = '1') t",
			'', '', -1, 0
		  );
	$row_tmp = $lr ? mysqli_fetch_assoc($lr) : null;
	$ln_total_abonado_pend = $row_tmp ? floatval($row_tmp['TOTAL']) : 0;

	$ln_por_cobrar = round($ln_total_comprometido - $ln_total_abonado_pend, 2);

	// ================================ CONTADORES GENERALES ================================ //
	$array_campo_pk = array('V_FLAG_ESTADO');
	$array_valor_pk = array('1');
	$li_pacientes_activos     = $crud->fila_contar(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk);
	$li_especialistas_activos = $crud->fila_contar(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk);
	$li_sedes_activas         = $crud->fila_contar(DEF_TABLA_SEDE, $array_campo_pk, $array_valor_pk);

	$array_campo_pk = array('V_ESTADO_TRATAMIENTO', 'V_FLAG_ESTADO');
	$array_valor_pk = array('ACT', '1');
	$li_tratamientos_activos = $crud->fila_contar(DEF_TABLA_TRATAMIENTO, $array_campo_pk, $array_valor_pk);

	// ================================ CITAS DE LA SEMANA ================================ //
	$li_dia_semana = date('N'); // 1=lunes ... 7=domingo
	$ls_lunes   = date('Y-m-d', strtotime('-' . ($li_dia_semana - 1) . ' days'));
	$ls_domingo = date('Y-m-d', strtotime($ls_lunes . ' +6 days'));

	$ls_cond = "D_FEC_CITA BETWEEN '$ls_lunes' AND '$ls_domingo' AND V_FLAG_ESTADO = '1'";
	$lr = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . " WHERE $ls_cond", '', '', -1, 0);
	$li_citas_semana = $lr ? $lr->num_rows : 0;

	// ================================ TASA DE INASISTENCIA DEL MES ================================ //
	$ls_cond_mes = "D_FEC_CITA BETWEEN '$ls_mes_ini' AND '$ls_hoy' AND V_FLAG_ESTADO = '1'";
	$lr_mes = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . " WHERE $ls_cond_mes", '', '', -1, 0);
	$li_citas_mes_total = $lr_mes ? $lr_mes->num_rows : 0;

	$lr_noa = $crud->fila_listar_solocondicion(DEF_TABLA_CITA . " WHERE $ls_cond_mes AND V_ESTADO_CITA = 'NOA'", '', '', -1, 0);
	$li_citas_mes_noasistio = $lr_noa ? $lr_noa->num_rows : 0;

	$ln_tasa_inasistencia = $li_citas_mes_total > 0 ? round(($li_citas_mes_noasistio / $li_citas_mes_total) * 100, 1) : 0;

	// ================================ INGRESOS ÚLTIMOS 7 DÍAS ================================ //
	$array_ult7dias = array();
	$ln_max_dia = 0.01; // evita división por cero al calcular el ancho de las barras

	for ($i = 6; $i >= 0; $i--) {
		$ls_dia  = date('Y-m-d', strtotime("-$i days"));
		$ls_cond = "D_FEC_PAGO = '$ls_dia' AND V_FLAG_ESTADO = '1'";
		$lr = $crud->fila_listar_solocondicion("(SELECT COALESCE(SUM(N_MONTO),0) AS TOTAL FROM " . DEF_TABLA_PAGO . " WHERE $ls_cond) t", '', '', -1, 0);
		$row_tmp = $lr ? mysqli_fetch_assoc($lr) : null;
		$ln_monto_dia = $row_tmp ? floatval($row_tmp['TOTAL']) : 0;

		if ($ln_monto_dia > $ln_max_dia) {
			$ln_max_dia = $ln_monto_dia;
		}

		$array_ult7dias[] = array('fecha' => $ls_dia, 'monto' => $ln_monto_dia);
	}

	// Catálogo local de estados de cita (evita depender de mov_cita_html.php, que no siempre está cargado aquí)
	$array_estados_cita = array(
		'PRO' => array('Programada',   'label-default'),
		'CON' => array('Confirmada',   'label-info'),
		'ATE' => array('Atendida',     'label-success'),
		'REP' => array('Reprogramada', 'label-warning'),
		'CAN' => array('Cancelada',    'label-danger'),
		'NOA' => array('No asistió',   'label-danger'),
	);

	?>
	<!-- Seccion Contenido -->
	<div class="content-wrapper">

		<!-- Content Header (Page header) -->
		<section class="content-header">
		  <h1>
			Dashboard
			<small>Resumen del centro &mdash; <?php echo date('d/m/Y'); ?></small>
		  </h1>
		</section>

		<!-- Contenido -->
		<section class="content">

			<!-- Fila de indicadores principales -->
			<div class="row">

				<div class="col-lg-3 col-xs-6">
					<div class="small-box bg-aqua">
						<div class="inner">
							<h3><?php echo $li_citas_hoy_total; ?></h3>
							<p>Citas de Hoy</p>
						</div>
						<div class="icon"><i class="fa fa-calendar"></i></div>
						<a href="mov_cita_lista.php" class="small-box-footer">
							<?php echo $li_citas_hoy_pendientes; ?> pendientes, <?php echo $li_citas_hoy_atendidas; ?> atendidas
						</a>
					</div>
				</div>

				<div class="col-lg-3 col-xs-6">
					<div class="small-box bg-green">
						<div class="inner">
							<h3>S/ <?php echo number_format($ln_ingresos_hoy, 2); ?></h3>
							<p>Ingresos de Hoy</p>
						</div>
						<div class="icon"><i class="fa fa-money"></i></div>
						<a href="mov_pago_lista.php" class="small-box-footer">
							Este mes: S/ <?php echo number_format($ln_ingresos_mes, 2); ?>
						</a>
					</div>
				</div>

				<div class="col-lg-3 col-xs-6">
					<div class="small-box bg-yellow">
						<div class="inner">
							<h3><?php echo $li_pacientes_activos; ?></h3>
							<p>Pacientes Activos</p>
						</div>
						<div class="icon"><i class="fa fa-users"></i></div>
						<a href="mae_paciente_lista.php" class="small-box-footer">
							<?php echo $li_especialistas_activos; ?> especialistas, <?php echo $li_sedes_activas; ?> sedes
						</a>
					</div>
				</div>

				<div class="col-lg-3 col-xs-6">
					<div class="small-box bg-red">
						<div class="inner">
							<h3>S/ <?php echo number_format($ln_por_cobrar, 2); ?></h3>
							<p>Por Cobrar (Créditos)</p>
						</div>
						<div class="icon"><i class="fa fa-credit-card"></i></div>
						<a href="mov_comprobante_lista.php" class="small-box-footer">
							Ver Cuentas por Cobrar <i class="fa fa-arrow-circle-right"></i>
						</a>
					</div>
				</div>

			</div>
			<!-- /.row -->

			<div class="row">

				<!-- Agenda de hoy -->
				<div class="col-md-8">
					<div class="panel panel-primary">
						<div class="box-header">
							<i class="fa fa-clock-o"></i> Agenda de Hoy
						</div>
						<div class="panel-body">
							<?php if (empty($array_agenda_hoy)) { ?>
								<div class="alert alert-warning">No hay citas programadas para hoy.</div>
							<?php } else { ?>
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th>Hora</th>
												<th>Paciente</th>
												<th>Especialista</th>
												<th>Estado</th>
												<th>WhatsApp</th>
											</tr>
										</thead>
										<tbody>
												<?php foreach ($array_agenda_hoy as $row) {

												$array_campo_pk	= array('N_COD_PACIENTE');
												$array_valor_pk	= array($row["N_COD_PACIENTE"]);
												$ls_pac = $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO') . ' ' .
														  $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO') . ', ' .
														  $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_NOMBRES');
												$ls_pac_nombres_solo = $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_NOMBRES');
												$ls_pac_movil = $crud->fila_recuperar_campo(DEF_TABLA_PACIENTE, $array_campo_pk, $array_valor_pk, 'V_MOVIL');

												$array_campo_pk	= array('N_COD_ESPECIALISTA');
												$array_valor_pk	= array($row["N_COD_ESPECIALISTA"]);
												$ls_esp = $crud->fila_recuperar_campo(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_APE_PATERNO') . ' ' .
														  $crud->fila_recuperar_campo(DEF_TABLA_ESPECIALISTA, $array_campo_pk, $array_valor_pk, 'V_APE_MATERNO');

												$ls_cod_estado = $row["V_ESTADO_CITA"];
												$ls_des_estado = isset($array_estados_cita[$ls_cod_estado]) ? $array_estados_cita[$ls_cod_estado][0] : $ls_cod_estado;
												$ls_css_estado = isset($array_estados_cita[$ls_cod_estado]) ? $array_estados_cita[$ls_cod_estado][1] : 'label-default';

												$ls_msg_wsp = "Hola $ls_pac_nombres_solo, te recordamos tu cita de hoy a las " . substr($row["D_HORA_INICIO"],0,5) . " con $ls_esp. ¡Te esperamos!";
												$ls_wsp_link = f_whatsapp_link($ls_pac_movil, $ls_msg_wsp);
												?>
												<tr>
													<td><?php echo substr($row["D_HORA_INICIO"],0,5).' - '.substr($row["D_HORA_FIN"],0,5); ?></td>
													<td><a href="mov_cita_editar.php?id_codigo=<?php echo intval($row["N_COD_CITA"]); ?>"><?php echo $ls_pac; ?></a></td>
													<td><?php echo $ls_esp; ?></td>
													<td><span class="label <?php echo $ls_css_estado; ?>"><?php echo $ls_des_estado; ?></span></td>
													<td>
														<?php if (!empty($ls_wsp_link)) { ?>
														<a href="<?php echo $ls_wsp_link; ?>" target="_blank" class="btn btn-xs btn-success" title="Enviar recordatorio por WhatsApp">
															<i class="fa fa-whatsapp"></i>
														</a>
														<?php } else { ?>
															<span class="text-muted" title="El paciente no tiene celular registrado">-</span>
														<?php } ?>
													</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							<?php } ?>
						</div>
					</div>

						<!-- Agenda Programada de la Semana (próximos 7 días) -->
					<div class="panel panel-info">
						<div class="box-header"><i class="fa fa-calendar-o"></i> Agenda Programada de la Semana (Próximos 7 Días)</div>
						<div class="panel-body">
							<?php
							$li_hay_citas_semana = false;
							for ($i = 0; $i <= 6; $i++) {

								$ls_dia = date('Y-m-d', strtotime("+$i days"));

								$array_campo_pk = array('D_FEC_CITA', 'V_FLAG_ESTADO');
								$array_valor_pk = array($ls_dia, '1');
								$lr_dia = $crud->fila_listar(DEF_TABLA_CITA, $array_campo_pk, $array_valor_pk, 'D_HORA_INICIO', 'A', 0, 200);

								$li_total_dia = 0;
								$li_pendientes_dia = 0;
								$li_atendidas_dia = 0;
								$li_canceladas_dia = 0;

								if ($lr_dia) {
									while ($row_dia = mysqli_fetch_assoc($lr_dia)) {
										$li_total_dia++;
										if ($row_dia['V_ESTADO_CITA'] == 'ATE') { $li_atendidas_dia++; }
										elseif ($row_dia['V_ESTADO_CITA'] == 'CAN') { $li_canceladas_dia++; }
										elseif ($row_dia['V_ESTADO_CITA'] == 'PRO' || $row_dia['V_ESTADO_CITA'] == 'CON') { $li_pendientes_dia++; }
									}
								}

								if ($li_total_dia > 0) { $li_hay_citas_semana = true; }
								?>
								<div style="display:flex; align-items:center; padding:6px 0; border-bottom:1px solid #f0f0f0;">
									<div style="width:130px;">
										<?php
										$array_dias_es = array('Sunday'=>'Domingo','Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miércoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sábado');
										$ls_nombre_dia = $array_dias_es[date('l', strtotime($ls_dia))];
										?>
										<strong><?php echo ($i == 0) ? 'Hoy' : $ls_nombre_dia; ?></strong><br>
										<small class="text-muted"><?php echo date('d/m/Y', strtotime($ls_dia)); ?></small>
									</div>
									<div style="flex:1;">
										<?php if ($li_total_dia == 0) { ?>
											<span class="text-muted">Sin citas programadas</span>
										<?php } else { ?>
											<a href="mov_cita_lista.php?id_fec_ini_filtro=<?php echo $ls_dia; ?>&id_fec_fin_filtro=<?php echo $ls_dia; ?>&id_cod_especialista_filtro=">
												<span class="label label-default"><?php echo $li_total_dia; ?> cita<?php echo $li_total_dia != 1 ? 's' : ''; ?></span>
											</a>
											&nbsp;
											<?php if ($li_pendientes_dia > 0) { ?><span class="label label-primary"><?php echo $li_pendientes_dia; ?> pendientes</span><?php } ?>
											<?php if ($li_atendidas_dia > 0) { ?><span class="label label-success"><?php echo $li_atendidas_dia; ?> atendidas</span><?php } ?>
											<?php if ($li_canceladas_dia > 0) { ?><span class="label label-danger"><?php echo $li_canceladas_dia; ?> canceladas</span><?php } ?>
										<?php } ?>
									</div>
								</div>
								<?php
							}
							if (!$li_hay_citas_semana) { ?>
								<div class="alert alert-warning" style="margin-top:10px;">No hay citas programadas para los próximos 7 días.</div>
							<?php } ?>
						</div>
					</div>
					<!-- Fin Agenda de la Semana -->

					<!-- Ingresos últimos 7 días -->
					<div class="panel panel-default">
						<div class="box-header"><i class="fa fa-bar-chart"></i> Ingresos de los Últimos 7 Días</div>
						<div class="panel-body">
							<?php foreach ($array_ult7dias as $dia) {
								$li_ancho = round(($dia['monto'] / $ln_max_dia) * 100);
								if ($li_ancho < 3 && $dia['monto'] > 0) $li_ancho = 3;
								?>
								<div style="margin-bottom:8px;">
									<div style="display:inline-block; width:90px; font-size:12px;">
										<?php echo date('d/m (D)', strtotime($dia['fecha'])); ?>
									</div>
									<div style="display:inline-block; width:calc(100% - 190px); background:#f4f4f4; border-radius:3px; vertical-align:middle;">
										<div style="width:<?php echo $li_ancho; ?>%; background:#00a65a; color:#fff; padding:3px 6px; border-radius:3px; font-size:11px; white-space:nowrap;">
											&nbsp;
										</div>
									</div>
									<div style="display:inline-block; width:90px; text-align:right; font-size:12px;">
										S/ <?php echo number_format($dia['monto'], 2); ?>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<!-- /.col-md-8 -->

				<!-- Panel lateral de indicadores -->
				<div class="col-md-4">

					<div class="panel panel-info">
						<div class="box-header"><i class="fa fa-heartbeat"></i> Estado General</div>
						<div class="panel-body">
							<table class="table">
								<tr>
									<td>Tratamientos Activos</td>
									<td class="text-right"><strong><?php echo $li_tratamientos_activos; ?></strong></td>
								</tr>
								<tr>
									<td>Citas esta Semana</td>
									<td class="text-right"><strong><?php echo $li_citas_semana; ?></strong></td>
								</tr>
								<tr>
									<td>Citas Canceladas Hoy</td>
									<td class="text-right"><strong><?php echo $li_citas_hoy_canceladas; ?></strong></td>
								</tr>
								<tr>
									<td>Tasa de Inasistencia (mes)</td>
									<td class="text-right">
										<strong class="<?php echo $ln_tasa_inasistencia > 15 ? 'text-danger' : 'text-success'; ?>">
											<?php echo $ln_tasa_inasistencia; ?>%
										</strong>
									</td>
								</tr>
							</table>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="box-header"><i class="fa fa-bolt"></i> Accesos Rápidos</div>
						<div class="panel-body">
							<a href="mov_cita_nuevo.php" class="btn btn-primary btn-block"><i class="fa fa-plus"></i> Nueva Cita</a>
							<a href="mae_paciente_nuevo.php" class="btn btn-default btn-block"><i class="fa fa-user-plus"></i> Nuevo Paciente</a>
							<a href="mov_pago_nuevo.php" class="btn btn-default btn-block"><i class="fa fa-money"></i> Registrar Pago</a>
							<a href="mov_comprobante_lista.php" class="btn btn-default btn-block"><i class="fa fa-credit-card"></i> Cuentas por Cobrar</a>
						</div>
					</div>

				</div>
				<!-- /.col-md-4 -->

			</div>
			<!-- /.row -->

		</section>

	</div>
	<!-- Fin seccion Contenido -->
<?php
}

// Formulario de Registro
function f_form_cambiarclave($as_msgRpta) {

	// Instanciar clase
	$crud = new crud();

	// Enlaces
	$url_actualizar	= "../controlador/mae_cambiarclave_actualizar.php";
	
	// Formulario HTML
	?>

	<!-- Sección Contenido -->
	<div class="content-wrapper">

		<!-- Cabecera de Sección Contenido -->
		<section class="content-header">
			<h1>
				<i class="fa fa-unlock-alt"></i> Cambiar Clave
			</h1>
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
							<i class="fa fa-edit"></i>
							MANTENIMIENTO
						</div>

						<!-- Cuerpo -->
						<div class="panel-body">

							<form  id="form_mtto" role="form" method="post" action="" enctype="multipart/form-data" autocomplete="off">

								<div class="form-group">
									<label for="id_clave_actual">Contraseña Actual </label>
									<input type="password" class="form-control" id="id_clave_actual" name="id_clave_actual" maxlength="20" required
									title = "Tamaño máximo: 20" placeholder="**********" value="">
								</div>
								
								<div class="form-group">
									<label for="id_clave_nueva">Nueva Contraseña </label>
									<input type="password" class="form-control" id="id_clave_nueva" name="id_clave_nueva" maxlength="20" required
									title = "Tamaño máximo: 20" placeholder="**********" value="">
								</div>

								<div class="form-group">
									<label for="id_clave_nueva_confirma">Repetir Nueva Contraseña </label>
									<input type="password" class="form-control" id="id_clave_nueva_confirma" name="id_clave_nueva_confirma" maxlength="20" required
									title = "Tamaño máximo: 20" placeholder="**********" value="">
								</div>

								<input class="btn btn-success" type="submit" id ="btn_actualizar" value="Actualizar Contraseña" onclick=this.form.action="<?php echo $url_actualizar?>">
			
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