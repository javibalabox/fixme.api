<?php
//Initialize clasess and session
include "mods-start.php";
$json_call = false;
$errores_array = array();
$httpCode = null;

if (!isset($_POST["action"])) {
	$body = @file_get_contents('php://input');
	$data = json_decode($body);
	error_log($body);
	$action = $data->action;
	if ($action == null || $action == "") {
		$httpCode = 400;
		array_push($errores_array, 'No hay una llamada concreta');
	} else {
		$json_call = true;
	}
} else {
	$action = $_POST["action"];
}

$response_array = $extras_class->response("false", "e1", "Hola amigo, cómo llegaste aquí?.", "both");

switch ($action) {
	case 'login':
		if ($json_call) {
			$mail = $data->mail;
			if ($mail == null) {
				array_push($errores_array, 'Falta el campo de correo');
			}

			$password = $data->password;
			if ($password == null) {
				array_push($errores_array, 'Falta el campo de contraseña');
			}

			$user_type = $data->user_type;
			if ($user_type == null) {
				array_push($errores_array, 'Falta el campo de tipo de usuario');
			}

			if (count($errores_array) != 0) {
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $user_class->try_login(
					$mail,
					$password,
					$user_type
				);
			}
		} else {
			$response_array = $user_class->try_login(
				$_POST["mail"],
				$_POST["password"],
				$_POST["user_type"]
			);
		}
		break;

	case 'signup':
		if ($json_call) {

			$profile_picture_url = $data->profile_picture_url;

			$name = $data->name;
			//error_log($name);
			if ($name == null) {
				array_push($errores_array, 'Falta el campo de nombre');
			}

			$first_lastname = $data->first_lastname;
			if ($first_lastname == null) {
				array_push($errores_array, 'Falta el campo de apellido paterno');
			}

			$second_lastname = $data->second_lastname;
			if ($second_lastname == null) {
				array_push($errores_array, 'Falta el campo de apellido materno');
			}

			$lat = $data->lat;
			if ($lat == null) {
				array_push($errores_array, 'Falta el campo de latitud');
			}

			$lng = $data->lng;
			if ($lng == null) {
				array_push($errores_array, 'Falta el campo de longitud');
			}

			$mail = $data->mail;
			if ($mail == null) {
				array_push($errores_array, 'Falta el campo de correo');
			}

			$biography = $data->biography;

			$gender = $data->gender;

			$phone = $data->phone;
			if ($phone == null) {
				array_push($errores_array, 'Falta el campo de teléfono');
			}

			$password = $data->password;

			$facebook_id = $data->facebook_id;

			$method = $data->method;
			if ($method == null) {
				array_push($errores_array, 'Falta el campo de tipo de método');
			} else {
				if ($method == "facebook") {
					if ($facebook_id == null) {
						array_push($errores_array, 'Falta el ID de Facebook');
					}
				} else {
					if ($password == null) {
						array_push($errores_array, 'Falta el campo de contraseña');
					}
				}
			}

			$user_type = $data->user_type;
			if ($user_type == null) {
				array_push($errores_array, 'Falta el campo de tipo de usuario');
			} else {
				if ($user_type == "coach") {
					if ($biography == null) {
						array_push($errores_array, 'Falta el campo de biografía');
					}
				}
			}

			if (count($errores_array) != 0) {
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $user_class->new_user(
					$profile_picture_url,
					$name,
					$first_lastname,
					$second_lastname,
					$lat,
					$lng,
					$mail,
					$biography,
					$gender,
					$phone,
					$password,
					$method,
					$facebook_id,
					$user_type
				);
			}
		}
		break;

	case 'validate_token':
		if ($json_call) {
			$user_id = $data->user_id;
			if ($user_id == null) {
				array_push($errores_array, 'Falta el campo de ID de usuario');
			}

			$token = $data->token;
			if ($token == null) {
				array_push($errores_array, 'Falta el campo de token');
			}

			if (count($errores_array) != 0) {
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $user_class->verify_user_token(
					$user_id,
					$token
				);
			}
		}
		break;


	case 'verify_login_js':
		$response_array = $user_class->verify_login_js();
		break;

	case 'logout':
		$httpCode = 200;
		$response_array = $user_class->logout();
		break;

		//---------------------------- Rest API ---------------------------- 
		//---------------------------- Rest API ---------------------------- 
		//---------------------------- Rest API ---------------------------- 

	case 'get_user_private_data':
		if ($json_call) {
			$user_id = $data->user_id;
			if ($user_id == null) {
				array_push($errores_array, 'Falta el campo de id de usuario');
			}

			$mail = $data->mail;
			if ($mail == null) {
				array_push($errores_array, 'Falta el campo de correo');
			}

			$password = $data->password;
			if ($password == null) {
				array_push($errores_array, 'Falta el campo de contraseña');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $user_class->get_user_private_data(
					$user_id,
					$mail,
					$password
				);
			}
		} else {
			$response_array = $user_class->get_user_private_data(
				$_POST["user_id"],
				$_POST["mail"],
				$_POST["password"]
			);
		}
		break;

	case 'get_user_public_data':
		if ($json_call) {
			$user_id = $data->user_id;
			if ($user_id == null) {
				array_push($errores_array, 'Falta el campo de id de usuario');
			}

			$token = $data->token;
			if ($token == null) {
				array_push($errores_array, 'Falta el campo de token');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $user_class->get_user_public_data(
					$user_id,
					$token
				);
			}
		}
		break;

	case 'get_worker_public_data':
		if ($json_call) {
			$user_id = $data->user_id;
			if ($user_id == null) {
				array_push($errores_array, 'Falta el campo de is de usuario');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $worker_class->get_worker_public_data(
					$user_id
				);
			}
		} else {
			$response_array = $worker_class->get_worker_public_data(
				$_POST["user_id"]
			);
		}
		break;

	case 'get_services_list':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$user_id = $data->user_id;
			if ($user_id == null) {
				array_push($errores_array, 'Falta el campo de id de usuario');
			}

			$status = $data->status;
			if ($status == null) {
				array_push($errores_array, 'Falta el campo de is de status');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $services_class->get_services_list(
					$user_id,
					$status
				);
			}
		} else {
			$response_array = $services_class->get_services_list(
				$_POST["user_id"],
				$_POST["status"]
			);
		}
		break;

	case 'get_service_details':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$service_id = $data->service_id;
			if ($service_id == null) {
				array_push($errores_array, 'Falta el campo de id de servicio');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $services_class->get_service_details(
					$service_id
				);
			}
		} else {
			$response_array = $services_class->get_service_details(
				$_POST["service_id"]
			);
		}
		break;

	case 'book_new_service':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$client_user_id = $data->client_user_id;
			if ($client_user_id == null) {
				array_push($errores_array, 'Falta el campo client_user_id');
			}

			$worker_user_id = $data->worker_user_id;
			/*if($worker_user_id == null){
				array_push($errores_array, 'Falta el campo worker_user_id');
			}*/

			$service_id = $data->service_id;
			if ($service_id == null) {
				array_push($errores_array, 'Falta el campo service_id');
			}

			$appointment_date = $data->appointment_date;
			if ($appointment_date == null) {
				array_push($errores_array, 'Falta el campo appointment_date');
			}

			$appointment_location = $data->appointment_location;
			if ($appointment_location == null) {
				array_push($errores_array, 'Falta el campo appointment_location');
			}

			$payment_action = $data->payment_action;
			if ($payment_action == null) {
				array_push($errores_array, 'Falta el campo payment_action');
			}

			$method = $data->method;
			if ($method == null) {
				array_push($errores_array, 'Falta el campo method');
			}

			$card_id = $data->card_id;
			/*if($card_id == null){
				array_push($errores_array, 'Falta el campo card_id');
			}*/

			$coupon = $data->coupon;
			/*if($coupon == null){
				array_push($errores_array, 'Falta el campo coupon');
			}*/

			$status = $data->status;
			if ($status == null) {
				array_push($errores_array, 'Falta el campo status');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $booking_class->book_new_service(
					$client_user_id,
					$worker_user_id,
					$service_id,
					$appointment_date,
					$appointment_location,
					$payment_action,
					$method,
					$card_id,
					$coupon,
					$status
				);
			}
		}
		break;

	case 'confirm_service_execution':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$booking_id = $data->booking_id;
			if ($booking_id == null) {
				array_push($errores_array, 'Falta el campo bookig_id');
			}

			$worker_user_id = $data->worker_user_id;
			if ($worker_user_id == null) {
				array_push($errores_array, 'Falta el campo worker_user_id');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $booking_class->confirm_service_execution(
					$booking_id,
					$worker_user_id
				);
			}
		} else {
			$response_array = $booking_class->confirm_service_execution(
				$_POST["booking_id"],
				$_POST["worker_user_id"]
			);
		}
		break;

	case 'check_on_worker':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$booking_id = $data->booking_id;
			if ($booking_id == null) {
				array_push($errores_array, 'Falta el campo bookig_id');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $booking_class->check_on_worker(
					$booking_id
				);
			}
		} else {
			$response_array = $booking_class->check_on_worker(
				$_POST["booking_id"]
			);
		}
		break;

	case 'get_booking_data':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$booking_id = $data->booking_id;
			if ($booking_id == null) {
				array_push($errores_array, 'Falta el campo bookig_id');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $booking_class->get_booking_data(
					$booking_id
				);
			}
		} else {
			$response_array = $booking_class->get_booking_data(
				$_POST["booking_id"]
			);
		}
		break;

	case 'get_client_bookings':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$user_id = $data->user_id;
			if ($user_id == null) {
				array_push($errores_array, 'Falta el campo user_id');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $booking_class->get_client_bookings(
					$user_id
				);
			}
		} else {
			$response_array = $booking_class->get_client_bookings(
				$_POST["user_id"]
			);
		}
		break;

	case 'get_worker_bookings':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$worker_user_id = $data->worker_user_id;
			if ($worker_user_id == null) {
				array_push($errores_array, 'Falta el campo worker_user_id');
			}

			$token = $data->token;
			if ($token == null) {
				array_push($errores_array, 'Falta el campo token');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $booking_class->get_worker_bookings(
					$worker_user_id,
					$token
				);
			}
		} else {
			$response_array = $booking_class->get_worker_bookings(
				$_POST["worker_user_id"]
			);
		}
		break;

	case 'client_cancel_booking':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$user_id = $data->user_id;
			if ($user_id == null) {
				array_push($errores_array, 'Falta el campo user_id');
			}

			$booking_id = $data->booking_id;
			if ($booking_id == null) {
				array_push($errores_array, 'Falta el campo booking_id');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $booking_class->client_cancel_booking(
					$user_id,
					$booking_id
				);
			}
		}
		break;

	case 'worker_cancel_booking':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$worker_user_id = $data->worker_user_id;
			if ($worker_user_id == null) {
				array_push($errores_array, 'Falta el campo worker_user_id');
			}

			$booking_id = $data->booking_id;
			if ($booking_id == null) {
				array_push($errores_array, 'Falta el campo booking_id');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $booking_class->worker_cancel_booking(
					$worker_user_id,
					$booking_id
				);
			}
		}
		break;

	case 'get_avaialble_bookings_requests':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$worker_user_id = $data->worker_user_id;
			if ($worker_user_id == null) {
				array_push($errores_array, 'Falta el campo worker_user_id');
			}

			$token = $data->token;
			if ($token == null) {
				array_push($errores_array, 'Falta el campo token');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $booking_class->get_avaialble_bookings_requests(
					$worker_user_id,
					$token
				);
			}
		} else {
			$response_array = $booking_class->get_avaialble_bookings_requests(
				$_POST["worker_user_id"]
			);
		}
		break;

	case 'create_stripe_customer':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$user_id = $data->user_id;
			if ($user_id == null) {
				array_push($errores_array, 'Falta el campo user_id');
			}

			$token = $data->token;
			if ($token == null) {
				array_push($errores_array, 'Falta el campo token');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $payments_class->create_stripe_customer(
					$user_id,
					$token
				);
			}
		} else {
			$response_array = $payments_class->create_stripe_customer(
				$_POST["user_id"],
				$_POST["token"]
			);
		}
		break;

	case 'get_stripe_customer_data':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$user_id = $data->user_id;
			if ($user_id == null) {
				array_push($errores_array, 'Falta el campo user_id');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $payments_class->get_stripe_customer_data(
					$user_id
				);
			}
		} else {
			$response_array = $payments_class->get_stripe_customer_data(
				$_POST["user_id"]
			);
		}
		break;

	case 'add_new_card':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$user_id = $data->user_id;
			if ($user_id == null) {
				array_push($errores_array, 'Falta el campo user_id');
			}

			$card_token = $data->card_token;
			if ($card_token == null) {
				array_push($errores_array, 'Falta el campo card_token');
			}

			$token = $data->token;
			if ($token == null) {
				array_push($errores_array, 'Falta el campo token');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $payments_class->add_new_card(
					$user_id,
					$card_token,
					$token
				);
			}
		} else {
			$response_array = $payments_class->add_new_card(
				$_POST["user_id"],
				$_POST["card_token"],
				$_POST["token"]
			);
		}
		break;

	case 'add_payment_method':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$user_id = $data->user_id;
			if ($user_id == null) {
				array_push($errores_array, 'Falta el campo user_id');
			}

			$payment_method = $data->payment_method;
			if ($payment_method == null) {
				array_push($errores_array, 'Falta el campo payment_method');
			}

			$token = $data->token;
			if ($token == null) {
				array_push($errores_array, 'Falta el campo token');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $payments_class->add_payment_method(
					$user_id,
					$payment_method,
					$token
				);
			}
		} else {
			$response_array = $payments_class->add_payment_method(
				$_POST["user_id"],
				$_POST["payment_method"],
				$_POST["token"]
			);
		}
		break;

	case 'get_stripe_customers_cards':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$user_id = $data->user_id;
			if ($user_id == null) {
				array_push($errores_array, 'Falta el campo user_id');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $payments_class->get_stripe_customers_cards(
					$user_id
				);
			}
		} else {
			$response_array = $payments_class->get_stripe_customers_cards(
				$_POST["user_id"]
			);
		}
		break;

	case 'remove_card':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$user_id = $data->user_id;
			if ($user_id == null) {
				array_push($errores_array, 'Falta el campo user_id');
			}

			$card_id = $data->card_id;
			if ($card_id == null) {
				array_push($errores_array, 'Falta el campo card_id');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $payments_class->remove_card(
					$user_id,
					$card_id
				);
			}
		} else {
			$response_array = $payments_class->remove_card(
				$_POST["user_id"],
				$_POST["card_id"]
			);
		}
		break;

	case 'execute_charge':
		if ($json_call) {
			$httpCode = 500; //Por si hay un error en la lógica

			$user_id = $data->user_id;
			if ($user_id == null) {
				array_push($errores_array, 'Falta el campo user_id');
			}

			$amount = $data->amount;
			if ($amount == null) {
				array_push($errores_array, 'Falta el campo amount');
			}

			$payment_source = $data->payment_source;
			if ($payment_source == null) {
				array_push($errores_array, 'Falta el campo payment_source');
			}

			$description = $data->description;
			if ($description == null) {
				array_push($errores_array, 'Falta el campo description');
			}

			if (count($errores_array) != 0) {
				$httpCode = 400;
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $payments_class->execute_charge(
					$user_id,
					$amount,
					$payment_source,
					$description
				);
			}
		} else {
			$response_array = $payments_class->execute_charge(
				$_POST["user_id"],
				$_POST["amount"],
				$_POST["payment_source"],
				$_POST["description"]
			);
		}
		break;

	case 'edit_profile':
		if ($json_call) {

			$profile_picture_base64 = $data->profile_picture_base64;

			$user_id = $data->user_id;
			if ($user_id == null) {
				array_push($errores_array, 'Falta el campo de ID de usuario');
			}

			$name = $data->name;
			if ($name == null) {
				array_push($errores_array, 'Falta el campo de nombre');
			}

			$first_lastname = $data->first_lastname;
			if ($first_lastname == null) {
				array_push($errores_array, 'Falta el campo de apellido paterno');
			}

			$second_lastname = $data->second_lastname;
			if ($second_lastname == null) {
				array_push($errores_array, 'Falta el campo de apellido materno');
			}

			$mail = $data->mail;
			if ($mail == null) {
				array_push($errores_array, 'Falta el campo de correo');
			}

			$biography = $data->biography;

			$password = $data->password;

			$new_password = $data->new_password;

			$facebook_id = $data->facebook_id;

			$method = $data->method;
			if ($method == null) {
				array_push($errores_array, 'Falta el campo de tipo de método');
			} else {
				if ($method == "facebook") {
					if ($facebook_id == null) {
						array_push($errores_array, 'Falta el ID de Facebook');
					}
				} else {
					if ($password == null) {
						array_push($errores_array, 'Falta el campo de contraseña');
					}
				}
			}

			$user_type = $data->user_type;
			if ($user_type == null) {
				array_push($errores_array, 'Falta el campo de tipo de usuario');
			}

			if (count($errores_array) != 0) {
				$response_array = $extras_class->response("false", "e1", $errores_array, "both");
			} else {
				$httpCode = 200;
				$response_array = $user_class->edit_user_profile(
					$user_id,
					$profile_picture_base64,
					$name,
					$first_lastname,
					$second_lastname,
					$mail,
					$biography,
					$password,
					$new_password,
					$method,
					$facebook_id,
					$user_type
				);
			}
		}
		break;

	default:
		$response_array = $extras_class->response("false", "e1", "Hola amigo, cómo llegaste aquí?.", "both");
		break;
}

if ($json_call) {
	header('Content-type: application/json');
	http_response_code($httpCode);
}
echo json_encode($response_array);
