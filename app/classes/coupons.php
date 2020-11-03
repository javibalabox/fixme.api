<?php
include "initializer.php";

class Coupons {

	public $conn;

	function __construct()
	{
		global $extras_class;
		$this->conn = $extras_class->database();
	}

	//---------------------------- Rest API ---------------------------- 
	//---------------------------- Rest API ---------------------------- 
	//---------------------------- Rest API ---------------------------- 

	public function verify_coupon($coupon_id){
		global $extras_class;
		
		$response_array = $extras_class->response("false","e1","Hubo un problema interno (500).", "both");

		if ($extras_class->record_exists('coupons','coupon_id',$coupon_id)) {

			$query = "SELECT * FROM coupons WHERE coupon_id = '$coupon_id'";
			$result = mysqli_query($this->conn, $query);

			if (mysqli_num_rows($result)) {
			  while($row = mysqli_fetch_assoc($result)) {
				  $discount_type = $row['discount_type'];
				  $discount = $row['discount'];
				  $expiration_date = $row["expiration_date"];
			  }

			  if ($discount_type != null) {
			  	if ($discount != null) {

			  		$coupon_data_array[] = array("discount_type" => $discount_type, "discount" => $discount, "expiration_date" => $expiration_date);

			  		$response_array = $extras_class->response("true","s1","Se obtuvo la información del cupón solicitao con éxito.", "both");
			  		$response_array[] = array("coupon_data"=>$coupon_data_array);

			  	} else {
			  		$response_array = $extras_class->response("false","e1","Hubo un problema para obtener el monto de descuento del cupón utilizado.", "both");
			  	}
			  } else {
			  	$response_array = $extras_class->response("false","e1","Hubo un problema para obtener el tipo de descuento que genera el cupón utilizado.", "both");
			  }

			} else {
				$response_array = $extras_class->response("false","e1","En la segunda revisión no se encontró el cupón asociado al id.", "both");
			}
		} else {
			$response_array = $extras_class->response("false","e1","No se encontró el cupón asociado al id. Id proporcionado: ".$coupon_id, "both");
		}

		return $response_array;
	}

}
?>