<?php

/**
 * Clase para enviar mensajes de WhatsApp a través de la API oficial de Meta
 * (WhatsApp Business Cloud API - https://developers.facebook.com/docs/whatsapp).
 *
 * IMPORTANTE - Requisitos que debes cumplir tú mismo antes de que esto funcione:
 *
 * 1. Crear una cuenta de Meta for Developers y una app de tipo "Business".
 * 2. Configurar WhatsApp Business Platform y verificar un número de teléfono.
 * 3. Obtener el "Phone Number ID" y un "Access Token" (permanente, no el de
 *    prueba de 24h) desde el panel de Meta for Developers.
 * 4. Crear y esperar la APROBACIÓN de una plantilla de mensaje (template) en
 *    Meta Business Manager. Meta EXIGE usar una plantilla pre-aprobada para
 *    cualquier mensaje que la empresa inicie (el paciente no te escribió
 *    primero en las últimas 24h) — no puedes mandar texto libre "en frío".
 *    Ejemplo de plantilla a crear (categoría UTILITY):
 *      Nombre: recordatorio_cita
 *      Idioma: es
 *      Cuerpo: "Hola {{1}}, te recordamos tu cita el {{2}} a las {{3}} con {{4}}."
 *
 * Sin estos 4 pasos, el envío fallará (la API rechazará el mensaje).
 */
class WhatsAppSender {

	private $phone_number_id;
	private $access_token;
	private $template_name;
	private $template_lang;

	public function __construct($phone_number_id, $access_token, $template_name = 'recordatorio_cita', $template_lang = 'es') {
		$this->phone_number_id = $phone_number_id;
		$this->access_token    = $access_token;
		$this->template_name   = $template_name;
		$this->template_lang   = $template_lang;
	}

	/**
	 * Envía un mensaje usando una plantilla aprobada (recomendado - único método
	 * que funciona de forma confiable para recordatorios automáticos).
	 *
	 * @param string $movil       Número de celular del destinatario (con o sin código de país)
	 * @param array  $parametros  Valores para los {{1}}, {{2}}, {{3}}... de la plantilla, en orden
	 * @return array ['exito' => bool, 'respuesta' => string]
	 */
	public function enviarPlantilla($movil, $parametros) {

		$ls_movil = $this->normalizarMovil($movil);
		if (empty($ls_movil)) {
			return array('exito' => false, 'respuesta' => 'Número de celular inválido o vacío.');
		}

		$array_parametros = array();
		foreach ($parametros as $valor) {
			$array_parametros[] = array('type' => 'text', 'text' => (string) $valor);
		}

		$data = array(
			'messaging_product' => 'whatsapp',
			'to'                => $ls_movil,
			'type'              => 'template',
			'template'          => array(
				'name'     => $this->template_name,
				'language' => array('code' => $this->template_lang),
				'components' => array(
					array(
						'type'       => 'body',
						'parameters' => $array_parametros
					)
				)
			)
		);

		return $this->llamarApi($data);
	}

	/**
	 * Envía texto libre. SOLO funciona si el destinatario te escribió por
	 * WhatsApp en las últimas 24 horas (ventana de conversación abierta de
	 * Meta). Útil para pruebas, NO confiable para recordatorios automáticos
	 * a pacientes que no iniciaron conversación.
	 */
	public function enviarTexto($movil, $mensaje) {

		$ls_movil = $this->normalizarMovil($movil);
		if (empty($ls_movil)) {
			return array('exito' => false, 'respuesta' => 'Número de celular inválido o vacío.');
		}

		$data = array(
			'messaging_product' => 'whatsapp',
			'to'                => $ls_movil,
			'type'              => 'text',
			'text'              => array('body' => $mensaje)
		);

		return $this->llamarApi($data);
	}

	private function normalizarMovil($movil) {
		$ls_movil = preg_replace('/\D/', '', $movil);
		if (empty($ls_movil)) {
			return '';
		}
		if (strlen($ls_movil) == 9) {
			$ls_movil = '51' . $ls_movil; // Perú por defecto
		}
		return $ls_movil;
	}

	private function llamarApi($data) {

		if (empty($this->phone_number_id) || empty($this->access_token)) {
			return array('exito' => false, 'respuesta' => 'Falta configurar Phone Number ID o Access Token.');
		}

		$url = 'https://graph.facebook.com/v19.0/' . $this->phone_number_id . '/messages';

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Bearer ' . $this->access_token,
			'Content-Type: application/json'
		));
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);

		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curl_error = curl_error($ch);
		curl_close($ch);

		if ($curl_error) {
			return array('exito' => false, 'respuesta' => 'Error de conexión: ' . $curl_error);
		}

		if ($http_code >= 200 && $http_code < 300) {
			return array('exito' => true, 'respuesta' => $response);
		}

		return array('exito' => false, 'respuesta' => "HTTP $http_code: " . $response);
	}

}

?>
