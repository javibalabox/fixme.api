<?php
include "initializer.php";

//Session is set to "PvUsrSess"

class User
{

	public $user_id;
	public $conn;
	public $is_logged;
	public $user_data_array;

	function __construct()
	{
		global $extras_class;
		$this->conn = $extras_class->database();
	}


	//Get from extra class
	public function randomCode($prefix)
	{
		global $extras_class;
		return $extras_class->randomCode($prefix);
	}
	//End Get from extra class


	public function new_user($profile_picture_url, $name, $first_lastname, $second_lastname, $lat, $lng, $mail, $biography, $gender, $phone, $password, $method, $facebook_id, $type)
	{
		global $extras_class;
		global $strings_class;

		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		$mail = mysqli_real_escape_string($this->conn, $mail);
		$name = mysqli_real_escape_string($this->conn, $name);
		$first_lastname = mysqli_real_escape_string($this->conn, $first_lastname);
		$second_lastname = mysqli_real_escape_string($this->conn, $second_lastname);
		$lat = mysqli_real_escape_string($this->conn, $lat);
		$lng = mysqli_real_escape_string($this->conn, $lng);
		$password = password_hash(mysqli_real_escape_string($this->conn, $password), PASSWORD_DEFAULT);
		/* $birth_date = mysqli_real_escape_string($this->conn, $birth_date);
		$phone = mysqli_real_escape_string($this->conn, $phone); */
		$birth_date = "";
		$phone = mysqli_real_escape_string($this->conn, $phone);

		$biography = mysqli_real_escape_string($this->conn, $biography);
		$gender = mysqli_real_escape_string($this->conn, $gender);
		$method = mysqli_real_escape_string($this->conn, $method);
		$facebook_id = mysqli_real_escape_string($this->conn, $facebook_id);
		$date = date('m/d/Y h:i:s a', time());
		$user_id = $this->randomCode('user_');
		$image_name = "";


		if ($method == "mail") {
			$query = "SELECT * FROM users WHERE mail = '$mail' AND type = '$type' AND method = 'mail'";
			$result = mysqli_query($this->conn, $query);
			if (mysqli_num_rows($result)) {
				$response_array = $extras_class->response("false", "e1", "Este correo ya está registrado.", "both");
				return $response_array;
				exit();
			}

			if ($profile_picture_url == "" || $profile_picture_url == null) {
				$image_name = "dummy.jpg";
			}
		} /* else {
            $social_linking = $this->social_linking_register($method, $user_id, $facebook_id, $type);
            if ($social_linking[0]["status"] != "true") {
                $response_array = $extras_class->response("false", "e1", $social_linking[0]["message"], "both");
                return $response_array;
                exit();
            }

            if ($profile_picture_url != "" && $profile_picture_url != null) {
                $image = file_get_contents($profile_picture_url);
                if ($image !== false) {
                    $image = base64_encode($image);
                    $image_name = $user_id . strtotime("now") . ".png";
                    file_put_contents('../images/' . $image_name, base64_decode($image));
                }
            }
        } */


		$sql = "INSERT INTO users (user_id,name,first_lastname,second_lastname,mail,password,birth_date,gender,phone,biography,img_url,method,type,creation_date) VALUES ('$user_id', '$name', '$first_lastname', '$second_lastname', '$mail', '$password', '$birth_date', '$gender', '$phone', '$biography', '$image_name', '$method','$type','$date')";
		if (mysqli_query($this->conn, $sql)) {
			$response_array = $extras_class->response("true", "sc1", "Registro exitoso.", "console");
			$response_array[] = array('user_id' => $user_id, 'url' => $extras_class->home_url);
		} else {
			$response_array = $extras_class->response("false", "e1", "Registro fallido.", "both");
		}

		return $response_array;
	}

