<?php
	require_once('recaptchalib.php');
	$privatekey = "6LfYG84SAAAAANKYW9DwoiCQIHflxRR98VR5W-Je";
	$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

	if ($resp->is_valid) {
		echo "success";
	} else {
		//die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." ."(reCAPTCHA said: " . $resp->error . ")");
		echo "no!";
	}
?>