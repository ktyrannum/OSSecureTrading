<?php
include_once 'osConfig.php';
 
function sec_session_start() {
	// Set unique session id
	$session_name = 'sec_session_id';   
	$secure = SECURE;

	// Stop JS from accessing session id
	$httponly = true;

	// User oly cookies
	if (ini_set('session.use_only_cookies', 1) === FALSE) {
	header("Location: error.php?err=Could not initiate a safe session (ini_set)");
	exit();
	}

	// Gets current cookies params.
	$cookieParams = session_get_cookie_params();
	session_set_cookie_params($cookieParams["lifetime"],
	$cookieParams["path"], 
	$cookieParams["domain"], 
	$secure,
	$httponly);

	// Sets the session name to the one set above.
	session_name($session_name);
	session_start();            // Start the PHP session 
	session_regenerate_id();    // regenerated the session, delete the old one. 
}

function login($email, $password, $userkey, $mysqli) {
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT osUserID, osUsername, osPasswordH, osPasswordS 
        FROM osCredentials
       WHERE osEmail = ?
        LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();
 
        // get variables from result.
        $stmt->bind_result($user_id, $username, $db_password, $salt);
        $stmt->fetch();
 
        // hash the password with the unique salt.
        $password = hash('sha512', $password . $salt);
        if ($stmt->num_rows == 1) {
            // If the user exists we check if the account is locked
            // from too many login attempts 
 
            if (checkbrute($user_id, $mysqli) == true) {
                // Account is locked 

                return false;
            } else {
                // Check if the password in the database matches
                // the password the user submitted.
                if ($db_password == $password) {
                    // Password is correct!
                    // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
                    // XSS protection as we might print this value
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/", 
                                                                "", 
                                                                $username);
                    $_SESSION['username'] = $username;
                    $_SESSION['login_string'] = hash('sha512', 
                              $password . $user_browser);

		    // Set the user's private key in session variable
		    $_SESSION['userpkey'] = base64_encode($userkey);

                    // Login successful.
                    return true;
                } else {
                    // Password is not correct
                    // We record this attempt in the database
                    $now = time();
                    $mysqli->query("INSERT INTO osLoginAttempts(osUserID, osTime)
                                    VALUES ('$user_id', '$now')");
                    return false;
                }
            }
        } else {
            // No user exists.
            return false;
        }
    }
}

function checkbrute($user_id, $mysqli) {
    // Get timestamp of current time 
    $now = time();
 
    // All login attempts are counted from the past 2 hours. 
    $valid_attempts = $now - (2 * 60 * 60);
 
    if ($stmt = $mysqli->prepare("SELECT osTime 
                             FROM osLoginAttempts 
                             WHERE osUserID = ? 
                            AND osTime > '$valid_attempts'")) {
        $stmt->bind_param('i', $user_id);
 
        // Execute the prepared query. 
        $stmt->execute();
        $stmt->store_result();
 
        // If there have been more than 5 failed logins 
        if ($stmt->num_rows > 5) {
            return true;
        } else {
            return false;
        }
    }
}

function login_check($mysqli) {
    // Check if all session variables are set 
    if (isset($_SESSION['user_id'], 
                        $_SESSION['username'], 
                        $_SESSION['login_string'])) {
 
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
 
        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
 
        if ($stmt = $mysqli->prepare("SELECT osPasswordH 
                                      FROM osCredentials 
                                      WHERE osUserID = ? LIMIT 1")) {
            // Bind "$user_id" to parameter. 
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();
 
            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);
 
                if ($login_check == $login_string) {
                    // Logged In!!!! 
                    return true;
                } else {
                    // Not logged in 
                    return false;
                }
            } else {
                // Not logged in 
                return false;
            }
        } else {
            // Not logged in 
            return false;
        }
    } else {
        // Not logged in 
        return false;
    }
}

function esc_url($url) {
 
    if ('' == $url) {
        return $url;
    }
 
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
 
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;
 
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
 
    $url = str_replace(';//', '://', $url);
 
    $url = htmlentities($url);
 
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
 
    if ($url[0] !== '/') {
        return '';
    } else {
        return $url;
    }
}

function showTable ($table, $username, $mysqli) {
 	$columns= $mysqli -> query("SHOW COLUMNS FROM $table");
 	$result = $mysqli -> query("SELECT osUsername, osStockTickers, osAskRealTime, osPurchased, osSold, osPurse FROM $table WHERE osUsername = '$username'"); //
	$fields = array("sUsername", "osStockTickers", "osAskRealTime", "osPurchased", "osSold", "osPurse");

	if (!$columns) {
	echo "Could not query colums from table";
	}
	if (!$result) {
	echo "Could not display contents of user's stock information";
	}
 	$array = array();
 	$i = 0;
 	
         // Start the table
	echo "<div class='stocktable'>";
 	echo "<table>";
 	echo "<tr>";
 	while ($row = $columns -> fetch_array(MYSQLI_BOTH)) {
			if (in_array($row['Field'], $fields )) {				
 				$array[] = $row['Field'];
			}
 	}

	// Show table headers
	echo"<td col='Ticker' align='left'>Ticker</td>";
	echo"<td col='Price' align='left'>Asking Price</td>";
	echo"<td col='Purchased' align='left'>Purchased</td>";
	echo"<td col='Sold' align='left'>Sold</td>";
	echo"<td col='Purse' align='left'>Purse</td>";
 	echo "<td col='Actions'>Actions</td>";
	echo "</tr>";

	// Show table data
 	while ($row = $result -> fetch_array(MYSQLI_BOTH)) {
 		echo "<tr>";
 		while ($i < sizeof($array)) {
 			echo "<td>" .decrypt_data($row[$array[$i]]). "</td>";

 			$i++;
 		}
 		echo "<td> <input type='checkbox' name='transid[]' value=" .$row['osUserID'] ." /></td>"; // Showing actions
		echo "</td>";
 		echo "</tr>"; // Closing the row
 		$i = 0; // Resetting columns
 	}
 	echo "</table>";
	echo "</div>";
	echo "<p></p>";

 }

