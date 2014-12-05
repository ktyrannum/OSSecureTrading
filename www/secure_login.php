<?php
include_once '../secured/secure_includes/dbconnect.php';
include_once '../secured/secure_includes/functions.php';

sec_session_start();

if (isset($_POST['email'], $_POST['p'], $_POST['pkey'])) {
	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
	$password = $_POST['p'];
	$userkey = $_POST['pkey'];
	// The hashed password.

	if (login($email, $password, $userkey, $mysqli) == true) {

		// Login success
		header('Location: secured.php'); 
	}
	 else {

		// Login failed
		header('Location: login.php?error=1');
	}
}
else {
	// The correct POST variables were not sent to this page.
	echo 'Invalid Request'; }
?>
