<?php
include "initializer.php";

class Payments
{

	public $conn;
	public $payment_library;

	function __construct()
	{
		global $extras_class;

		$this->payment_library = '../libraries/stripe/init.php';

		require_once($this->payment_library);

		$this->conn = $extras_class->database();

		\Stripe\Stripe::setApiKey($extras_class->stripe_api_key);
	}

	//---------------------------- Rest API ---------------------------- 
	//---------------------------- Rest API ---------------------------- 
	//---------------------------- Rest API ---------------------------- 

	public function create_stripe_customer($user_id, $user_token)
	{
		global $extras_class;
		global $user_class;

		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		if ($extras_class->record_exists('users', 'user_id', $user_id)) {

			$user_public_data = $user_class->get_user_public_data($user_id, $user_token);
			if ($user_public_data[0]["status"] != "true") {
				$response_array = $user_public_data;
				return $response_array;
				exit();
			}


			$user_name = $user_public_data[1]["user_data"][0]["name"] . " " . $user_public_data[1]["user_data"][0]["first_lastname"] . " " . $user_public_data[1]["user_data"][0]["second_lastname"];

			$user_type = $user_public_data[1]["user_data"][0]["type"];
			$user_mail = $user_public_data[1]["user_data"][0]["mail"];
			$user_phone = $user_public_data[1]["user_data"][0]["phone"];

			try {
				$customer = \Stripe\Customer::create([
					'name' => $user_name,
					'description' => 'Zoul (' . $user_type . ')',
					'email' => $user_mail,
					'phone' => $user_phone
				]);

				$customer_id = $customer->id;

				$response_array[] = array("customer_id" => $customer_id);

				if ($customer_id != null) {
					$associate_customer = $this->associate_customer_id($user_id, $customer_id);
					if ($associate_customer[0]["status"] == "true") {
						$response_array = $extras_class->response("true", "s1", "Se registró el cliente de manera exitosa en ambos servicios.", "both");
						$response_array[] = array("stripe_response" => $customer);
					} else {
						$response_array = $extras_class->response("false", "ec1", $associate_customer[0]["message"], "both");
					}
				} else {
					$response_array = $extras_class->response("false", "ec1", "El cliente se registró en Stripe pero no se pudo localizar el customer id", "both");
				}
			} catch (Exception $e) {
				$response_array = $extras_class->response("false", "ec1", $e->getMessage(), "both");
			}
		} else {
			$response_array = $extras_class->response("false", "ec1", "El id de usuario que se quiere registrar como cliente Stripe no existe.", "both");
		}

		return $response_array;
	}

	public function get_stripe_customer_data($user_id)
	{
		global $extras_class;

		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		$customer_id = $this->get_stripe_customer_id($user_id);
		if ($customer_id[0]["status"] == "true") {
			$customer_id = $customer_id[1]["customer_id"];
			try {
				$customer = \Stripe\Customer::retrieve($customer_id);

				$response_array = $extras_class->response("true", "s1", "Se recibió respuesta de Stripe con éxito.", "both");
				$response_array[] = array("stripe_response" => $customer);
			} catch (Exception $e) {
				$response_array = $extras_class->response("false", "ec1", $e->getMessage(), "both");
			}
		} else {
			$response_array = $extras_class->response("false", "ec1", "No se encontró ningún registro de cliente para ese id.", "both");
		}

		return $response_array;
	}

	public function associate_customer_id($user_id, $customer_id)
	{
		global $extras_class;

		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		if ($extras_class->record_exists('customers', 'user_id', $user_id)) {
			$sql = "UPDATE customers SET customer_id = '$customer_id' WHERE user_id = '$user_id';";
			if (mysqli_query($this->conn, $sql)) {
				$response_array = $extras_class->response("true", "s1", "Se actualizó el id del cliente exitósamente.", "both");
			} else {
				$response_array = $extras_class->response("false", "ec1", "Hubo un problema para actualizar el id del cliente.", "both");
			}
		} else {
			$sql = "INSERT INTO customers (user_id,customer_id) VALUES ('$user_id', '$customer_id')";
			if (mysqli_query($this->conn, $sql)) {
				$response_array = $extras_class->response("true", "s1", "Se creó el id del cliente exitósamente.", "both");
			} else {
				$response_array = $extras_class->response("false", "ec1", "Hubo un problema para crear el id del cliente.", "both");
			}
		}

		return $response_array;
	}