	public function try_login($mail, $password, $user_type)
	{
		global $extras_class;
		global $strings_class;

		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		$query = "SELECT * FROM users WHERE mail = '$mail' AND type = '$user_type' AND method = 'mail'";
		$result = mysqli_query($this->conn, $query);

		/* Ver si el correo existe */
		if (mysqli_num_rows($result)) {
			while ($row = mysqli_fetch_assoc($result)) {
				$password_db = $row['password'];
				$user_id = $row['user_id'];
				$user_type = $row['type'];
			}

			/* Comparar las contraseñas */
			if (password_verify($password, $password_db)) {
				$response_array = $extras_class->response("true", "sc1", "Sesión iniciada", "console");

				$create_token = $this->create_login_token($user_id);
				if ($create_token[0]["status"] == "true") {
					if ($create_token[1]["token"] != null) {
						$response_array[] = array('user_id' => $user_id, 'user_type' => $user_type, 'token' => $create_token[1]["token"], 'link' => $extras_class->home_url);
						$this->user_id = $user_id;
						$this->trigger_login($user_type);
					}
				} else {
					$response_array = $create_token;
				}
			} else {
				$response_array = $extras_class->response("false", "e1", "No coinciden las contraseñas", "both");
			}
		} else {
			$response_array = $extras_class->response("false", "e1", "El correo no está registrado", "both");
		}

		return $response_array;
	}

	public function create_login_token($user_id)
	{
		global $extras_class;
		global $strings_class;
		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		$token = $this->randomCode('token_');

		while ($extras_class->record_exists("login_tokens", "token", $token)) {
			$token = $this->randomCode('token_');
		}

		$sql = "INSERT INTO login_tokens (user_id,token, status) VALUES ('$user_id', '$token','active')";
		if (mysqli_query($this->conn, $sql)) {
			$response_array = $extras_class->response("true", "sc1", "Registro exitoso.", "console");
			$response_array[] = array('token' => $token);
		} else {
			$response_array = $extras_class->response("false", "e1", "Creación de token fallida.", "both");
		}
		return $response_array;
	}

	public function trigger_login()
	{
		global $extras_class;
		$conn = $extras_class->database();

		if ($this->user_id != "") {
			$_SESSION['PvUsrSess'] = $this->user_id;
			ob_end_flush();
			$this->is_logged = true;

			//Get user data
			$user_id = $this->user_id;
			$query = "SELECT * FROM users WHERE user_id = '$user_id'";
			$result = mysqli_query($conn, $query);

			if (mysqli_num_rows($result)) {
				$this->user_data_array = mysqli_fetch_assoc($result);
			}
			//End get user data

		} else {
			$this->is_logged = false;
		}
	}

	public function verify_user_token($user_id, $token)
	{
		global $extras_class;
		global $services_class;

		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		$query = "SELECT * FROM login_tokens WHERE user_id = '$user_id' AND token = '$token'";
		$result = mysqli_query($this->conn, $query);

		if (mysqli_num_rows($result)) {
			while ($row = mysqli_fetch_assoc($result)) {
				$status = $row['status'];
			}

			if ($status != null) {
				if ($status == "active") {
					$response_array = $extras_class->response("true", "sc1", "El token es válido", "console");
				} else {
					$response_array = $extras_class->response("false", "e1", "El token no se encuentra activo, inicia sesión para obtener uno nuevo.", "console");
				}
			} else {
				$response_array = $extras_class->response("false", "e1", "Hubo un problema al obtener el estatus del token, inicia sesión para obtener uno nuevo.", "console");
			}
		} else {
			$response_array = $extras_class->response("false", "e1", "El token proporcionado no existe.", "console");
		}

		return $response_array;
	}

	public function verify_login()
	{
		if ($_SESSION['PvUsrSess'] != "") {
			$this->user_id = $_SESSION['PvUsrSess'];
			ob_end_flush();
			$this->is_logged = true;
		} else {
			$this->is_logged = false;
		}

		return $this->is_logged;
	}

	public function verify_login_js()
	{
		global $extras_class;
		global $strings_class;

		$response_array = $extras_class->response("false", "e1", "No loggeado", "both");

		if ($_SESSION['PvUsrSess'] != "") {
			$this->user_id = $_SESSION['PvUsrSess'];
			ob_end_flush();
			$response_array = $extras_class->response("true", "e1", "loggeado", "both");
		}

		return $response_array;
	}

	public function logout()
	{
		global $extras_class;
		global $strings_class;
		session_unset();
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(
				session_name(),
				'',
				time() - 42000,
				$params["path"],
				$params["domain"],
				$params["secure"],
				$params["httponly"]
			);
		} // Finally, destroy the session.
		session_destroy();
		$response_array = $extras_class->response("true", "e1", "Logout exitoso", "both");
		$response_array[] = array('url' => $extras_class->home_url);

