<?php

// Secure Includes
include_once 'dbconnect.php';
include_once 'osConfig.php';

// Ckear Error Message
$error_msg = "";
 
if (isset($_POST['username'], $_POST['email'], $_POST['p'])) {

    // Sanitize and validate POST Data
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    $hidecontent = "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        // Invalid Email address
        $error_msg .= '<p class="error">Invalid Email Address</p>';
    }
 
    $password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
    if (strlen($password) != 128) {

        // Check for 128 character, or 512 bit string for SHA512
        $error_msg .= '<p class="error">Invalid password.</p>';
    }
 
   // All Client Side error checking done.
 
   // Prepare SQL statements
    $prep_stmt = "SELECT osUserID FROM osCredentials WHERE osEmail = ? LIMIT 1";
    $stmt = $mysqli->prepare($prep_stmt);
 
   // Check for Duplicate email address in database  
    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
 
        if ($stmt->num_rows == 1) {

            // Duplicate Email Address found.
            $error_msg .= '<p class="error">Duplicate Email Address found.</p>';
                        $stmt->close();
        }
                //$stmt->close();
    } else {
        $error_msg .= '<p class="error">Database connection error. ERRROR 50.</p>';
                $stmt->close();
    }
 
    // check existing username
    $prep_stmt = "SELECT osUserID FROM osCredentials WHERE osUsername = ? LIMIT 1";
    $stmt = $mysqli->prepare($prep_stmt);
 
    if ($stmt) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
 
                if ($stmt->num_rows == 1) {
                        
                        $error_msg .= '<p class="error">Duplicate Username.</p>';
                        $stmt->close();
                }
               
        } else {
                $error_msg .= '<p class="error">Database connection error. ERROR 70.</p>';
                $stmt->close();
        }
 
    if (empty($error_msg)) {

	//Generate a new Public / Private key pair
	include_once 'rsa_gen.php'; 
        // Create a random salt
        $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
	
        //$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
 
        // Create salted password 
        $password = hash('sha512', $password . $random_salt);
 
        // Insert the new user into the database 
        if ($insert_stmt = $mysqli->prepare("INSERT INTO osCredentials (osUsername, osEmail, osPasswordH, osPasswordS) VALUES (?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssss', $username, $email, $password, $random_salt);
            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                header('Location: error.php?err=Registration failure: INSERT');
            }
        }

	if ($insert_stmt = $mysqli->prepare("INSERT INTO osPKey (osUsername, osPublicKey) VALUES (?, ?)")) {
            $insert_stmt->bind_param('ss', $username, base64_encode($pubKey));
            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                header('Location: error.php?err=Key generation failure: INSERT');
            }

        }
        //header('Location: registration_success.php');
	$hidecontent = "TRUE";
    }
}
?>
