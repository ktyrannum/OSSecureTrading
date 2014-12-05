<?php
include_once '../secured/secure_includes/registration.inc.php';
include_once '../secured/secure_includes/functions.php';
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
		<title>OS Secure Stock Trading Registration</title>
	</head>
	<body>
		<div id="wrapper">
			<?php include ('includes/header.php'); ?>
			<?php include ('includes/nav.php'); ?>
			<?php include ('../secured/secure_includes/div_management.php'); ?>

			<div id="content">
				<?php
				if (!empty($error_msg)) {
					echo $error_msg;
				}
				?>
				<ul>
					<li>
						Usernames must be at least 6 characters long.
					</li>
					<li>
						Email must be a valid format. This will be your login name.
					</li>
					<li>
						Passwords must be at least 6 characters long
					</li>
					<li>
						Passwords must contain
						<ul>
							<li>
								At least one upper case letter (A..Z)
							</li>
							<li>
								At least one lower case letter (a..z)
							</li>
							<li>
								At least one number (0..9)
							</li>
						</ul>
					</li>
				</ul>
				<div class="form">
				<form action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>"
				method="post"
				name="registration_form">
					<p class="contact">Username:</p>
					<input type="text" name="username" id="username" placeholder="BSmith123" />
					<br>
					<p class="contact">Email:</p>
					<input type="text" name="email" id="email" placeholder="username@domain.com" />
					<br>
					<p class="contact">Password:</p>
					<input type="password" name="password" id="password" placeholder="Strong Password" />
					<br>
					<p class="contact">Confirm password:</p>
					<input type="password" name="confirmpwd" id="confirmpwd" placeholder="Same Strong Password" />
					<br>
					<input clas="buttom" type="button" value="Register" onclick="return regformhash(this.form,
						this.form.username,
						this.form.email,
						this.form.password,
						this.form.confirmpwd);"/>
				</form>
				</div>

				
				<p>
					Return to the <a href="login.php">login page</a>.
				</p>
			</div>
			<!-- end #content -->
			<div id="regpage">
			<?php include ('../secured/secure_includes/registration_success.php'); ?>
			</div>

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

