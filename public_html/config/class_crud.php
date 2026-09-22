<?php

// Incluye Clase BD
require_once("class_baseDatos.php");
  
class crud{
	
	/* Propiedades de la clase */
	protected	$query;
				
	/* Registrar Fila */
	public function fila_registrar($tabla, $array_campo, $array_valor, $flag_autoincremento){
		
		// Inicializar
		$campos  = '';
		$valores = '';
		
		// Según caso
		if ($flag_autoincremento=='1'){
			$inicio = 1;
		}else{
			$inicio = 0;
		}
		
		// Armar condición
		if (count($array_campo) > 0) {
			FOR($i=$inicio; $i<count($array_campo); $i++) {
				if($i==count($array_campo)-1) {
					$campos  =  $campos.$array_campo[$i];
					$valores =  $valores."'".$array_valor[$i]."'";
				}else {
					$campos  =  $campos.$array_campo[$i].",";
					$valores =  $valores."'".$array_valor[$i]."',";
				}
			}
		}

		// Preparar query a ejecutar
		$this->query = "INSERT INTO $tabla($campos) VALUES ($valores)";		
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);	
		
		// Retorno
		if($result){
			return true;
		}else{
			return false;
		}
		
	}
	
	/* Eliminar Fila */
	function fila_eliminar($tabla, $array_campo_pk, $array_valor_pk){		
		
		// Inicializar
		$condicion = '';
		
		// Armar condición
		if (count($array_campo_pk) > 0) {
			FOR($i=0; $i<count($array_campo_pk); $i++) {
				if($i == count($array_campo_pk)-1) {
					$condicion = $condicion.$array_campo_pk[$i]." = '".$array_valor_pk[$i]."' ";
				}else {
					$condicion = $condicion.$array_campo_pk[$i]." = '".$array_valor_pk[$i]."' AND ";
				}
			}
		}

		// Preparar query a ejecutar
		$this->query = "DELETE FROM $tabla WHERE $condicion";		
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);	
		
		// Retorno
		if($result){
			return true;
		}else{
			return false;
		}
		
	}
	
	/* Actualizar Fila */
	function fila_actualizar($tabla, $array_campo_pk, $array_valor_pk, $array_campo, $array_valor){		
	
		// Inicializar
		$cuerpo 	= '';
		$condicion 	= '';
	
		// Armar condición
		if (count($array_campo_pk) > 0) {
			FOR($i=0; $i<count($array_campo_pk); $i++) {
				if($i == count($array_campo_pk)-1) {
					$condicion = $condicion.$array_campo_pk[$i]." = '".$array_valor_pk[$i]."' ";
				}else {
					$condicion = $condicion.$array_campo_pk[$i]." = '".$array_valor_pk[$i]."' AND ";
				}
			}
		}
		
		// Armar cuerpo
		if (count($array_campo_pk) > 0) {
			FOR($j=0; $j<count($array_campo); $j++) {
				if($j == count($array_campo)-1) {
					$cuerpo = $cuerpo.$array_campo[$j]." = '".$array_valor[$j]."' ";
				}else {
					$cuerpo = $cuerpo.$array_campo[$j]." = '".$array_valor[$j]."',";
				}
			}
		}
		
		// Preparar query a ejecutar
		$this->query = "UPDATE $tabla SET $cuerpo WHERE $condicion";		
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);	
		
		// Retorno
		if($result){
			return true;
		}else{
			return false;
		}
		
	}
	
	/* Listar Filas */
	function fila_listar($tabla, $array_campo_pk, $array_valor_pk, $campo_orden, $tipo_orden, $limite_ini, $limite_tamano){
		
		// Inicializar
		$condicion = '';
		
		// Tipo de Ordern
		IF ($tipo_orden == 'D'){
			$t_orden = 'DESC';
		}ELSE{
			$t_orden = 'ASC';
		}
	
		// Armar condición
		if (count($array_campo_pk) > 0) {
			FOR($i=0; $i<count($array_campo_pk); $i++) {
				if($i == count($array_campo_pk)-1) {
					$condicion = $condicion.$array_campo_pk[$i]." = '".$array_valor_pk[$i]."' ";
				}else {
					$condicion = $condicion.$array_campo_pk[$i]." = '".$array_valor_pk[$i]."' AND ";
				}
			}
		}
		
		// Preparar query a ejecutar
		$this->query = "SELECT * FROM $tabla";	
		IF ($condicion || $condicion!=""){
			$this->query = $this->query." WHERE $condicion";
		}
		IF ($campo_orden || $campo_orden!=""){
			$this->query = $this->query." ORDER BY $campo_orden $t_orden";
		}
		IF ($limite_ini>=0){
			$this->query = $this->query." LIMIT $limite_ini, $limite_tamano";
		}		

		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();

		// Ejecutar Query
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Retornar
		return $result;
		
	}	

	/* Listar Filas Condición */
	function fila_listar_condicion($tabla, $condicion, $campo_orden, $tipo_orden, $limite_ini, $limite_tamano){
				
		// Tipo de Ordern
		IF ($tipo_orden == 'D'){
			$t_orden = 'DESC';
		}ELSE{
			$t_orden = 'ASC';
		}
		
		// Preparar query a ejecutar
		$this->query = "SELECT * FROM $tabla WHERE $condicion";	
		IF ($campo_orden || $campo_orden!=""){
			$this->query = $this->query." ORDER BY $campo_orden $t_orden";
		}
		IF ($limite_ini>=0){
			$this->query = $this->query." LIMIT $limite_ini, $limite_tamano";
		}		

		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		
		// Ejecutar Query
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Retornar
		return $result;
		
	}	

	/* Listar Filas Solo Condición */
	function fila_listar_solocondicion($condicion, $campo_orden, $tipo_orden, $limite_ini, $limite_tamano){
				
		// Tipo de Ordern
		IF ($tipo_orden == 'D'){
			$t_orden = 'DESC';
		}ELSE{
			$t_orden = 'ASC';
		}
		
		// Preparar query a ejecutar
		$this->query = "SELECT * FROM $condicion";	
		IF ($campo_orden || $campo_orden!=""){
			$this->query = $this->query." ORDER BY $campo_orden $t_orden";
		}
		IF ($limite_ini>=0){
			$this->query = $this->query." LIMIT $limite_ini, $limite_tamano";
		}		

		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		
		// Ejecutar Query
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Retornar
		return $result;
		
	}	

	/* Listar Filas Con IN*/
	function fila_listar_in($tabla, $campo_pk, $array_valor_pk, $campo_orden, $tipo_orden, $limite_ini, $limite_tamano){
		
		// Inicializar
		$condicion = '';
		
		// Tipo de Ordern
		IF ($tipo_orden == 'D'){
			$t_orden = 'DESC';
		}ELSE{
			$t_orden = 'ASC';
		}
	
		// Armar condición
		if (count($array_valor_pk) > 0) {
			FOR($i=0; $i<count($array_valor_pk); $i++) {
				if($i == count($array_valor_pk)-1) {
					$condicion = $condicion." '".$array_valor_pk[$i]."' ";
				}else {
					$condicion = $condicion." '".$array_valor_pk[$i]."', ";
				}
			}
		}
		$condicion = $campo_pk.' IN ('.$condicion.')';
		
		// Preparar query a ejecutar
		$this->query = "SELECT * FROM $tabla";	
		IF ($condicion || $condicion!=""){
			$this->query = $this->query." WHERE $condicion";
		}
		IF ($campo_orden || $campo_orden!=""){
			$this->query = $this->query." ORDER BY $campo_orden $t_orden";
		}
		IF ($limite_ini>=0){
			$this->query = $this->query." LIMIT $limite_ini, $limite_tamano";
		}		
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		
		// Ejecutar Query
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Retornar
		return $result;
		
	}	
	
	/* Listar Filas NOT IN */
	function fila_listar_not_in($tabla, $array_campo_pk, $array_valor_pk, $tabla_ref, $campo_ref, $campo_padre, $valor_padre, $campo_orden, $tipo_orden, $limite_ini, $limite_tamano){
		
		// Inicializar
		$condicion = '';
		
		// Tipo de Ordern
		IF ($tipo_orden == 'D'){
			$t_orden = 'DESC';
		}ELSE{
			$t_orden = 'ASC';
		}
	
		// Armar condición
		if (count($array_campo_pk) > 0) {
			FOR($i=0; $i<count($array_campo_pk); $i++) {
				if($i == count($array_campo_pk)-1) {
					$condicion = $condicion.$array_campo_pk[$i]." = '".$array_valor_pk[$i]."' ";
				}else {
					$condicion = $condicion.$array_campo_pk[$i]." = '".$array_valor_pk[$i]."' AND ";
				}
			}
		}
		IF ($tabla_ref || $tabla_ref!=""){
			$condicion = $condicion." AND not EXISTS (select 1 from $tabla_ref r where r.$campo_ref = x.$campo_ref and r.$campo_padre = $valor_padre)";
		}
		
		// Preparar query a ejecutar
		$this->query = "SELECT * FROM $tabla x";	
		IF ($condicion || $condicion!=""){
			$this->query = $this->query." WHERE $condicion";
		}
		IF ($campo_orden || $campo_orden!=""){
			$this->query = $this->query." ORDER BY $campo_orden $t_orden";
		}
		IF ($limite_ini>=0){
			$this->query = $this->query." LIMIT $limite_ini, $limite_tamano";
		}		
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		
		// Ejecutar Query
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Retornar
		return $result;
		
	}	

	/* Recuperar Fila */
	function fila_recuperar($tabla, $array_campo_pk, $array_valor_pk){
		
		// Inicializar
		$condicion 	= '';
		
		// Armar condición
		if (count($array_campo_pk) > 0) {
			FOR($i=0; $i<count($array_campo_pk); $i++) {
				if($i == count($array_campo_pk)-1) {
					$condicion = $condicion.$array_campo_pk[$i]." = '".$array_valor_pk[$i]."' ";
				}else {
					$condicion = $condicion.$array_campo_pk[$i]." = '".$array_valor_pk[$i]."' AND ";
				}
			}
		}

		// Preparar query a ejecutar
		$this->query = "SELECT * FROM $tabla WHERE $condicion";
				
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Contar registros
		$num_result = $result->num_rows;
		
		// Validar una fila
		If ($num_result <> 1) {
			return null;
		}
		
		// Obtener fila en arreglo
		$row = $result->fetch_array(MYSQLI_ASSOC);
		
		/* Cerrar el resultset */
		$result->close();
		
		// Retornar
		return $row;
		
	}	
	
	/* Listar Filas */
	function fila_recuperar_campo($tabla, $array_campo_pk, $array_valor_pk, $campo){

		// Inicializar
		$condicion 	= '';
		
		// Armar condición
		if (count($array_campo_pk) > 0) {
			FOR($i=0; $i<count($array_campo_pk); $i++) {
				if($i == count($array_campo_pk)-1) {
					$condicion = $condicion.$array_campo_pk[$i]." = '".$array_valor_pk[$i]."' ";
				}else {
					$condicion = $condicion.$array_campo_pk[$i]." = '".$array_valor_pk[$i]."' AND ";
				}
			}
		}

		// Preparar query a ejecutar
		$this->query = "SELECT $campo FROM $tabla WHERE $condicion";	
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Recuperar dato específico
		$result = $bd->bd_mysqli_result($result, 0 , $campo);
				
		// Retornar
		return $result;
		
	}	
	
	/* Recuperar Inidice de Fila */	
	function fila_recuperar_indice($tabla, $campo_pk){
		
		// Preparar query a ejecutar
		$this->query = "SELECT (MAX($campo_pk)+1) AS $campo_pk FROM $tabla";
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Recuperar dato específico
		$result = $bd->bd_mysqli_result($result, 0 , $campo_pk);
		
		// Retornar
		return $result;
		
	}
	
	/* Recuperar Inidice de Tabla */	
	function fila_recuperar_lastId($tabla, $campo_pk){
		
		// Preparar query a ejecutar
		$this->query = "SELECT MAX($campo_pk) AS $campo_pk FROM $tabla";
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Recuperar dato específico
		$result = $bd->bd_mysqli_result($result, 0 , $campo_pk);
		
		// Retornar
		return $result;
		
	}
	
	/* Recuperar Inidice de Tabla según condición*/	
	function fila_recuperar_lastIdPar($tabla, $array_campo_pk, $array_valor_pk, $campo_pk){
		
		// Inicializar
		$condicion 	= '';
		
		// Armar condición
		if (count($array_campo_pk) > 0) {
			FOR($i=0; $i<count($array_campo_pk); $i++) {
				if($i == count($array_campo_pk)-1) {
					$condicion = $condicion.$array_campo_pk[$i]." = '".$array_valor_pk[$i]."' ";
				}else {
					$condicion = $condicion.$array_campo_pk[$i]." = '".$array_valor_pk[$i]."' AND ";
				}
			}
		}
		
		// Preparar query a ejecutar
		$this->query = "SELECT (MAX($campo_pk)) AS $campo_pk FROM $tabla";
		
		IF ($condicion || $condicion!=""){
			$this->query = $this->query." WHERE $condicion";
		}
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Recuperar dato específico
		$result = $bd->bd_mysqli_result($result, 0 , $campo_pk);
		
		// Retornar
		return $result;
		
	}
	
	/* Recuperar número de Filas */	
	function fila_contar($tabla, $array_campo_pk, $array_valor_pk){
		
		// Inicializar
		$condicion 	= '';
		
		// Armar condición
		if (count($array_campo_pk) > 0) {
			for($i=0; $i<count($array_campo_pk); $i++) {
				if($i == count($array_campo_pk)-1) {
					$condicion = $condicion.$array_campo_pk[$i]." = '".$array_valor_pk[$i]."' ";
				}else {
					$condicion = $condicion.$array_campo_pk[$i]." = '".$array_valor_pk[$i]."' AND ";
				}
			}
		}
		
		// Preparar query a ejecutar
		$this->query = "SELECT * FROM $tabla";	
		IF ($condicion || $condicion!=""){
			$this->query = $this->query." WHERE $condicion";
		}
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Contar registros
		if ($result) {
			$num_result = $result->num_rows;
		}else{
			$num_result = 0;
		}

		/* Cerrar el resultset */
		//$result->close();
			
		// Retornar
		return $num_result;
		
	}

	/* Ejecutar Query */
	function ejecutar_sp($as_query){		

		// Preparar query a ejecutar
		$this->query = $as_query;		
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);	
		
		// Retorno
		if($result){
			return true;
		}else{
			return false;
		}
		
	}
	
	/* Método para validar permiso */
	public function f_usuario_acceso($usuario, $opcion){
				
		// Validar 
		if( !empty( $usuario ) ){
		
			// Sql a ejecutar
			$this->query = "SELECT * FROM MAE_ROL_USUARIO rxu, MAE_ROL_OPCION rxo WHERE rxu.V_COD_ROL = rxo.V_COD_ROL AND rxu.V_FLAG_ESTADO = '1' AND rxo.V_FLAG_ESTADO = '1' AND rxu.V_COD_USER = '$usuario' AND rxo.N_COD_OPCION = '$opcion'";
						
			// Invocar ejecución de query en la B.D
			$bd = new baseDatos();
			$result = $bd->bd_ejecutarQuery($this->query);
			
			// Validar
			if (!$result)
				return 0;

			// Contar registros
			$num_result = $result->num_rows;
			
			/* Cerrar el resultset */
			$result->close();
		
			// Según caso
			if( $num_result > 0 ){
				return 1; // Cuenta con acceso
			}else{	
				return 0; // No cuenta con acceso
			}
		} else{
			return 0;
		}
	
	}
	
	// ======================================================= //
	// R E G L A S  D E  N E G O C I O
	// ======================================================= //
	
	// Función para obtener datos de empresa
	function f_get_param($as_cod_campo){

		// Preparar query a ejecutar
		$this->query = "SELECT $as_cod_campo FROM MAE_EMPRESA where v_id = '1'";	
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Recuperar dato específico
		$ls_return = $bd->bd_mysqli_result($result, 0 , $as_cod_campo);

		// Retornar
		return $ls_return;
		
	}

	// Función para obtener Cinturón Actual
	function f_get_cinturonActual($an_id_estudiante){

		// Preparar query a ejecutar
		$this->query = "SELECT x.N_COD_CINTURON AS N_COD_CINTURON FROM MAE_CINTURON x WHERE x.N_ORDEN = (
			SELECT MAX(c.N_ORDEN) FROM MAE_ESTUDIANTE_CINTURON ec, MAE_CINTURON c 
			 WHERE ec.N_COD_CINTURON = c.N_COD_CINTURON AND ec.V_FLAG_ESTADO = 1 AND c.V_FLAG_ESTADO = 1 
			   AND ec.N_COD_ESTUDIANTE = $an_id_estudiante)";	
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Recuperar dato específico
		$li_return = $bd->bd_mysqli_result($result, 0 , 'N_COD_CINTURON');

		// Retornar
		return $li_return;
		
	}

	// Función para obtener Cinturón Nuevo
	function f_get_cinturonNuevo($an_id_estudiante){

		// Preparar query a ejecutar
		$this->query = "SELECT x.N_COD_CINTURON AS N_COD_CINTURON FROM MAE_CINTURON x WHERE x.N_ORDEN = (
			SELECT MAX(c.N_ORDEN)+1 FROM MAE_ESTUDIANTE_CINTURON ec, MAE_CINTURON c 
			 WHERE ec.N_COD_CINTURON = c.N_COD_CINTURON AND ec.V_FLAG_ESTADO = 1 AND c.V_FLAG_ESTADO = 1 
			   AND ec.N_COD_ESTUDIANTE = $an_id_estudiante)";	
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Recuperar dato específico
		$li_return = $bd->bd_mysqli_result($result, 0 , 'N_COD_CINTURON');

		// Retornar
		return $li_return;
		
	}

	/* Recuperar número de Filas */	
	function f_get_cinturones($an_id_cinturon){
		
		// Preparar query a ejecutar
		$this->query = "SELECT *
						FROM (
						SELECT x.N_COD_ESTUDIANTE, 
								(
								SELECT x.N_COD_CINTURON AS N_COD_CINTURON FROM MAE_CINTURON x WHERE x.N_ORDEN = (
									SELECT MAX(c.N_ORDEN) FROM MAE_ESTUDIANTE_CINTURON ec, MAE_CINTURON c 
									WHERE ec.N_COD_CINTURON = c.N_COD_CINTURON AND ec.V_FLAG_ESTADO = 1 AND c.V_FLAG_ESTADO = 1 
									AND ec.N_COD_ESTUDIANTE = x.N_COD_ESTUDIANTE)
								) AS N_CINTURON
						FROM MAE_ESTUDIANTE_CINTURON x,
							MAE_ESTUDIANTE e
						WHERE x.N_COD_ESTUDIANTE = e.N_COD_ESTUDIANTE
						AND x.V_FLAG_ESTADO = '1'
						AND e.V_FLAG_ESTADO = '1'
						AND e.V_TIPO_EST = 'EST_INT'
						GROUP BY x.N_COD_ESTUDIANTE
						) t
						WHERE t.N_CINTURON = $an_id_cinturon";	

		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Contar registros
		if ($result) {
			$num_result = $result->num_rows;
		}else{
			$num_result = 0;
		}

		/* Cerrar el resultset */
		//$result->close();
			
		// Retornar
		return $num_result;
		
	}

	/* Recuperar número de Filas */	
	function f_get_edadesxcategoria($an_anios_min, $an_anios_max){
		
		// Preparar query a ejecutar
		$this->query = "SELECT T.*
							FROM (
								SELECT e.N_COD_ESTUDIANTE, (YEAR(CURDATE())-YEAR(e.D_FEC_NACIMIENTO)) AS N_EDAD
								FROM MAE_ESTUDIANTE e 
								WHERE e.V_TIPO_EST = 'EST_INT'
								AND e.V_FLAG_ESTADO = '1'
							) T
							WHERE (T.N_EDAD >= $an_anios_min AND T.N_EDAD <= $an_anios_max) ";	

		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Contar registros
		if ($result) {
			$num_result = $result->num_rows;
		}else{
			$num_result = 0;
		}

		/* Cerrar el resultset */
		//$result->close();
			
		// Retornar
		return $num_result;
		
	}

	// Función para obtener Asistencia de Estudiante en Horario x Clase
	function f_get_asistEstxClase($an_cod_horario, $an_cod_clase, $an_id_estudiante){

		// Inicializar
		$ls_asistencia	= '';
		$ls_tipoasist 	= '';

		// Preparar query a ejecutar
		$this->query = "SELECT x.V_FLAG_ESTADO, x.N_COD_TIPOASIST FROM MOV_ASISTENCIA x 
						 WHERE x.n_cod_horario = $an_cod_horario 
						   AND x.n_clase = $an_cod_clase 
						   AND x.n_cod_estudiante = $an_id_estudiante";	
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);

		// Recuperar dato específico
		$n_filas = $result->num_rows;
		if($n_filas > 0){
			while ($row = mysqli_fetch_assoc($result)) {	
				$ls_asistencia 	= $row['V_FLAG_ESTADO'];
				$ls_tipoasist 	= $row['N_COD_TIPOASIST'];
			}
		}	

		// Según Caso
		if($ls_asistencia == '1'){
			$ls_asistencia = 'S';
		}elseif($ls_asistencia == '-1'){
			$ls_asistencia = 'N';
			if($ls_tipoasist == 3){
				$ls_asistencia = 'J';
			}
		}else{
			$ls_asistencia = '-';
		}
		
		// Retornar
		return $ls_asistencia;
		
	}

	// Función para obtener saldo pendiente
	function f_get_saldoPendiente($an_id_matricula){

		// ======================================================= //
		// CAPTURAR MONTO DE MATRICULA
		// ======================================================= //

		// Preparar query a ejecutar
		$this->query = "SELECT N_MONTO_NETO FROM MOV_MATRICULA WHERE N_COD_MATRICULA = $an_id_matricula";	
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Recuperar dato específico
		$ldc_monto_neto = $bd->bd_mysqli_result($result, 0 , 'N_MONTO_NETO');

		// ======================================================= //
		// CAPTURAR PAGOS EFECTUADOS
		// ======================================================= //
		$campo_pk = 'N_MONTO';
		
		// Preparar query a ejecutar
		$this->query = "SELECT SUM($campo_pk) AS $campo_pk FROM MOV_PAGO WHERE V_FLAG_ESTADO = '1' AND N_COD_MATRICULA = $an_id_matricula";	
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Recuperar dato específico
		$ldc_monto_pagago = $bd->bd_mysqli_result($result, 0 , $campo_pk);
		
		// ======================================================= //
		// CALCULAR PENDIENTE
		// ======================================================= //
		$ldc_monto_pendiente = $ldc_monto_neto - $ldc_monto_pagago;

		// Retornar
		return $ldc_monto_pendiente;
		
	}

	// Función para actualizar saldo pendiente
	function f_update_saldoPendiente($an_id_matricula){

		// ======================================================= //
		// CAPTURAR MONTO DE MATRICULA
		// ======================================================= //

		// Preparar query a ejecutar
		$this->query = "SELECT N_MONTO_NETO FROM MOV_MATRICULA WHERE N_COD_MATRICULA = $an_id_matricula";	
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Recuperar dato específico
		$ldc_monto_neto = $bd->bd_mysqli_result($result, 0 , 'N_MONTO_NETO');

		// ======================================================= //
		// CAPTURAR PAGOS EFECTUADOS
		// ======================================================= //
		$campo_pk = 'N_MONTO';
		
		// Preparar query a ejecutar
		$this->query = "SELECT SUM($campo_pk) AS $campo_pk FROM MOV_PAGO WHERE V_FLAG_ESTADO = '1' AND N_COD_MATRICULA = $an_id_matricula";	
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Recuperar dato específico
		$ldc_monto_pagago = $bd->bd_mysqli_result($result, 0 , $campo_pk);
		
		// ======================================================= //
		// CALCULAR PENDIENTE
		// ======================================================= //
		$ldc_monto_pendiente = $ldc_monto_neto - $ldc_monto_pagago;
		if ($ldc_monto_pendiente < 0) {
			$ldc_monto_pendiente = 0;
		}

		// Según Caso
		if ($ldc_monto_pendiente == 0) {
			$ls_check_pago = '1';
		}else{
			$ls_check_pago = '0';
		}	
		// ======================================================= //
		// ACTUALIZAR SALD0 PENDIENTE
		// ======================================================= //
		// Preparar query a ejecutar
		$this->query = "UPDATE MOV_MATRICULA SET v_flag_pago = $ls_check_pago WHERE N_COD_MATRICULA = $an_id_matricula";		

		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);	
		
		// Retorno
		if($result){
			return true;
		}else{
			return false;
		}
		
	}

	// Recuperar Horarios X Estudiante
	function fila_recuperar_horariosxEst($an_cod_estudiante){
		
		// Preparar query a ejecutar
		$this->query = "SELECT * 
						  FROM MOV_HORARIO H 
						 WHERE H.V_FLAG_ESTADO IN (2, 3) AND 
								EXISTS ( SELECT 1 FROM MOV_MATRICULA M 
										WHERE M.N_COD_HORARIO = H.N_COD_HORARIO 
											AND M.V_FLAG_ESTADO = 1
											AND M.N_COD_ESTUDIANTE = $an_cod_estudiante
										) 
						ORDER BY H.D_FEC_INICIO DESC";
				
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		
		// Ejecutar Query
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Retornar
		return $result;
		
	}	

	// Recuperar Matrícula X Estudiante
	function fila_recuperar_matriculaxEst($an_cod_estudiante){
		
		// Preparar query a ejecutar
		$this->query = "SELECT * 
						  FROM MOV_MATRICULA M 
						 WHERE M.V_FLAG_ESTADO = 1
						   AND M.N_COD_ESTUDIANTE = $an_cod_estudiante
						ORDER BY M.D_FEC_MATRICULA DESC";
				
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		
		// Ejecutar Query
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Retornar
		return $result;
		
	}	
	
	// Función para obtener Datos del Estudiante Para el FrontEnd
	function f_get_datosEstudiante($an_id_estudiante, $as_valor){

		// Preparar query a ejecutar
		IF ($as_valor == 'N_DEUDAS'){
			$this->query = "SELECT SUM(
										MAT.N_MONTO_NETO - NVL((SELECT SUM(P.N_MONTO)
															FROM MOV_PAGO P 
															WHERE P.N_COD_MATRICULA = MAT.N_COD_MATRICULA 
															AND P.V_FLAG_ESTADO = '1'),0)
										) AS N_DEUDAS
								FROM MOV_MATRICULA MAT
								WHERE MAT.N_COD_ESTUDIANTE = $an_id_estudiante
									AND MAT.V_FLAG_ESTADO = '1'
									AND MAT.V_FLAG_PAGO = '0'";
		}ELSE {
			$this->query = "SELECT h.N_COD_HORARIO AS N_COD_HORARIO, MIN(p.D_FEC_PROG) AS D_PROX_FECHA, MAX(p.D_HORA_PROG) AS D_PROX_HORA,
									(SELECT COUNT(1)
									FROM MOV_ASISTENCIA a 
									WHERE a.N_COD_HORARIO = h.N_COD_HORARIO 
										AND a.N_COD_ESTUDIANTE = m.N_COD_ESTUDIANTE
										AND a.V_FLAG_ESTADO = '-1' ) AS N_FALTAS
							FROM MOV_MATRICULA m, MOV_HORARIO h, MOV_HORARIO_PROG p 
							WHERE m.N_COD_HORARIO = h.N_COD_HORARIO
							AND h.N_COD_HORARIO = p.N_COD_HORARIO
							AND m.V_FLAG_ESTADO = '1'
							AND h.V_FLAG_ESTADO = '2'
							AND m.N_COD_ESTUDIANTE = $an_id_estudiante
							AND p.D_FEC_PROG >= CURDATE()
							GROUP BY h.N_COD_HORARIO";	
		}

		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Recuperar dato específico
		$li_return = $bd->bd_mysqli_result($result, 0 , $as_valor);

		// Retornar
		return $li_return;
		
	}
	// ======================================================= //
	// R E P O R T E S
	// ======================================================= //
	
	// Reporte de Estudiantes x Sede
	function fila_rpt_estxsede($an_cod_sede, $an_cod_cinturon){
		
		// Operador para Sede
		IF ($an_cod_sede!="%"){
			$ls_operador = '=';
		}ELSE{
			$ls_operador = 'LIKE';
		}	

		// Preparar query a ejecutar
		$this->query = "SELECT * FROM MAE_ESTUDIANTE E WHERE E.V_TIPO_EST = 'EST_INT' AND E.N_COD_SEDE $ls_operador '$an_cod_sede'";	
		
		// Cinturon
		IF ($an_cod_cinturon!="%"){
			$this->query = $this->query." AND (SELECT x.N_COD_CINTURON AS N_COD_CINTURON FROM MAE_CINTURON x WHERE x.N_ORDEN = (
				SELECT MAX(c.N_ORDEN) FROM MAE_ESTUDIANTE_CINTURON ec, MAE_CINTURON c 
				 WHERE ec.N_COD_CINTURON = c.N_COD_CINTURON AND ec.V_FLAG_ESTADO = 1 AND c.V_FLAG_ESTADO = 1 
				   AND ec.N_COD_ESTUDIANTE = E.N_COD_ESTUDIANTE) ) = $an_cod_cinturon";
		}
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		
		// Ejecutar Query
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Retornar
		return $result;
		
	}	

	// Reporte de Matriculados x Horario
	function fila_rpt_matxhorario($an_cod_horario){

		// Matriculados
		IF ($an_cod_horario!="%"){
			$this->query = " SELECT x.* FROM MOV_MATRICULA x WHERE x.n_cod_horario = $an_cod_horario";
		}
		
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		
		// Ejecutar Query
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Retornar
		return $result;
		
	}	

	// Reporte de Créditos
	function fila_rpt_creditos($an_cod_sede, $an_cod_estudiante, $an_cod_estado){
		
		// Operador para Estudiante
		IF ($an_cod_estudiante!="%"){
			$ls_operador_est = '=';
		}ELSE{
			$ls_operador_est = 'LIKE';
		}	

		// Operador para Estado
		IF ($an_cod_estado!="%"){
			$ls_operador_estado = '=';
		}ELSE{
			$ls_operador_estado = 'LIKE';
		}	

		// Preparar query a ejecutar
		$this->query = "SELECT M.* FROM MOV_MATRICULA M, MOV_HORARIO H WHERE M.N_COD_HORARIO = H.N_COD_HORARIO 
						AND M.V_FLAG_ESTADO !='0' AND H.V_FLAG_ESTADO !='0' AND M.N_COD_FORMAPAGO = '2'
						AND H.N_COD_SEDE = '$an_cod_sede' 
						AND M.N_COD_ESTUDIANTE $ls_operador_est '$an_cod_estudiante' 
						AND M.V_FLAG_PAGO $ls_operador_estado '$an_cod_estado'";	
	
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		
		// Ejecutar Query
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Retornar
		return $result;
		
	}
    
    // Reporte de Traslados de Sede Pendientes
	function fila_rpt_trasladospend($an_cod_sede){
		
		// Operador para Sede
		IF ($an_cod_sede!="%"){
			$ls_operador = '=';
		}ELSE{
			$ls_operador = 'LIKE';
		}	

		// Preparar query a ejecutar
		$this->query = "SELECT * FROM MAE_ESTUDIANTE_SEDE E WHERE E.V_FLAG_ESTADO = '1' AND E.D_FEC_FIN IS NOT NULL AND E.N_COD_SEDE_DESTINO $ls_operador '$an_cod_sede'
		    AND 0 = (SELECT COUNT(1) FROM MAE_ESTUDIANTE_SEDE T WHERE T.V_FLAG_ESTADO = '1' AND T.N_COD_ESTUDIANTE = E.N_COD_ESTUDIANTE AND T.D_FEC_INICIO > E.D_FEC_INICIO)";	

		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		
		// Ejecutar Query
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Retornar
		return $result;
		
	}	
	
	// Reporte de Cuadre de Ingresos
	function fila_rpt_cuadreingresos($an_cod_sede, $adt_fecha){
		
		// Operador para Sede
		IF ($an_cod_sede!="%"){
			$ls_operador = '=';
		}ELSE{
			$ls_operador = 'LIKE';
		}	

		// Preparar query a ejecutar
		$this->query = "SELECT M.D_FEC_MATRICULA AS D_FECHA,
                        	   'MATRICULA' AS V_TIPO,
                        	   M.N_COD_MATRICULA, 
                        	   M.D_FEC_MATRICULA AS D_FEC_MATRICULA, 
                        	   M.N_COD_HORARIO, 
                        	   H.V_DESCRIPCION AS HORARIO, 
                        	   H.D_HORA_INICIO AS HORA,
                        	   M.N_COD_ESTUDIANTE AS N_COD_ESTUDIANTE,
                        	   E.V_NRO_DOC, CONCAT(E.V_APE_PATERNO, CONCAT(' ', CONCAT(E.V_APE_MATERNO,CONCAT(' ',E.V_NOMBRES)))) AS DATOS,
                        	   M.N_MONTO_TARIFA, 
                        	   M.N_MONTO_DSCTO, 
                        	   M.N_MONTO_NETO AS N_MONTO_PAGADO,
                               FP.V_DES_CORTA AS DES_FORMAPAGO,
                               MP.V_DES_CORTA AS DES_MEDIOPAGO,
                        	   M.V_AUD_USR_REG, M.D_AUD_FEC_REG
                        FROM MOV_MATRICULA M 
                        INNER JOIN MOV_HORARIO H ON M.N_COD_HORARIO = H.N_COD_HORARIO
                        INNER JOIN MAE_ESTUDIANTE E ON M.N_COD_ESTUDIANTE = E.N_COD_ESTUDIANTE
                        LEFT JOIN MAE_MEDIO_PAGO MP ON M.N_COD_MEDIOPAGO = MP.N_COD_MEDIOPAGO
                        LEFT JOIN MAE_FORMA_PAGO FP ON M.N_COD_FORMAPAGO = FP.N_COD_FORMAPAGO
                        WHERE M.V_FLAG_ESTADO = '1'
                        AND M.N_COD_FORMAPAGO = '1'
                        AND H.N_COD_SEDE $ls_operador '$an_cod_sede'
                        AND M.D_FEC_MATRICULA = '$adt_fecha'
                        UNION ALL
                        -- PAGOS DE CRÉDITOS
                        SELECT P.D_FEC_PAGO AS D_FECHA,
                        	   'PAGO CRÉDITOS' AS V_TIPO,
                        	   M.N_COD_MATRICULA, 
                        	   M.D_FEC_MATRICULA AS D_FEC_MATRICULA, 
                        	   M.N_COD_HORARIO, 
                        	   H.V_DESCRIPCION AS HORARIO, 
                        	   H.D_HORA_INICIO AS HORA,
                               M.N_COD_ESTUDIANTE,
                        	   E.V_NRO_DOC, CONCAT(E.V_APE_PATERNO, CONCAT(' ', CONCAT(E.V_APE_MATERNO,CONCAT(' ',E.V_NOMBRES)))) AS DATOS,
                        	   M.N_MONTO_TARIFA, 
                        	   M.N_MONTO_DSCTO, 
                        	   P.N_MONTO AS N_MONTO_PAGADO,
                        	   FP.V_DES_CORTA AS DES_FORMAPAGO,
                               MP.V_DES_CORTA AS DES_MEDIOPAGO, 
                        	   P.V_AUD_USR_REG,
                               P.D_AUD_FEC_REG
                        FROM MOV_PAGO P
                        INNER JOIN MOV_MATRICULA M ON P.N_COD_MATRICULA = M.N_COD_MATRICULA
                        INNER JOIN MOV_HORARIO H ON M.N_COD_HORARIO = H.N_COD_HORARIO
                        INNER JOIN MAE_ESTUDIANTE E ON M.N_COD_ESTUDIANTE = E.N_COD_ESTUDIANTE
                        LEFT JOIN MAE_MEDIO_PAGO MP ON M.N_COD_MEDIOPAGO = MP.N_COD_MEDIOPAGO
                        LEFT JOIN MAE_FORMA_PAGO FP ON M.N_COD_FORMAPAGO = FP.N_COD_FORMAPAGO
                        WHERE H.N_COD_SEDE $ls_operador '$an_cod_sede'
                        AND P.D_FEC_PAGO = '$adt_fecha'
                        AND P.V_FLAG_ESTADO != '0'";	
   	    
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		
		// Ejecutar Query
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Retornar
		return $result;
		
	}
	
	// Reporte de Ingresos por Periodo
	function fila_rpt_ingresosxperiodo($an_cod_sede, $adt_fini, $adt_ffin){
		
		// Preparar query a ejecutar
		/*$this->query = "SELECT X.FECHA, 
								NVL((SELECT SUM(MA.N_MONTO_NETO) FROM MOV_MATRICULA MA, MOV_HORARIO HO
									WHERE MA.N_COD_HORARIO = HO.N_COD_HORARIO 
										AND HO.N_COD_SEDE = '$an_cod_sede' 
										AND MA.D_FEC_MATRICULA = X.FECHA
										AND MA.N_COD_FORMAPAGO = '1'
										AND MA.V_FLAG_ESTADO != '0'), 0) AS MONTO_PAGO_CONTADO,
								NVL((SELECT SUM(PA.N_MONTO) FROM MOV_PAGO PA, MOV_MATRICULA MA, MOV_HORARIO HO
								WHERE PA.N_COD_MATRICULA = MA.N_COD_MATRICULA
									AND MA.N_COD_HORARIO = HO.N_COD_HORARIO
									AND HO.N_COD_SEDE = '$an_cod_sede' 
									AND PA.D_FEC_PAGO = X.FECHA
									AND PA.V_FLAG_ESTADO != '0'), 0) AS MONTO_PAGO_CREDITO	   
						FROM
						(SELECT DISTINCT P.D_FEC_PAGO AS FECHA
						FROM MOV_PAGO P, MOV_MATRICULA M, MOV_HORARIO H
						WHERE P.N_COD_MATRICULA = M.N_COD_MATRICULA
							AND M.N_COD_HORARIO = H.N_COD_HORARIO
							AND H.N_COD_SEDE = '$an_cod_sede' 
							AND P.D_FEC_PAGO BETWEEN '$adt_fini' AND '$adt_ffin'
							AND P.V_FLAG_ESTADO != '0'
						UNION
						SELECT DISTINCT M.D_FEC_MATRICULA AS FECHA
						FROM MOV_MATRICULA M, MOV_HORARIO H
						WHERE M.N_COD_HORARIO = H.N_COD_HORARIO
							AND M.N_COD_FORMAPAGO = '1'
							AND M.V_FLAG_ESTADO = '1'
							AND H.N_COD_SEDE = '$an_cod_sede' 
							AND M.D_FEC_MATRICULA BETWEEN '$adt_fini' AND '$adt_ffin'
						) X
						ORDER BY X.FECHA";	*/
	    
	    // Preparar query a ejecutar (Nuevo)
		$this->query = "SELECT Z.FECHA, 
                            	   GROUP_CONCAT(IF(Z.N_COD_MEDIOPAGO = 1, Z.MONTO, NULL)) MONTO_EFECTIVO,
                            	   GROUP_CONCAT(IF(Z.N_COD_MEDIOPAGO = 2, Z.MONTO, NULL)) MONTO_TRANSFERENCIA,
                                   GROUP_CONCAT(IF(Z.N_COD_MEDIOPAGO = 3, Z.MONTO, NULL)) MONTO_TARJETA,
                                   GROUP_CONCAT(IF(Z.N_COD_MEDIOPAGO = 4, Z.MONTO, NULL)) MONTO_PAGOMOVIL
                            FROM (
                            SELECT X.FECHA, X.N_COD_MEDIOPAGO, ROUND(SUM(X.MONTO),2) AS MONTO
                            FROM 
                            (
                            SELECT M.D_FEC_MATRICULA AS FECHA, M.N_COD_MEDIOPAGO, M.N_MONTO_NETO AS MONTO
                            FROM MOV_MATRICULA M, MOV_HORARIO H
                            WHERE M.N_COD_HORARIO = H.N_COD_HORARIO
                            AND M.N_COD_FORMAPAGO = '1'
                            AND M.V_FLAG_ESTADO = '1'
                            AND H.N_COD_SEDE = '$an_cod_sede' 
                            AND M.D_FEC_MATRICULA BETWEEN '$adt_fini' AND '$adt_ffin'
                            UNION ALL
                            SELECT P.D_FEC_PAGO AS FECHA, P.N_COD_MEDIOPAGO, P.N_MONTO AS MONTO
                            FROM MOV_PAGO P, MOV_MATRICULA M, MOV_HORARIO H
                            WHERE P.N_COD_MATRICULA = M.N_COD_MATRICULA
                            AND M.N_COD_HORARIO = H.N_COD_HORARIO
                            AND H.N_COD_SEDE = '$an_cod_sede' 
                            AND P.D_FEC_PAGO BETWEEN '$adt_fini' AND '$adt_ffin'
                            AND P.V_FLAG_ESTADO != '0'
                            ) X
                            GROUP BY X.FECHA, X.N_COD_MEDIOPAGO
                            ) Z
                            GROUP BY Z.FECHA
                            ORDER BY Z.FECHA";
						
		// Invocar ejecución de query en la B.D
		$bd = new baseDatos();
		
		// Ejecutar Query
		$result = $bd->bd_ejecutarQuery($this->query);
		
		// Retornar
		return $result;
		
	}

}

?>