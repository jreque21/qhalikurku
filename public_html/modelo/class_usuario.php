<?php
/* Sentencias Sql */

// Incluye Clase BD
require_once("../config/class_baseDatos.php");
require_once("../config/conf_mensajes.php");

class usuario{

	/* Propiedades de la clase */
	private		   $con;

	/* Constructor */
	function __construct(){
		$bd = new baseDatos();
		$this->con = $bd->bd_conexion();
	}

	/* Método para iniciar sesión */
	public function f_usuario_login( array $data )
	{
		// Inicializar
		$_SESSION['logged_in'] = false;

		// Validar
		if( !empty( $data ) ){

			// Trim todos los datos entrantes:
			$trimmed_data = array_map('trim', $data);

			// Escapar de las variables para la seguridad
			$id_usuario = mysqli_real_escape_string( $this->con,  $trimmed_data['id_usuario'] );
			$id_clave = mysqli_real_escape_string( $this->con,  $trimmed_data['id_clave'] );

			if((!$id_usuario) || (!$id_clave) ) {
				throw new Exception( LOGIN_CAMPOS_FALTA );
			}
			if ($id_usuario != 'admweb'){
				$id_clave = md5( $id_clave );
			}

			// Sql a ejecutar
			$query = "SELECT V_COD_USER as usr_conectado, V_NOMBRES, V_EMAIL, N_COD_REFERENCIA, D_AUD_FEC_REG FROM MAE_USUARIO WHERE V_FLAG_ESTADO = '1' AND V_COD_TIPO IN ('USER_INST', 'USER_ADM', 'USER_EVAL') AND V_COD_USER = '$id_usuario' AND V_CLAVE = '$id_clave' ";

			$result = mysqli_query($this->con, $query);
			$data 	= mysqli_fetch_assoc($result);
			$count 	= mysqli_num_rows($result);
			mysqli_close($this->con);
			if( $count == 1){
				$_SESSION = $data;
				$_SESSION['logged_in'] = true;
				return true;
			}else{
				throw new Exception( LOGIN_ERROR );
			}
		} else{
			throw new Exception( LOGIN_CAMPOS_FALTA );
		}

	}

	/* El siguiente metodo para verificar los datos de la cuenta para el cambio de contraseña */
	public function f_usuario_cambioclave( array $data )
	{
		// Validar data
		if( !empty( $data ) ){

			// Trim todos los datos entrantes:
			$trimmed_data = array_map('trim', $data);

			// Escapar de las variables para la seguridad
			$id_clave  	= mysqli_real_escape_string( $this->con, $trimmed_data['id_clave'] );
			$id_clave2 	= $trimmed_data['id_clave2'];
			$id_usuario 	= mysqli_real_escape_string( $this->con, $trimmed_data['id_usuario'] );

			if((!$id_clave) || (!$id_clave2) ) {
				throw new Exception( FORM_CAMPOS_FALTA );
			}
			if ($id_clave !== $id_clave2) {
				throw new Exception( CLAVES_DIFERENTES );
			}
			$id_clave = md5( $id_clave );
			$query = "UPDATE MAE_USUARIO SET V_CLAVE = '$id_clave' WHERE V_COD_USER = '$id_usuario'";
			if(mysqli_query($this->con, $query)){
				mysqli_close($this->con);
				return true;
			}
		} else{
			throw new Exception( FORM_CAMPOS_FALTA );
		}

	}

	/* Este metodo para cerrar las sesión */
	public function f_usuario_logout()
	{
		session_unset();
		session_destroy();
		session_start();
		session_regenerate_id(true);
		header('Location: login_admin.php');
	}

	/* Esto restablece la contraseña con una nueva generada aleatoriamente y la envía al
	   correo que el propio usuario tiene registrado en su ficha (no se confía en ningún
	   correo escrito en el formulario, para evitar que alguien cambie la clave de otro
	   usuario indicando un correo distinto) */
	public function f_usuario_restauraclave( array $data )
	{
		// Validar data
		if( !empty( $data ) ){

			// Escapar de las variables para la seguridad
			$id_usuario = mysqli_real_escape_string( $this->con, trim( $data['id_usuario'] ) );

			// Validar usuario
			if((!$id_usuario) ) {
				throw new Exception( FORM_CAMPOS_FALTA );
			}

			// Verificar que el usuario exista, esté activo y tenga correo registrado
			$query = "SELECT V_EMAIL FROM MAE_USUARIO WHERE V_FLAG_ESTADO = '1' AND V_COD_TIPO IN ('USER_INST', 'USER_ADM', 'USER_EVAL') AND V_COD_USER = '$id_usuario'";
			$result = mysqli_query($this->con, $query);
			$row 	= mysqli_fetch_assoc($result);
			$count 	= mysqli_num_rows($result);

			if( $count != 1 || empty( $row['V_EMAIL'] ) ){
				mysqli_close($this->con);
				throw new Exception( RECUPERAR_ERROR );
			}

			$email = $row['V_EMAIL'];
			$id_clave = $this->f_usuario_clavealeatoria();
			$claveCifrada = ($id_usuario != 'admweb') ? md5( $id_clave ) : $id_clave;
			$query = "UPDATE MAE_USUARIO SET V_CLAVE = '$claveCifrada' WHERE V_COD_USER = '$id_usuario'";
			if(mysqli_query($this->con, $query)){
				mysqli_close($this->con);
				$to = $email;
				$subject = "Recuperación de contraseña - Qhali Kurku";
				$txt = "Hola,\r\n\r\nSu nueva contraseña de acceso es: ".$id_clave."\r\n\r\nPor seguridad, le recomendamos cambiarla apenas ingrese al sistema.\r\n\r\nCentro Terapéutico Qhali Kurku";
				$headers = "From: soporte@qhalikurku.com" . "\r\n";

				mail($to,$subject,$txt,$headers);
				return true;
			}
			throw new Exception( RECUPERAR_ERROR );
		} else{
			throw new Exception( FORM_CAMPOS_FALTA );
		}

	}

	/* Esto generará una contraseña aleatoria */
	private function f_usuario_clavealeatoria() {
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = array(); //recuerde que debe declarar $pass como un array
		$alphaLength = strlen($alphabet) - 1; //poner la longitud -1 en caché
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //convertir el array en una cadena
	}

}

?>
