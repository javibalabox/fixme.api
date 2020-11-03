<?php
//Start session if there is none
if(session_id() == ''){
    session_start();
}

//Get class for extra functions (Database, Random Codes, etc.)
if( !class_exists( 'Extras' ) ) {
    require "app/classes/extras.php";
    $extras_class = new Extras;
}

if( !class_exists( 'Strings' ) ) {
    require "app/classes/strings.php";
    $strings_class = new Strings;
}

if( !class_exists( 'User' ) ) {
    require "app/classes/user.php";
    $user_class = new User;
}

if( !class_exists( 'Worker' ) ) {
    require "app/classes/worker.php";
    $worker_class = new Worker;
}

if( !class_exists( 'Services' ) ) {
    require "app/classes/services.php";
    $services_class = new Services;
}

if( !class_exists( 'Booking' ) ) {
    require "app/classes/booking.php";
    $booking_class = new Booking;
}

if( !class_exists( 'Payments' ) ) {
    require "app/classes/payments.php";
    $payments_class = new Payments;
}

if( !class_exists( 'Coupons' ) ) {
    require "app/classes/coupons.php";
    $coupons_class = new Coupons;
}


if(!function_exists("getString")) {
	function getString($string_name){
		global $strings_class;
		echo $strings_class->$string_name;
	}
}

if(!function_exists("getStringValue")) {
    function getStringValue($string_name){
        global $strings_class;
        return $strings_class->$string_name;
    }
}

?>