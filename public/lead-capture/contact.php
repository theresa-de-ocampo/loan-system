<?php require_once "../../config/config.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="author" content="Jesus Lopez" />
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" type="text/css" href="../../css/all.min.css" />
	<link rel="stylesheet" type="text/css" href="../../css/lead-capture.css" />
	<link rel="shortcut icon" type="image/x-icon" href="../../img/others/favicon.png" />
	<title><?php echo COOPERATIVE; ?></title>
</head>
<body>
	<section class="sub-header">
		<nav>
			<div class="nav-links" id="navlinks">
				<i class="fa fa-times" onclick="hideMenu()"></i>
				<ul>
					<li><a href="index.php">home</a></li>
					<li><a href="borrow.php">borrow</a></li>
					<li><a href="invest.php">invest</a></li>
					<li><a href="terms.php">terms</a></li>
					<li><a href="contact.php">contact</a></li>
				</ul>
			</div><!-- .nav-links -->
			<i class="fa fa-bars" onclick="showMenu()"></i>
		</nav>
		<h1> Contact Us</h1>
	</section><!-- .sub-header -->

	<section class="location">
		<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3865.7981647291704!2d120.8049899140579!3d14.323153487657297!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33962a1f0356bd5d%3A0x67d1d524d376a9a1!2sCiudad%20Nuevo!5e0!3m2!1sen!2sph!4v1633670867367!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
	</section><!-- .location -->

	<section class="contact-us">
		<div class="row">
			<div class="contact-col">
				<div>
					<i class="fa fa-home"></i>
					<span>
						<p class="fifth"><?php echo TOWN.", ".PROVINCE; ?></p>
						<p>Location</p>
					</span>
				</div>
				<div>
					<i class="fa fa-phone"></i>
					<span>
						<p class="fifth"><?php echo LANDLINE; ?></p>
						<p> Opens at 10 AM to 4 PM on weekdays</p>
					</span>
				</div>
				<div>
					<i class="fas fa-mobile-alt"></i>
					<span>
						<p class="fifth"><?php echo CP_NUMBER; ?></p>
						<p> We're just one text away! </p>
					</span>
				</div>
			</div><!-- .contact-col -->
			<div class="contact-col">
				<form action="" method="post">
					<input type="text" name="name" placeholder="Enter your name" required>
					<input type="email" name="email" placeholder="Enter your email" required>
					<input type="text" name="subject" placeholder="Enter your subject" required>
					<textarea rows="8" name="message" placeholder="Type your message here" required=""></textarea>
					<button type="submit" class="hero-btn red-btn">Send Message</button>
				</form>
			</div><!-- .contact-col -->
		</div><!-- .row -->
	</section><!-- .contact-us -->

	<section class="footer">
		<p><a href="../../agreement.php" target="_blank"> read our data privacy </a></p>
		<p>&copy; 2021 <?php echo COOPERATIVE; ?>. All Rights Reserved.</p>
	</section><!-- .footer -->

	<script src="../../js/lead-capture.js"></script>
</body>
</html>