function get_publickey ($mysqli) {
	$username = $_SESSION['username'];
 	$columns= $mysqli -> query("SHOW COLUMNS FROM osPKey");
 	$result = $mysqli -> query("SELECT osPublicKey FROM osPKey WHERE osUsername = '$username'"); //
	$fields = array("osPublicKey");

	if (!$columns) {
	echo "Could not query colums from table";
	}
	if (!$result) {
	echo "Could not display contents of user's Public Key";
	}
 	$array = array();
 	$i = 0;
 	
         // Get the Column Name
 	while ($row = $columns -> fetch_array(MYSQLI_BOTH)) {
			if (in_array($row['Field'], $fields )) {				
 				$array[] = $row['Field'];
			}
 	}

	// Get the Public Key
 	while ($row = $result -> fetch_array(MYSQLI_BOTH)) {
 		while ($i < sizeof($array)) {
			$publickey = base64_decode($row[$array[$i]]);
 			$i++;
 		}
 		$i = 0; // Resetting columns
 	}
return $publickey;
//echo "<td><pre>" .$publickey. "</pre></td>";
}

function encrypt_data ($mysqli, $data) {
$publickey = get_publickey($mysqli);
openssl_public_encrypt($data, $encrypted, $publickey);
return $encrypted;
}

function decrypt_data ($data) {
$privkey = base64_decode(htmlentities($_SESSION['userpkey']));
openssl_private_decrypt($data, $decrypted, $privkey);

return $decrypted;
}

function update_prices ($mysqli) {
	$username = $_SESSION['username'];
	$transid = get_userid($mysqli);
	for($x = 0; $x < count($transid); $x++) {						
		$userid = $transid[$x];	 	

	$columns= $mysqli -> query("SHOW COLUMNS FROM osSecurities");


 	$result = $mysqli -> query("SELECT osStockTickers FROM osSecurities WHERE osUserID = '$userid'");
	$fields = array("osStockTickers");
 	$array = array();
 	$i = 0;


         // Get the Column Name
 	while ($row = $columns -> fetch_array(MYSQLI_BOTH)) {
			if (in_array($row['Field'], $fields )) {				
 				$array[] = $row['Field'];
			}
 	}

	// Get the ticker price;
 	while ($row = $result -> fetch_array(MYSQLI_BOTH)) {
 		//while ($i < sizeof($array)) {
		//	$ticker = decrypt_data($row[$array[$i]]);
		for($i = 0; $i < count($array); $i++) {			
			$ticker = decrypt_data($row[$array[$i]]);

			if (!empty($ticker)) {
				if ($stream = simplexml_load_file('https://finance.yahoo.com/webservice/v1/symbols/' .$ticker. '/quote?format=xml')) {
					$price = $stream->resources[0]->resource->field[1];
										
					
	
				if ($insert_stmt = $mysqli->prepare("UPDATE osSecurities SET osAskRealTime = ? WHERE osUsername = '$username' AND osUserID = ?" )) {
					
				    $insert_stmt->bind_param('ss', encrypt_data($mysqli, $price), $userid);
				    // Execute the prepared query.
				    if (! $insert_stmt->execute()) {
					header('Location: error.php?err=Key generation failure: INSERT');
				    }

				}
				//return $price;
			
				}
 			
			}
		//	$i++;
 		}
 	//$i = 0; // Resetting columns
	}
	}
//return $ticker;

}

function get_userid ($mysqli) {
$username = $_SESSION['username'];
 	$columns= $mysqli -> query("SHOW COLUMNS FROM osSecurities");
 	$result = $mysqli -> query("SELECT osUserID FROM osSecurities WHERE osUsername = '$username'");
	$fields = array("osUserID");
 	$array = array();
	$useridarray = array();
 	$i = 0;
 	
         // Get the Column Name
 	while ($row = $columns -> fetch_array(MYSQLI_BOTH)) {
			if (in_array($row['Field'], $fields )) {				
 				$array[] = $row['Field'];
			}
 	}

	// Get the ticker price;
 	while ($row = $result -> fetch_array(MYSQLI_BOTH)) {
 		while ($i < sizeof($array)) {
			$userid = $row[$array[$i]];
			$useridarray[] = $userid;
			$i++;	
	
		}	
 		$i = 0; // Resetting columns

 	}
return $useridarray;
}
?>
