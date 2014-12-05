<?php

// Secure Includes
include_once '../secured/secure_includes/secure_prices.inc.php';
include_once '../secured/secure_includes/functions.php';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<meta name="author" content="" />
		<link rel="stylesheet" href="css/style.css" />
		<link rel="stylesheet" href="css/style1.css" />
		<title>OS Secure Stock Trading Secured Content</title>
	</head>
	<body>
		<div id="wrapper">
			<?php include ('includes/header.php'); ?>
			<?php include ('../secured/secure_includes/secure_nav.php'); ?>

		<div id="content">

			<?php if (login_check($mysqli) == true) : ?>
				<?php else : ?>
				    <p><span class="error">You are not authorized to access this page.</span> Please <a href="index.php">login</a></p>
				<?php endif; ?>
				<?php echo $error_msg; ?>
				<div class='form'>
					<form action='<?php echo $_PHP_SELF; ?>' name='modify' method='post'>
					<p class='contact'>Insert Ticker</p>
					<input type='submit' name='insert' value='Insert' id='insert' />
					<input type='text' name='ticker' id='ticker' placeholder='goog' />
					</form>
					<form  action='<?php echo $_PHP_SELF; ?>' name='update' method='post'>
					<p class='contact'>Update Asking Price</p>
					<input type='submit' name='update' value='Update' id='update'/>
					</form>
				</div>
			<?php showTable($secTable, htmlentities($_SESSION['username']), $mysqli); ?>
			</div>
			<!-- End Content -->

		<?php include ('includes/sidebar.php'); ?>
		<?php include ('includes/footer.php'); ?>
			
		</div>
		<!-- End Wrapper -->	
		

	<?php unset($_POST); ?>

	</body>
</html>
