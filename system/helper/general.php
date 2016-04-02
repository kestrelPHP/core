<?php
function token($length = 32) {
	// Create token to login with
	$string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

	$token = '';

	for ($i = 0; $i < $length; $i++) {
		$token .= $string[mt_rand(0, strlen($string) - 1)];
	}

	return $token;
}

function set_lang_text($old_content, $new_content, $lang) {
	$old_content = json_decode($old_content);
	if(!is_object($old_content)) {
		$old_content = new stdClass();
	}
	$old_content->$lang = trim($new_content);
	return json_encode($old_content);
}

function get_lang_text($content, $lang) {
	$obj = json_decode($content);
	if( is_object($obj) ) {
		if ( isset($obj->$lang) ) {
			return trim($obj->$lang);
		}
		return '';
	}
	return $content;
}

function get_text_after($key, $string) {
    if (!is_bool(strpos($string, $key)))
        return substr($string, strpos($string, $key) + strlen($key));
    return $string;
}

function get_text_before($key, $string) {
    return substr($string, 0, strpos($string, $key));
}

function website_send_test_notification() {
	$api_key    = TLS_SMS_KEY;
	$api_secret = TLS_SMS_SECRET;
	$from       = "Tour Link Solution";
	$content    = "SMS Tested successfully!!";

	require_once(DRUPAL_ROOT . '/sites/all/libraries/NexmoMessage.php');
	$phone_sms = isset($_POST['phone_sms'])?$_POST['phone_sms']:0;
	$phone_sms = str_replace(" ", "",$phone_sms);
	// Step 1: Declare new NexmoMessage.
	$nexmo_sms = new NexmoMessage($api_key,$api_secret);
	// Step 2: Use sendText( $to, $from, $message ) method to send a message.
	$info = $nexmo_sms->sendText( $phone_sms,$from, $content );
	// Step 3: Display an overview of the message
	$error =  $nexmo_sms->displayOverview($info);
	// Done!
	return array('errorSMS' => $error, 'phone_sms' => $phone_sms);
}

function render_select_options($options, $before = array(), $after = array()){
	$str = "";
	if(count($before)){
		foreach ($before as $key => $value) {
			$str .= '<option value="'. $key .'">' . $value . '</option>';
		}
	}
	foreach ($options as $key => $value) {
		$str .= '<option value="'. $key .'">' . $value . '</option>';
	}

	if(count($after)){
		foreach ($after as $key => $value) {
			$str .= '<option value="'. $key .'">' . $value . '</option>';
		}
	}

	return $str;
}
function html_render($options, $type, $before = array(), $after = array()){
	$str = '';
	if( $type == ELEMENT_SELECT ) {
		if(count($before)){
			foreach ($before as $key => $value) {
				$str .= '<option value="'. $key .'">' . $value . '</option>';
			}
		}
		foreach ($options as $key => $value) {
			$str .= '<option value="'. $key .'">' . $value . '</option>';
		}

		if(count($after)){
			foreach ($after as $key => $value) {
				$str .= '<option value="'. $key .'">' . $value . '</option>';
			}
		}
	} elseif ( $type == ELEMENT_INPUT ) {
		$str = $options;
	}


	return htmlspecialchars ($str);
}

function __render($data, $key_value="", $k="key", $v="value"){
    if ( is_string($data) ) {
        $obj = new stdClass();
        $obj->$k = $key_value;
        $obj->$v = $data;
        return $obj;
    }
    else {
        $return = array();
        foreach ( (array)$data as $key => $value ) {
            $obj = new stdClass();
            $obj->$k        = $key;
            $obj->$v        = $value;
            $return[] = $obj;
        }
        return $return;
    }
}
