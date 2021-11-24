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
		<h1> Be a Borrower</h1>
	</section><!-- .sub-header -->

	<section class="borrow">
		<div class="row">
			<div class="borrow-col">
				<h1>Borrow and pay with just 10% interest!</h1>
				<p>
					Get quick response and funding for your loan with our system. Better financing management tailored just for you.
				</p>
				<a href="terms.php" class="hero-btn red-btn">read more</a>
			</div><!-- .borrow-col -->
			<div class="borrow-col">
				<img src="../../img/public/borrow.jpg">
			</div><!-- .borrow-col -->
		</div><!-- .row -->
	</section><!-- .borrow -->

	<section class="footer">
		<p><a href="../../agreement.php" target="_blank"> read our data privacy </a></p>
		<p>&copy; 2021 <?php echo COOPERATIVE; ?>. All Rights Reserved.</p>
	</section><!-- .footer -->

	<script src="../../js/lead-capture.js"></script>
</body>
</html>