	public function get_stripe_customer_id($user_id)
	{
		global $extras_class;

		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		$query = "SELECT * FROM customers WHERE user_id = '$user_id'";
		$result = mysqli_query($this->conn, $query);

		if (mysqli_num_rows($result)) {
			while ($row = mysqli_fetch_assoc($result)) {
				$customer_id = $row['customer_id'];
			}

			$response_array = $extras_class->response("true", "s1", "Se obtuvo el id de cliente con éxito.", "both");
			$response_array[] = array("customer_id" => $customer_id);
		} else {
			$response_array = $extras_class->response("false", "ec1", "No se encontró ningún registro de cliente para ese id.", "both");
		}

		return $response_array;
	}

	public function add_new_card($user_id, $card_token, $user_token)
	{
		global $extras_class;

		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		$customer_id = $this->get_stripe_customer_id($user_id);
		if ($customer_id[0]["status"] != "true") {
			$create_new_stripe_customer = $this->create_stripe_customer($user_id, $user_token);
			if ($create_new_stripe_customer[0]["status"] != "true") {
				$response_array = $extras_class->response("false", "e1", "Se intentó crear el cliente para asignarle el método de pago, pero hubo el siguiente error: " . $create_new_stripe_customer[0]["message"], "both");
				return $response_array;
				exit();
			} else {
				$customer_id = $this->get_stripe_customer_id($user_id);
				if ($customer_id[0]["status"] != "true") {
					$response_array = $extras_class->response("false", "e1", "Se intentó obtener el id del cliente en un segundo intento para asignarle el método de pago, pero hubo el siguiente error: " . $customer_id[0]["message"], "both");
					return $response_array;
					exit();
				}
			}
		}

		$customer_id = $customer_id[1]["customer_id"];
		try {
			$create_card = \Stripe\Customer::createSource(
				$customer_id,
				['source' => $card_token]
			);

			$response_array = $extras_class->response("true", "s1", "Se agregó la tarjeta de Stripe con éxito.", "both");
			$response_array[] = array("stripe_response" => $create_card);
		} catch (Exception $e) {
			$response_array = $extras_class->response("false", "ec1", $e->getMessage(), "both");
		}

		return $response_array;
	}

	public function add_payment_method($user_id, $payment_method_id, $user_token)
	{
		global $extras_class;

		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		$customer_id = $this->get_stripe_customer_id($user_id);
		if ($customer_id[0]["status"] != "true") {
			$create_new_stripe_customer = $this->create_stripe_customer($user_id, $user_token);
			if ($create_new_stripe_customer[0]["status"] != "true") {
				$response_array = $extras_class->response("false", "e1", "Se intentó crear el cliente para asignarle el método de pago, pero hubo el siguiente error: " . $create_new_stripe_customer[0]["message"], "both");
				return $response_array;
				exit();
			} else {
				$customer_id = $this->get_stripe_customer_id($user_id);
				if ($customer_id[0]["status"] != "true") {
					$response_array = $extras_class->response("false", "e1", "Se intentó obtener el id del cliente en un segundo intento para asignarle el método de pago, pero hubo el siguiente error: " . $customer_id[0]["message"], "both");
					return $response_array;
					exit();
				}
			}
		}

		$customer_id = $customer_id[1]["customer_id"];
		try {

			$stripe = new \Stripe\StripeClient(
				$extras_class->stripe_api_key
			);
			$attsach_method = $stripe->paymentMethods->attach(
				$payment_method_id,
				['customer' => $customer_id]
			);

			$response_array = $extras_class->response("true", "s1", "Se agregó el método de pago con éxito.", "both");
			$response_array[] = array("stripe_response" => $attsach_method);
		} catch (Exception $e) {
			$response_array = $extras_class->response("false", "ec1", $e->getMessage(), "both");
		}

		return $response_array;
	}

	public function remove_card($user_id, $card_id)
	{
		global $extras_class;

		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		$customer_id = $this->get_stripe_customer_id($user_id);
		if ($customer_id[0]["status"] == "true") {
			$customer_id = $customer_id[1]["customer_id"];
			try {
				$remove_card = \Stripe\Customer::deleteSource(
					$customer_id,
					$card_id
				);

				$response_array = $extras_class->response("true", "s1", "Se eliminó la tarjeta de Stripe con éxito.", "both");
				$response_array[] = array("stripe_response" => $remove_card);
			} catch (Exception $e) {
				$response_array = $extras_class->response("false", "ec1", $e->getMessage(), "both");
			}
		} else {
			$response_array = $extras_class->response("false", "ec1", "No se encontró ningún registro de cliente para ese id.", "both");
		}

		return $response_array;
	}

