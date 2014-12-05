<?php
include_once '../secured/secure_includes/dbconnect.php';
include_once '../secured/secure_includes/functions.php';

if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == ""){
    $redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header("Location: $redirect");
}

sec_session_start();

if (login_check($mysqli) == true) {
	$logged = 'in';
} else {
	$logged = 'out';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>

		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<meta name="author" content="" />
		<link rel="stylesheet" type="text/css" href="css/style1.css"/>
		<link rel="stylesheet" type="text/css" href="css/style.css"/>
		<script type="text/JavaScript" src="js/sha512.js"></script>
		<script type="text/JavaScript" src="js/forms.js"></script>

		<title>OS Secure Stock Trading Login</title>

	</head>
	<body>

		<div id="wrapper">
			<?php include ('includes/header.php');?>
			<?php include ('includes/nav.php'); ?>

			<div id="content">			

				<?php
				if (isset($_GET['error'])) {
					echo '<p class="error">Error Logging In!</p>';
				}
				if (isset($_GET['logout'])) {
					echo '<p class="error">Thank you for logging out!</p>';
				}
				?>
				<div class="form">
				<form action="secure_login.php" method="post" name="login_form">
					<p class="contact">Email:</p>					
					<input type="email" name="email" />
					<p class="contact">Password:</p>
					<input type="password" name="password" id="password"/>
					<p class="contact">Private Key:</p> <! -- Temporary until Smart Cards are implemented --!>
					<p><textarea type="text" name="pkey" id="pkey"" cols="65" rows="15"></textarea></p>
					<span class = "error">Please enter your privat key</span>
					<input type="button" value="Login" onclick="formhash(this.form, this.form.password, this.form.pkey);" />
				</form>
				<p>
					You will need to register for an account to log in. <a href="registration.php">Register</a>
				</p>
				<p>
					Please log out to clean your session. <a href="secure_logout.php">Log Out</a>.
				</p>
				<p>
					You are currently logged <?php echo $logged ?>.
				</p>

			</div>
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

