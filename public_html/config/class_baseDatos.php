<?php
/*
Proyecto 	: Sistema web - Backend 
Fecha 		: 2026-04-01
Autor 		: Johnny Reque
Proposito	: Clase con funciones de Login
*/
class baseDatos
{
	
	/* Propiedades de la clase */
	private static	$bd_servidor = 'localhost';
	private static	$bd_usuario  = 'jmtalentgroup_centro_admin';
	private static 	$bd_clave    = '#Limaperu21';
	protected 		$bd_nombre   = 'jmtalentgroup_centro_bd';
	protected		$rows        =  array();
	private	   		$con;
	
	/* Constructor
	function __construct(){
		$this->bd_conectar();
	}*/
	
	/* Conectar con la B.D */
	public function bd_conectar(){
		$this->con = new mysqli(self::$bd_servidor, self::$bd_usuario, self::$bd_clave, $this->bd_nombre);
		if(mysqli_connect_error()){
			die("Conexión a la base de datos falló " . mysqli_connect_error() . mysqli_connect_errno());
		}else{
			if (!mysqli_set_charset($this->con, "utf8")) 
            {
              printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($this->con));
              exit();
            }
		}
	}
	
	// Metodo para obtener la conexion a la base de datos
    public function bd_conexion(){
		$this->bd_conectar();
        return $this->con;
    }
	
	/* Desconectar con la B.D */
	public function bd_desconectar(){
		$this->con->close();
	}
	
	/* Función para ejecutar Query */
	public function bd_ejecutarQuery($sql_instruccion){
		$this->bd_conectar();
		$result = mysqli_query($this->con, $sql_instruccion);		
		$this->bd_desconectar();
		return $result;
	}

	/* Función para buscar campo de fila específica */
	public function bd_mysqli_result($buscar, $fila, $campo){
		$result = '';
		$i=0; 
		while($results = @mysqli_fetch_array($buscar)){
			if ($i==$fila){
				$result=$results[$campo];
			}
			$i++;
		}
		return $result;
	}
	
	/* Función para escapar cadena */
	public function bd_escapeCadena($variable){
		$this->bd_conectar();
		$return = mysqli_real_escape_string($this->con, $variable);
		$this->bd_desconectar();
		return $return;
	}

}

?>