	public function get_stripe_customers_cards($user_id)
	{
		global $extras_class;

		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		$customer = $this->get_stripe_customer_data($user_id);

		if ($customer[0]["status"] == "true") {
			$stripe_response = $customer[1]["stripe_response"];
			$customers_payment_sources = $stripe_response["sources"]["data"];
			$response_array = $extras_class->response("true", "s1", "Se obtuvo la lista de métodos de pago del cliente con éxito.", "both");
			$response_array[] = array("metodos_de_pago" => $customers_payment_sources);
		} else {
			$response_array = $extras_class->response("false", "ec1", "No se encontró ningún registro de cliente para ese id", "both");
		}

		return $response_array;
	}

	public function execute_charge($user_id, $amount, $payment_source, $description)
	{
		global $extras_class;

		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		$customer_id = $this->get_stripe_customer_id($user_id);
		if ($customer_id[0]["status"] == "true") {
			$customer_id = $customer_id[1]["customer_id"];

			$amount = floatval($amount) * 100;
			try {
				$payment = \Stripe\Charge::create([
					'amount' => $amount,
					'currency' => 'mxn',
					'customer' => $customer_id,
					'source' => $payment_source,
					'description' => $description,
				]);

				$response_array = $extras_class->response("true", "sc1", "Se realizó el cargo de Stripe con éxito.", "both");
				$response_array[] = array("stripe_response" => $payment);
			} catch (Exception $e) {
				$response_array = $extras_class->response("false", "e1", $e->getMessage(), "both");
			}
		} else {
			$response_array = $extras_class->response("false", "e1", "El usuario que intenta hacer el pago no aparece como un cliente Stripe registrado, significa que no ha registrado ninguna tarjeta aún o un método de pago. Usuario: " . $user_id, "both");
		}

		return $response_array;
	}


	public function create_subscription($user_id, $card_id)
	{
		global $extras_class;
		global $subscription_class;

		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		$check_subscription = $this->check_subscription($user_id);
		if ($check_subscription[0]["status"] == "true") {
			if ($check_subscription[0]["code"] == $extras_class->active_non_renewable_code) {
				$response_array = $extras_class->response("false", $extras_class->active_non_renewable_code, "Para suscribir a este usuario, debes esperar a que termine el ciclo.", "both");
				$response_array[] = array("end_of_cicle" => $check_subscription[1]["end_of_cicle"]);
			} else {
				$response_array = $extras_class->response("false", "e1", "Ya estás suscrito.", "both");
			}
			return $response_array;
			exit();
		}

		$customer_id = $this->get_stripe_customer_id($user_id);
		if ($customer_id[0]["status"] == "true") {
			$customer_id = $customer_id[1]["customer_id"];

			try {
				$stripe = new \Stripe\StripeClient(
					$extras_class->stripe_api_key
				);
				$create_subscription = $stripe->subscriptions->create([
					'customer' => $customer_id,
					'items' => [[
						'price' => $extras_class->subscription_price_id,
					]],
					'default_payment_method' => $card_id,
					"trial_from_plan" => false
				]);

				$subscription_id = $create_subscription->id;

				$add_subscription = $subscription_class->add_subscription($user_id, $subscription_id);
				if ($add_subscription[0]["status"] == "true") {
					$response_array = $extras_class->response("true", "sc1", "La suscripción del usuario se activó con éxito.", "both");
				} else {
					$response_array = $extras_class->response("false", "e1", "Se realizó la suscripción en el panel de pagos, pero no se alamacenó el registro en la base de datos principal.", "both");
				}

				$response_array[] = array("stripe_response" => $create_subscription);
			} catch (Exception $e) {
				$response_array = $extras_class->response("false", "e1", $e->getMessage(), "both");
			}
		} else {
			$response_array = $extras_class->response("false", "e1", "El usuario que se intenta suscribir no aparece como un cliente Stripe registrado, significa que no ha registrado ninguna tarjeta aún o un método de pago. Usuario: " . $user_id, "both");
		}

		return $response_array;
	}

