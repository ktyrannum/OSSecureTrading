<?php 

// Secure Includes
include_once 'dbconnect.php';
include_once 'functions.php';
include_once 'osConfig.php';

// Start Secure Session
sec_session_start();


echo $error_msg; 
echo $error_msg = "";
if(isset($_POST['ticker'])) {
	if (!empty($_POST['ticker'])) {
	    $ticker = filter_input(INPUT_POST, 'ticker', FILTER_SANITIZE_STRING);
	    $username = htmlentities($_SESSION['username']);
	    $prep_stmt = "SELECT osStockTickers FROM osSecurities WHERE osStockTickers = ? AND osUsername = ? LIMIT 1";
	    $stmt = $mysqli->prepare($prep_stmt);
		
		   // Check for Duplicate ticker in database  
		    if ($stmt) {
			$stmt->bind_param('ss', decrypt_data($ticker), $username);
			$stmt->execute();
			$stmt->store_result();
			if ($stmt->num_rows == 1) {

		    		// Duplicate ticker found.
		    		$error_msg .= '<p class="error">Duplicate ticker Address found.</p>';
			        $stmt->close();
			}

	    	} else {
		$error_msg .= '<p class="error">Database connection error. ERRROR 50.</p>';
	    }

	 if (empty($error_msg)) {
		// Check to make sure ticker is valid
		if ($stream = simplexml_load_file('https://finance.yahoo.com/webservice/v1/symbols/' .$ticker. '/quote?format=xml')) {
			$price = $stream->resources[0]->resource->field[1];
			if (!empty($price)) {
				// Insert the new ticker into the database 
				if ($insert_stmt = $mysqli->prepare("INSERT INTO osSecurities (osUsername, osStockTickers) VALUES (?, ?)")) {
				    $insert_stmt->bind_param('ss', $username, encrypt_data($mysqli, $ticker));
				    	    // Execute the prepared query.
					    if (! $insert_stmt->execute()) {
						header('Location: secured.php?error');
					    }
				}
			}
			else {
			$error_msg = "<p class='error'>Please enter a valid ticker and click Insert.</p>";
			}
		}
		else {
		$error_msg = "<p class='error'> Error contacting Yahoo Finance. </p>";
		}

	}
	}
unset($_POST['ticker']);
header('Location: secured.php');
}

if(isset($_POST['update'])) {
	if (!empty($_POST)) {
	    update_prices($mysqli);
	}
}
?>
