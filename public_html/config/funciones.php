<?php

// Funcion carga y redimensiona imagen archivo al servidor
function f_upload_archivo($archivo, $carpeta, $tipo, $valor1, $valor2) {
	
	// Carga libreria de upload
    //require_once("../recursos/upload/class.upload.php");
    include('../recursos/upload/class.upload.php');
	
	// Asigna Ruta
	$carpeta = "../../upload/$carpeta";

	// Recupera peso
	$peso  = $_FILES["archivo"]["size"];
    
	//Verificamos tamaño . .
    $kilobytes = $peso/1024;	// Obtenemos peso en kb    
    $megas = $kilobytes/1024;	// Obtenemos peso en megas    
	// Validar Peso
	if ($megas > 10 ) {
        echo 'El archivo cargado supera los 10 Megas';
		?>
		<input type="button" value="Retornar" title="Retornar" onclick="history.back()" />
        <?php
    }
	
	// Instanciar la clase
	$handle = new Verot\Upload\Upload($archivo);
    
    // Segùn caso
	if ($handle->uploaded) {
		$handle->image_resize  = true;
		if ($tipo == 'W'){ // Width
			$handle->image_ratio_y		= true;
			$handle->image_x        	= $valor1;
		}
		if ($tipo == 'H'){ // Height
			$handle->image_ratio_x  	= true;
			$handle->image_y        	= $valor1;
		}
		if ($tipo == 'C'){ // Crop
			$handle->image_ratio_crop 	= true;
			$handle->image_x        	= $valor1;
			$handle->image_y            = $valor2;
		}else{
			$handle->image_ratio    	= true;
			$handle->image_x        	= $valor1;
			$handle->image_y        	= $valor2;
		}	

        // Procesar Carpeta
		$handle->Process($carpeta);
        
        // Fue procesado ?
		if ($handle->processed){
			$cadena = $handle->file_dst_pathname;
			$pos = strrpos($cadena,"/"); // \\ JR 2020
			$nombre = substr($cadena, $pos+1);
		}else{
			$nombre = "";
		}

	}

    // Retornar
	return $nombre;

}	

//Funcion cargando archivo al servidor
function f_carga_archivo($archivo, $carpeta) {
    //Recuperando datos de archivo ..
    $archivo = $_FILES["archivo"]["name"];
    $temp    = $_FILES["archivo"]["tmp_name"];
    $tamano  = $_FILES["archivo"]["size"];
    $tipo    = $_FILES["archivo"]["type"];

    //Verificamos tamaño . .
    $kilobytes = $tamano/1024;//con esto temenos la cantidad en kb (
    $megas = $kilobytes/1024;

    if ($megas > 10 ) {
        echo '<div id="msg_error">El archivo cargado supera los 10 megas</div>';
        ?>
<br />
<input type="button" value="Volver" title="Volver" onclick="history.back()" />
        <?php
    }

    //Ahora validamos la extension o el tipo de archivo ..
    if ($tipo=="application/pdf" or $tipo=="image/jpeg" or $tipo=="image/jpg" or $tipo=="image/gif" or $tipo=="image/png" or $tipo=="application/vnd.openxmlformats-officedocument.wordprocessingml.document") {
        //Ahora podemos subir la imagen al servidor
        switch ($tipo) {
            case 'application/pdf':
                $ext=".pdf";
                break;
            case 'image/jpeg':
                $ext=".jpg";
                break;
            case 'image/jpg':
                $ext=".jpg";
                break;
            case 'image/gif':
                $ext=".gif";
                break;
            case 'image/png':
                $ext=".png";
                break;
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $ext=".docx";
                break;
        }

        //Renombrando archivo ..
        //$nombre_archivo = str_replace(" ","_",$nombre_archivo);

        //generamos el randon
        $randon="";
        for ($i=0; $i<15; $i++) {
            $d=rand(1,30)%2;
            $caracter=($d ? chr(rand(65,90)) : chr(rand(48,57)));
            $randon=$randon.$caracter;
        }

        $nombre_archivo = $randon.$ext;
        //Subiendo archivo ..
        copy($temp,"../upload/$carpeta/$nombre_archivo");

    }else {

        $nombre_archivo = " ";

    }

    //Return campo
    return $nombre_archivo;

}

//Funcion que Muestra Url . .
function f_r_url($url, $descripcion, $ayuda) {
    ?>
<a href="<?php echo $url?>" title=".: <?php echo $ayuda?> :."><?php echo $descripcion?></a>
    <?php
}

