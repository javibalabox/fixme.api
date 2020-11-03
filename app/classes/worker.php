<?php
include "initializer.php";

//Session is set to "PvUsrSess"

class Worker {

	public $conn;

	function __construct()
	{
		global $extras_class;
		$this->conn = $extras_class->database();
	}

	//---------------------------- Rest API ---------------------------- 
	//---------------------------- Rest API ---------------------------- 
	//---------------------------- Rest API ---------------------------- 

	public function get_worker_public_data($user_id){
		global $extras_class;
		global $strings_class;
		$data_list_array = array();

		$query = "SELECT * FROM users WHERE user_id = '$user_id' AND type = 'worker'";
		$result = mysqli_query($this->conn, $query);

		/* Ver si el correo existe */
		if (mysqli_num_rows($result)) {
		  while($row = mysqli_fetch_assoc($result)) {
			  $password_db = $row['password'];
			  $data_list_array[] = array("user_id" => $row['user_id'], "name" => $row['name'], "first_lastname" => $row['first_lastname'], "second_lastname" => $row['second_lastname'], "mail" => $row['mail'], "phone" => $row['phone'], "img_url" => $row['img_url']);
		  }

		  $response_array = $extras_class->response("true","sc1","Información del trabajador obtenida con éxito", "console");
		  $response_array[] = array('worker_data' => $data_list_array);
		} else {
			$response_array = $extras_class->response("false","e1","No se ha encontrado nada para este ID", "both");
		}

		return $response_array;
	}
}
?>