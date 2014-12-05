<?php
$isPostBack = false;

$referer = "";
$thisPage = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

if (isset($_SERVER['HTTP_REFERER'])){
    $referer = $_SERVER['HTTP_REFERER'];
}

if ($referer == $thisPage){
    $isPostBack = true;
}
/**
 * These are the database login details
 */  
define("HOST", "localhost");     // The host you want to connect to.
define("USER", "dblogin");    // The database username. 
define("PASSWORD", "");    // The database password. 
define("DATABASE", "osSecureTrading");    // The database name.
 
define("CAN_REGISTER", "any");
define("DEFAULT_ROLE", "member");
 
define("SECURE", FALSE);    // FOR DEVELOPMENT ONLY!!!!

$secTable = 'osSecurities';
?>
