<?php
include_once 'secure_includes/dbconnect.php';
include_once 'secure_includes/functions.php';

$isPostBack = false;

$referer = "";
$thisPage = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

if (isset($_SERVER['HTTP_REFERER'])){
    $referer = $_SERVER['HTTP_REFERER'];
}

if ($referer == $thisPage){
    $isPostBack = true;
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>

		<link rel="stylesheet" href="css/style.css" />
		<link rel="stylesheet" href="css/style1.css" />

	</head>
	<body>		
	<?php showTable($secTable, htmlentities($_SESSION['username']), $mysqli); ?>
	<?php unset($_POST); ?>
	</body>
</html>
