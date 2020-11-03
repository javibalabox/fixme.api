<?php
//Funciones Extra

class Extras {

	public $dblocation;
	public $dbuser;
	public $dbpass;
	public $db;

	public $mainurl;
	public $webmastermail;
	public $companymail;
	public $companymail_password;
	public $mailserver;
	public $smtp_port;

	public $templates_path;
	public $gallery_folder;

	//Permalinks
	public $home_url;
	public $appurl;

	//Assets
	public $logo;

	//Payments
	public $stripe_api_key;

	function __construct()
	{

		$this->mainurl = "http://balabox-demos.com/fixme/backend/";
		$this->dblocation = "localhost";
		$this->dbuser = "balaboxd_javier";
		$this->dbpass = "wJxci-HHXxWr";
		$this->db = "balaboxd_fixme";
		$this->webmastermail = 'pruebas.balabox@gmail.com';
		$this->companymail = 'pruebas@balabox-demos.com';
		$this->companymail_password = 'PruebasBalabox.';
		$this->mailserver = 'mail.balabox-demos.com';
		$this->smtp_port = 25;

		$this->templates_path = "/../templates/";
		$this->gallery_folder = "images/";

		//Permalinks
		$this->home_url = "http://balabox-demos.com/fixme/backend/";
		$this->appurl = "app/";

		//Assets
		$this->logo = "https://argusdental.com/wp-content/uploads/2017/08/Master-Plan-Logo-PNG.png";

		//Payments
		$this->stripe_api_key = "sk_test_4J0odLt2DJ61kHUKFxKDiyor00jNFiPPj4";

	}

	public function database(){
		$conn = mysqli_connect($this->dblocation, $this->dbuser, $this->dbpass, $this->db);
		$conn->set_charset("latin5");
		if (mysqli_connect_errno()) {
			mail($this->webmastermail, 'Error al conectar con base de datos', "Failed to connect to MySQL: " . mysqli_connect_error());
		}

		return $conn;
	}

	public function randomCode($prefix){
		$characters = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$string = '';
		$max = strlen($characters) - 1;
		for ($i = 0; $i < 11; $i++) {
		    $string .= $characters[mt_rand(0, $max)];
		}

		$code = $prefix.$string;
		return $code;
	}

	public function response($status,$code,$message,$receiver){
		$response_array[] = array("status" => $status, "code" => $code, "message" => $message, "receiver" => $receiver);
		return $response_array;
	}

	public function getTemplate($template,$array){

		$template = file_get_contents(__DIR__ .$this->templates_path.$template.".html");

		if ($array != "") {
			foreach ($array as $key => $value) {
				$template = str_replace('[['.$key.']]', $value, $template);
			}
		}
		
		return $template;
	}

	public function unique_id($table, $column, $id){
		global $extras_class;
		global $strings_class;
		$is_unique = false;
		$conn = $this->database();

		$query = "SELECT * FROM ".$table." WHERE ".$column." = '$id'";
		$result = mysqli_query($conn, $query);
		if(!mysqli_num_rows($result)){
			$is_unique = true;
		}

		return $is_unique;
	}

	public function record_exists($table, $column, $id){
		global $extras_class;
		global $strings_class;
		$record_exists = false;
		$conn = $this->database();

		$query = "SELECT * FROM ".$table." WHERE ".$column." = '$id'";
		$result = mysqli_query($conn, $query);
		if(mysqli_num_rows($result)){
			$record_exists = true;
		}

		return $record_exists;
	}
}

?>
