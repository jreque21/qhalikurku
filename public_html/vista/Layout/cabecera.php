<?php

function f_admin_cabecera(){

	// Logo de la empresa (el mismo configurado en Mantenimiento > Empresa
	// y usado en el login). Si no tiene logo propio cargado, se usa el
	// logo institucional por defecto.
	$crud_cabecera = new crud();
	$array_campo_pk_logo = array('V_ID');
	$array_valor_pk_logo = array(1);
	$ls_foto_empresa_cabecera = $crud_cabecera->fila_recuperar_campo(DEF_TABLA_EMPRESA, $array_campo_pk_logo, $array_valor_pk_logo, 'V_FOTO');

	if (!empty($ls_foto_empresa_cabecera)) {
		$ls_logo_cabecera = "../../upload/".DEF_UPLOAD_EMPRESA_DIR."/".$ls_foto_empresa_cabecera;
	} else {
		$ls_logo_cabecera = "../../website/recursos/images/Logo.png";
	}
?>
<!-- Inicio Pagina -->
<div class='wrapper'>

	<!-- Inicio Cabecera -->
    <header class="main-header">
	
        <!-- Logo -->
        <a href="panel_admin.php" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><b>T</b>K</span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg"><b><?php echo COPYRIGHT_AUTOR ?></b></span>
        </a>
        <!-- Fin Logo -->

        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">

            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">

                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-gears"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="<?php echo $ls_logo_cabecera; ?>" class="img-circle" alt="Imagen Logo">
                                <p>
                                    Soporte : <?php echo SOPORTE_MOVIL ?>
                                    <small><?php echo SOPORTE_EMAIL ?></small>
                                </p>
                            </li>

                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="../vista/mae_cambiarclave.php" class="btn btn-default btn-flat">Cambiar Clave</a>
                                </div>
                                <div class="pull-right">
                                    <a href="salir.php" class="btn btn-default btn-flat">Cerrar Sesion</a>
                                </div>
                            </li>
                        </ul>
                    </li>
     
                </ul>
            </div>
        </nav>
		
    </header>
	
<?php
}