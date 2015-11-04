<?php

$suspect = false;
$pattern = '/Content-Type:|Bcc:|Cc:/i';

function isSuspect($val, $pattern, &$suspect){
	if (is_array($val)){
		foreach ($val as $item) {
			isSuspect($item, $pattern, $suspect);
		}
	} else {
		if (preg_match($pattern, $val)) {
			$suspect = true;
		}
	}
}

isSuspect($_POST, $pattern, $suspect);

if (!$suspect){
	foreach ($_POST as $key => $value) {
	    // assign to temporary variable and strip whitespace if not an array
	    $temp = is_array($value) ? $value : trim($value);
	    // if empty and required, add to $missing array
	    if (empty($temp) && in_array($key, $required)) {
	        $missing[] = $key;
	        ${$key} = '';
	    } elseif (in_array($key, $expected)) {
	        // otherwise, assign to a variable of the same name as $key
	        ${$key} = $temp;
	    }
	}
}

if (!$suspect && !empty($email)) {
	$validemail = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
	if ($validemail) {
		$headers .= "\r\nReply-To: $validemail";
	} else {
		$errors['email'] = true;
	}
}

$mailSent = false;
if (!$suspect && !$missing && !$errors) {

	$message = '';

	foreach($expected as $item) {

		if (isset(${$item}) && !empty(${$item})) {
			$val = ${$item};
		} else {
			$val = 'Not selected';
		}
		
		if (is_array($val)) {
			$val = implode(', ', $val);
		}
		
		$item = str_replace(['_', '-'], ' ', $item);
		$message .= ucfirst($item).": $val\r\n\r\n";
	}
	
	$message = wordwrap($message, 70);
	$mailSent = mail($to, $subject, $message, $headers);
	if (!$mailSent) {
		$errors['mailfail'] = true;
	}
}