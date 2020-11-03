<?php
//Funciones de strings

class Strings {

	//General Information
	public $main_title;
	public $main_description;

	function __construct()
	{
		$this->changeLanguage("es_mx");
	}

	public function changeLanguage($language_prefix){
		$new_language = $language_prefix;
		$this::$new_language();
	}

	public function es_mx(){
		$this->main_title = "Título de la página";
		$this->main_description = "Descripción de la página";
	}

	public function en_us(){
		$this->main_title = "Page title";
		$this->main_description = "Page description";
	}

	
	public function getString($string_name){
		return $this->$string_name;
	}

    public function getStringValue($string_name){
        return $this->$string_name;
    }

}

?>
