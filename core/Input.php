<?php
namespace app\core;
use app\core\Session;

class Input{
	public static function sanitize($dirty_input){
		if(is_array($dirty_input)){
			$result = array();
			foreach($dirty_input as $d_input){
				$result[] = htmlentities($d_input, ENT_QUOTES, "UTF-8");
			}
			return $result;
		}
		return htmlentities($dirty_input, ENT_QUOTES, "UTF-8");
	}

	public static function get($input){
		if(isset($_POST[$input])){
			return self::sanitize($_POST[$input]);
		}else if(isset($_GET[$input])){
			return self::sanitize($_GET[$input]);
		}
	}

	public static function generateToken(){
		$token = base64_encode(openssl_random_pseudo_bytes(32));
		Session::set('csrf_token',$token);
		return $token;
	}

	public static function checkToken($token){
		return (Session::exists('csrf_token') && Session::get('csrf_token') == $token);
	}

	public static function csrfInput(){
		return '<input type="hidden" name="csrf_token" value="' . self::generateToken() . '" />';
	}	
}