//Funcion que Muestra Url Vertical. .
function f_r_url_vert($url, $descripcion, $ayuda) {
    ?>
<a href="<?php echo $url?>" title=".: <?php echo $ayuda?> :."><?php echo $descripcion?></a>
    <?php
}

//Funcin mostrar url hija . .
function f_r_url_hija($url, $descripcion, $ayuda) {
    ?>
<a href="<?php echo $url?>" title=".: <?php echo $ayuda?> :." target="_blank"><?php echo $descripcion?></a>
    <?php
}

//Funcion que Muestra Imagen Url . .
function f_r_imagen_url($url, $imagen, $ayuda) {
    ?>
<a href="<? echo $url?>" title=".: <? echo $ayuda?> :."><img src="images/<? echo $imagen?>"/></a>
    <?php
}

// Funcion que Muestra Imagen . .
function f_r_imagen($image, $ext, $alt, $alto, $ancho) {
    echo "<image src=\"images/$image".".$ext\" alt=\"$alt\" border=0 height = $alto width = $ancho>  ";
}

// Fecha Capturada .
function f_r_fecha() {
    $week_days = array ("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado");
    $months = array ("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    $year_now = date ("Y");
    $month_now = date ("n");
    $day_now = date ("j");
    $week_day_now = date ("w");
    $date = $week_days[$week_day_now] . ", " . $day_now . " de " . $months[$month_now] . " del " . $year_now;
    return $date;
}

// Fecha Capturada .
function f_r_diasemana($fecha) {
    $week_days = array ("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado");
    $diaSemana = $week_days[date('N', strtotime($fecha))];
    return $diaSemana;
}

// Funcion que Corta Cadena ..
function f_r_corta_cadena($texto) {
    $tamano = 252; // tamao maximo
    $textoFinal = ''; // Resultado

    // Si el numero de carateres del texto es menor que el tamaño maximo,
    // el tamaño maximo pasa a ser el del texto
    if (strlen($texto) < $tamano) $tamano = strlen($texto);

    for ($i=0; $i <= $tamano - 1; $i++) {
        // Añadimos uno por uno cada caracter del texto
        // original al texto final, habiendo puesto
        // como limite la variable $tamano
        $textoFinal .= $texto[$i];
    }
	$textoFinal = $textoFinal."...";
    // devolvemos el texto final
    return $textoFinal;
}

function f_get_edad($fecha_nacimiento){
    $dia=date("d");
    $mes=date("m");
    $ano=date("Y");
    $dianaz=date("d",strtotime($fecha_nacimiento));
    $mesnaz=date("m",strtotime($fecha_nacimiento));
    $anonaz=date("Y",strtotime($fecha_nacimiento));

    //si el mes es el mismo pero el día inferior aun no ha cumplido años, le quitaremos un año al actual
    if (($mesnaz == $mes) && ($dianaz > $dia)) {
    $ano=($ano-1); }
    
    //si el mes es superior al actual tampoco habrá cumplido años, por eso le quitamos un año al actual
    if ($mesnaz > $mes) {
    $ano=($ano-1);}
    
     //ya no habría mas condiciones, ahora simplemente restamos los años y mostramos el resultado como su edad   
    $edad=($ano-$anonaz);    
    
    return $edad; 
    
}

// ======================================= WHATSAPP ======================================= //

// Arma un link de WhatsApp (wa.me) a partir de un número de celular y un mensaje.
// No requiere ninguna API ni configuración: abre WhatsApp Web o la app con el
// mensaje ya escrito, listo para que el usuario le dé "Enviar".
function f_whatsapp_link($as_movil, $as_mensaje) {

    // Limpiar el número: dejar solo dígitos
    $ls_movil = preg_replace('/\D/', '', $as_movil);

    if (empty($ls_movil)) {
        return '';
    }

    // Si no trae código de país, asumir Perú (51) cuando el número tiene 9 dígitos
    if (strlen($ls_movil) == 9) {
        $ls_movil = '51' . $ls_movil;
    }

    return 'https://wa.me/' . $ls_movil . '?text=' . rawurlencode($as_mensaje);
}

// ======================================= HISTORIA CLINICA ======================================= //

/**
 * Crea o actualiza una entrada de Historia Clínica a partir de una Cita o una Sesión.
 * - Si ya existe una entrada ligada a esa misma cita/sesión, la actualiza (evita duplicados).
 * - Si no existe, crea una nueva, arrastrando antecedentes/alergias/enfermedades de la
 *   última entrada del paciente para no perder esa información.
 * - No hace nada si tanto el diagnóstico como la observación llegan vacíos.
 *
 * @param crud   $crud            Instancia ya creada de la clase crud
 * @param int    $li_cod_paciente Código del paciente
 * @param string $ld_fecha        Fecha de la cita/sesión (Y-m-d)
 * @param string $ls_diagnostico  Diagnóstico (puede venir vacío)
 * @param string $ls_observacion  Observación (puede venir vacía)
 * @param int|null $li_cod_cita   Código de la cita de origen (o null si viene de una sesión)
 * @param int|null $li_cod_sesion Código de la sesión de origen (o null si viene de una cita)
 * @param string $ls_usuario      Usuario que realiza la acción (para auditoría)
 */
function f_sincronizar_historia_clinica($crud, $li_cod_paciente, $ld_fecha, $ls_diagnostico, $ls_observacion, $li_cod_cita, $li_cod_sesion, $ls_usuario) {

    // Si no hay nada que registrar, no crear una entrada vacía
    if (empty(trim((string) $ls_diagnostico)) && empty(trim((string) $ls_observacion))) {
        return;
    }

    $ldt_ahora = date('Y-m-d H:i:s');

    // Buscar si ya existe una entrada ligada a esta cita o sesión específica
    $array_existente = null;

    if (!empty($li_cod_cita)) {
        $array_campo_pk = array('N_COD_CITA', 'V_FLAG_ESTADO');
        $array_valor_pk = array($li_cod_cita, '1');
        $lr = $crud->fila_listar(DEF_TABLA_HISTORIA_CLINICA, $array_campo_pk, $array_valor_pk, 'N_COD_HISTORIA', 'D', 0, 1);
        $array_existente = $lr ? mysqli_fetch_assoc($lr) : null;
    } elseif (!empty($li_cod_sesion)) {
        $array_campo_pk = array('N_COD_SESION', 'V_FLAG_ESTADO');
        $array_valor_pk = array($li_cod_sesion, '1');
        $lr = $crud->fila_listar(DEF_TABLA_HISTORIA_CLINICA, $array_campo_pk, $array_valor_pk, 'N_COD_HISTORIA', 'D', 0, 1);
        $array_existente = $lr ? mysqli_fetch_assoc($lr) : null;
    }

    // Si ya existe, solo actualizarla (evita duplicados cuando se edita la cita/sesión)
    if ($array_existente) {

        $array_campo_pk = array('N_COD_HISTORIA');
        $array_valor_pk = array($array_existente['N_COD_HISTORIA']);

        $array_campo = array('D_FEC_CITA', 'V_DIAGNOSTICO', 'V_OBSERVACION', 'V_AUD_USR_MOD', 'D_AUD_FEC_MOD');
        $array_valor = array($ld_fecha, $ls_diagnostico, $ls_observacion, $ls_usuario, $ldt_ahora);

        $crud->fila_actualizar(DEF_TABLA_HISTORIA_CLINICA, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor);
        return;
    }

    // Traer antecedentes/alergias/enfermedades vigentes del paciente (última entrada), para no perderlos
    $array_campo_pk = array('N_COD_PACIENTE', 'V_FLAG_ESTADO');
    $array_valor_pk = array($li_cod_paciente, '1');
    $lr_ultima = $crud->fila_listar(DEF_TABLA_HISTORIA_CLINICA, $array_campo_pk, $array_valor_pk, 'N_COD_HISTORIA', 'D', 0, 1);
    $array_ultima = $lr_ultima ? mysqli_fetch_assoc($lr_ultima) : null;

    $array_campo = array(
        'N_COD_PACIENTE',
        'N_COD_CITA',
        'N_COD_SESION',
        'D_FEC_CITA',
        'V_DIAGNOSTICO',
        'V_OBSERVACION',
        'V_ANTECEDENTES',
        'V_ALERGIAS',
        'V_ENFERMEDADES',
        'V_FLAG_ESTADO',
        'V_AUD_USR_REG',
        'D_AUD_FEC_REG'
    );

    $array_valor = array(
        $li_cod_paciente,
        $li_cod_cita,
        $li_cod_sesion,
        $ld_fecha,
        $ls_diagnostico,
        $ls_observacion,
        $array_ultima ? $array_ultima['V_ANTECEDENTES'] : null,
        $array_ultima ? $array_ultima['V_ALERGIAS'] : null,
        $array_ultima ? $array_ultima['V_ENFERMEDADES'] : null,
        '1',
        $ls_usuario,
        $ldt_ahora
    );

    $crud->fila_registrar(DEF_TABLA_HISTORIA_CLINICA, $array_campo, $array_valor, '0');
}

?>
