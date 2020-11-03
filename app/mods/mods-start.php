<?php
//Start session if there is none
if(session_id() == ''){
    session_start();
}

//Get class for extra functions (Database, Random Codes, etc.)
if( !class_exists( 'Extras' ) ) {
    require "../classes/extras.php";
    $extras_class = new Extras;
}

if( !class_exists( 'Strings' ) ) {
    require "../classes/strings.php";
    $strings_class = new Strings;
}

if( !class_exists( 'User' ) ) {
    require "../classes/user.php";
    $user_class = new User;
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