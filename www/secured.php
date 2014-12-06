<?php
include_once '../secured/secure_includes/dbconnect.php';
include_once '../secured/secure_includes/functions.php';
 
sec_session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>

		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<meta name="author" content="" />
		<link rel="stylesheet" href="css/style.css" />
		<title>OS Secure Stock Trading Secured Content</title>
	</head>
	<body>
		<div id="wrapper">
		<?php include ('includes/header.php'); ?>
		<?php include ('../secured/secure_includes/secure_nav.php'); ?>

		<div id="content">
		<?php if (login_check($mysqli) == true) : ?>
			<?php if (!htmlentities($_SESSION['userpkey']) == "") : ?>
			    <p><?php echo $error_msg; ?> </p>
			    <p><?php include ('../secured/secure_includes/secure.inc.php'); ?></p>
			    <p>
				Update stock tickers and retrieve live stock prices. All transactions are
				Encrypted with your <br>4096-bit RSA encryption key and session TLS. Good luck...
			    </p>

			    <p>Return to <a href="index.php">home page</a></p>
			
			<?php else : ?>
			    <p>
				<span class="error">You are not authorized to access this page, or you have not supplied the correct information.</span> Please <a href="index.php">login</a>.
			    </p>
		<?php endif; ?>
		<?php endif; ?>
			</div>
			<!-- end #content -->

		<?php
		include ('includes/sidebar.php');
		?>

		<?php
		include ('includes/footer.php');
		?>
		</div>
		<!-- End #wrapper -->

	</body>
</html>