	public function check_subscription($user_id)
	{
		global $extras_class;
		global $subscription_class;
		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");
		$status = null;
		$subscription_id = null;

		$get_subscription_id = $subscription_class->get_subscription_id($user_id);
		//$response_array = $get_subscription_id;
		if ($get_subscription_id[0]["status"] != "true") {
			$response_array = $get_subscription_id;
			return $response_array;
			exit();
		} else {
			$subscription_id = $get_subscription_id[1]["subscription_id"];
		}

		$customer_id = $this->get_stripe_customer_id($user_id);
		if ($customer_id[0]["status"] == "true") {
			$customer_id = $customer_id[1]["customer_id"];

			try {
				$stripe = new \Stripe\StripeClient(
					$extras_class->stripe_api_key
				);
				$retrieve_subscription = $stripe->subscriptions->retrieve(
					$subscription_id,
					[]
				);


				setlocale(LC_TIME, "es_ES");
				$current_period_end_timestamp = $retrieve_subscription->current_period_end;
				$current_period_end = $current_period_end_timestamp;
				$fecha = new DateTime("@$current_period_end");
				$current_period_end = $fecha->format('Y-m-d');

				$subscription_status = $retrieve_subscription->status;
				if ($subscription_status != null) {
					if ($subscription_status == "active") {
						$response_array = $extras_class->response("true", "sc1", "La suscripción del usuario se encuentra activa.", "both");
					} else {
						$today = date("Y-m-d");

						if ($current_period_end > $today) {
							$response_array = $extras_class->response("true", $extras_class->active_non_renewable_code, "La suscripción se encuentra activa, pero ya no se renovará, aún tendrá acceso hasta que cumpla el ciclo del último pago, si quieres renovar, podrás hacerlo al finalizar el periodo.", "both");
						} else {
							$response_array = $extras_class->response("false", "e1", "La suscripción del usuario se encuentra inactiva.", "both");
						}
					}
				} else {
					$response_array = $extras_class->response("false", "e1", "Hubo un problema para obtener la información de la suscripción.", "both");
				}

				$response_array[] = array("end_of_cicle" => (strftime("%A, %d de %B de %Y", strtotime($current_period_end))), "stripe_response" => $retrieve_subscription);
			} catch (Exception $e) {
				$response_array = $extras_class->response("false", "e1", "No se pudo conectar con Stripe para obtener la información de la suscripción.", "both");
				$response_array[] = array("error_details" => $e->getMessage());
			}
		}

		return $response_array;
	}

	public function cancel_subscription($user_id)
	{
		global $extras_class;
		global $subscription_class;
		$subscription_id = null;

		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		$get_subscription_id = $subscription_class->get_subscription_id($user_id);
		$response_array = $get_subscription_id;
		if ($get_subscription_id[0]["status"] != "true") {
			return $response_array;
			exit();
		} else {
			$subscription_id = $get_subscription_id[1]["subscription_id"];
		}

		$check_subscription = $this->check_subscription($user_id);
		if ($check_subscription[0]["status"] != "true") {
			$response_array = $check_subscription;
			return $response_array;
			exit();
		} else {
			if ($check_subscription[0]["code"] == $extras_class->active_non_renewable_code) {
				$response_array = $extras_class->response("false", $extras_class->active_non_renewable_code, "La facturación de tu suscripción ya está cancelada, aún tendrás acceso hasta que se cumpla el ciclo desde el último pago.", "both");
				$response_array[] = array("end_of_cicle" => $check_subscription[1]["end_of_cicle"]);
				return $response_array;
				exit();
			}
		}

		$customer_id = $this->get_stripe_customer_id($user_id);
		if ($customer_id[0]["status"] == "true") {
			$customer_id = $customer_id[1]["customer_id"];

			try {
				$stripe = new \Stripe\StripeClient(
					$extras_class->stripe_api_key
				);
				$create_subscription = $stripe->subscriptions->cancel(
					$subscription_id,
					[]
				);

				$check_subscription = $this->check_subscription($user_id);
				if ($check_subscription[0]["status"] == "true") {
					if ($check_subscription[0]["code"] == $extras_class->active_non_renewable_code) {
						$response_array = $extras_class->response("true", $extras_class->active_non_renewable_code, "La facturación de tu suscripción se canceló con éxito, aún tendrás acceso hasta que se cumpla el ciclo desde el último pago.", "both");
						$response_array[] = array("end_of_cicle" => $check_subscription[1]["end_of_cicle"], "stripe_response" => $create_subscription);
					} else {
						$response_array = $extras_class->response("false", "e1", "Hubo un error al cancelar tu suscripción, por favor contacta a soporte para que te ayuden a cancelar tu suscripción.", "both");
					}
				} else {
					$response_array = $extras_class->response("true", "sc1", "La suscripción ya se encuentra cancelada", "both");
				}
			} catch (Exception $e) {
				$response_array = $extras_class->response("false", "e1", $e->getMessage(), "both");
			}
		} else {
			$response_array = $extras_class->response("false", "e1", "El usuario que se intenta suscribir no aparece como un cliente Stripe registrado, significa que no ha registrado ninguna tarjeta aún o un método de pago. Usuario: " . $user_id, "both");
		}

		return $response_array;
	}
}