		return $response_array;
	}


	public function getPublicUserInfo($user_id)
	{

		if ($user_id == "") {
			if ($this->verify_login()) {
				$user_id = $this->user_id;
			}
		}

		$query = "SELECT * FROM users WHERE user_id = '$user_id'";
		$result = mysqli_query($this->conn, $query);
		if (mysqli_num_rows($result)) {
			$row = mysqli_fetch_assoc($result);
		} else {
			$row = "";
		}

		return $row;
	}


	//---------------------------- Rest API ---------------------------- 
	//---------------------------- Rest API ---------------------------- 
	//---------------------------- Rest API ---------------------------- 

	public function get_user_private_data_2($user_id, $mail, $password)
	{
		global $extras_class;
		global $strings_class;
		$data_list_array = array();

		$query = "SELECT * FROM users WHERE mail = '$mail' AND user_id = '$user_id'";
		$result = mysqli_query($this->conn, $query);

		/* Ver si el correo existe */
		if (mysqli_num_rows($result)) {
			while ($row = mysqli_fetch_assoc($result)) {
				$password_db = $row['password'];
				$data_list_array[] = $row;
			}

			/* Comparar las contraseñas */
			if (password_verify($password, $password_db)) {
				$response_array = $extras_class->response("true", "sc1", "Se obtuvo la información pública del usuario con éxito", "console");
				$response_array[] = array("user_data" => $data_list_array);
			} else {
				$response_array = $extras_class->response("false", "e1", "No coinciden las contraseñas", "both");
			}
		} else {
			$response_array = $extras_class->response("false", "e1", "Los datos no coinciden", "both");
		}

		return $response_array;
	}

	public function get_user_private_data($user_id, $mail, $password)
	{
		global $extras_class;
		global $strings_class;
		$data_list_array = array();

		$query = "SELECT * FROM users WHERE mail = '$mail' AND user_id = '$user_id'";
		$result = mysqli_query($this->conn, $query);

		/* Ver si el correo existe */
		if (mysqli_num_rows($result)) {
			while ($row = mysqli_fetch_assoc($result)) {
				$password_db = $row['password'];
				$data_list_array[] = $row;
			}

			/* Comparar las contraseñas */
			if (password_verify($password, $password_db)) {
				$response_array = $extras_class->response("true", "sc1", $data_list_array, "console");
			} else {
				$response_array = $extras_class->response("false", "e1", "No coinciden las contraseñas", "both");
			}
		} else {
			$response_array = $extras_class->response("false", "e1", "Los datos no coinciden", "both");
		}

		return $response_array;
	}

	public function get_user_public_data($user_id, $token)
	{
		global $extras_class;
		global $strings_class;
		global $payments_class;
		$data_list_array = array();

		/* $verify_user_token = $this->verify_user_token($user_id, $token);
		if ($verify_user_token[0]["status"] != "true") {
			$response_array = $extras_class->response("false", "e1", $verify_user_token[0]["message"], "both");
			return $response_array;
			exit();
		} */

		$query = "SELECT * FROM users WHERE user_id = '$user_id'";
		$result = mysqli_query($this->conn, $query);

		/* Ver si el correo existe */
		if (mysqli_num_rows($result)) {
			while ($row = mysqli_fetch_assoc($result)) {
				$password_db = $row['password'];
				$data_list_array[] = array("user_id" => $row['user_id'], "name" => $row['name'], "first_lastname" => $row['first_lastname'], "second_lastname" => $row['second_lastname'], "mail" => $row['mail'], "phone" => $row['phone'], "biography" => $row['biography'], "birth_date" => $row['birth_date'], "img_url" => $extras_class->home_url . $extras_class->appurl . $extras_class->gallery_folder . $row['img_url'], "type" => $row['type'], "method" => $row['method']);
			}

			$response_array = $extras_class->response("true", "sc1", "La información pública del usuario se obtuvo con éxito", "console");
			$response_array[] = array('user_data' => $data_list_array);
		} else {
			$response_array = $extras_class->response("false", "e1", "No se ha encontrado nada para este ID", "both");
		}

		return $response_array;
	}

	public function edit_user_profile($user_id, $profile_picture_base64, $name, $first_lastname, $second_lastname, $mail, $biography, $password, $new_password, $method, $facebook_id, $type)
	{
		global $extras_class;
		global $strings_class;

		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		$user_id = mysqli_real_escape_string($this->conn, $user_id);
		$mail = mysqli_real_escape_string($this->conn, $mail);
		$name = mysqli_real_escape_string($this->conn, $name);
		$first_lastname = mysqli_real_escape_string($this->conn, $first_lastname);
		$second_lastname = mysqli_real_escape_string($this->conn, $second_lastname);

		/* $birth_date = mysqli_real_escape_string($this->conn, $birth_date);
		$phone = mysqli_real_escape_string($this->conn, $phone); */
		$biography = mysqli_real_escape_string($this->conn, $biography);
		$method = mysqli_real_escape_string($this->conn, $method);
		$facebook_id = mysqli_real_escape_string($this->conn, $facebook_id);
		$date = date('m/d/Y h:i:s a', time());
		$image_name = "";

		if ($method == "mail") {
			$try_login = $this->verify_credentials($user_id, $password, $type);
			if ($try_login[0]["status"] != "true") {
				$response_array = $extras_class->response("false", "e1", $try_login[0]["message"], "both");
				return $response_array;
				exit();
			}
		} else {
			$try_social_login = $this->verify_social_credentials($user_id, $method, $facebook_id);
			if ($try_social_login[0]["status"] != "true") {
				$response_array = $extras_class->response("false", "e1", $try_social_login[0]["message"], "both");
				return $response_array;
				exit();
			}
		}

		$img_sql = "";
		if ($profile_picture_base64 != "" && $profile_picture_base64 != null) {
			$image_name = $user_id . strtotime("now") . ".png";
			file_put_contents('../images/' . $image_name, base64_decode($profile_picture_base64));
			$img_sql = ", img_url = '$image_name'";
		}

		$pwd_sql = "";
		if ($new_password != null && $new_password != "") {
			$new_password = password_hash(mysqli_real_escape_string($this->conn, $new_password), PASSWORD_DEFAULT);
			$pwd_sql = ", password = '$new_password'";
		}

		if ($method == "mail") {
			$query = "SELECT * FROM users WHERE mail = '$mail' AND type = '$type' AND method = 'mail' AND user_id = '$user_id'";
			$result = mysqli_query($this->conn, $query);
			if (!mysqli_num_rows($result)) {
				$query_2 = "SELECT * FROM users WHERE mail = '$mail' AND type = '$type' AND method = 'mail'";
				$result_2 = mysqli_query($this->conn, $query_2);
				if (mysqli_num_rows($result_2)) {
					$response_array = $extras_class->response("false", "e1", "Este correo ya está siendo utilizado por alguien más.", "both");
					return $response_array;
					exit();
				}
			}
		}


		$sql = "UPDATE users SET name = '$name', mail = '$mail', first_lastname = '$first_lastname', second_lastname = '$second_lastname', biography = '$biography'" . $pwd_sql . $img_sql . "  WHERE user_id = '$user_id'";
		if (mysqli_query($this->conn, $sql)) {
			$response_array = $extras_class->response("true", "sc1", "Actualización exitosa.", "console");
		} else {
			$response_array = $extras_class->response("false", "e1", "Actualización fallida.", "both");
		}

		return $response_array;
	}

	public function verify_credentials($user_id, $password, $user_type)
	{
		global $extras_class;
		global $strings_class;

		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		$query = "SELECT * FROM users WHERE user_id = '$user_id' AND type = '$user_type' AND method = 'mail'";
		$result = mysqli_query($this->conn, $query);

		/* Ver si el correo existe */
		if (mysqli_num_rows($result)) {
			while ($row = mysqli_fetch_assoc($result)) {
				$password_db = $row['password'];
				$user_id = $row['user_id'];
				$user_type = $row['type'];
			}

			/* Comparar las contraseñas */
			if (password_verify($password, $password_db)) {
				$response_array = $extras_class->response("true", "sc1", "Credenciales verificadas", "console");
			} else {
				$response_array = $extras_class->response("false", "e1", "No coinciden las contraseñas", "both");
			}
		} else {
			$response_array = $extras_class->response("false", "e1", "El usuario no aparece registrado", "both");
		}

		return $response_array;
	}
	
	public function verify_social_credentials($user_id, $source, $id)
	{
		global $extras_class;
		global $strings_class;

		$response_array = $extras_class->response("false", "e1", "Hubo un problema interno (500).", "both");

		$query = "SELECT * FROM social_linking WHERE source = '$source' AND user_id = '$user_id' AND social_id = '$id'";
		$result = mysqli_query($this->conn, $query);

		if (mysqli_num_rows($result)) {
			$response_array = $extras_class->response("true", "sc1", "Credenciales correctas", "console");
		} else {
			$response_array = $extras_class->response("false", "e1", "No coinciden las credenciales", "both");
		}

		return $response_array;
	}
}
