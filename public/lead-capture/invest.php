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
			<a href="index.html"><img src="fa fa-home"/></a>
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
		<h1> Be a Guarantor </h1>
	</section><!-- .sub-header -->

	<section class="listing">
		<h1>Keep Calm and Invest</h1>
		<p>
			Peer-to-Peer’s target market is the population that has limited access to financial services and unbanked individuals, these types of loan inherently come with risks for investors. However, unlike other platforms, our system offers its users the right tools and services to curb these risks.
		</p>
		<div class="row">
			<div class="listing-col">
				<p class="third">Up to Five Shares</p>
				<p class="desc">You only need one share to get started!</p>
			</div><!-- .listing-col -->
			<div class="listing-col">
				<p class="third">₱12,000</p>
				<p class="desc">Amount Per Share</p>
			</div><!-- .listing-col -->
			<div class="listing-col">
				<p class="third">10% Interest</p>
				<p class="desc">Collect your profit every month!</p>
			</div><!-- .listing-col -->
		</div><!-- .row -->
		<a href="terms.php" class="hero-btn red-btn">read more</a>
	</section><!-- .listing -->

	<section class="boxes">
		<h1>Invest and Lend</h1>
		<p>How to be a guarantor and how to earn and grow your money.</p>
		<div class="row">
			<div class="boxes-col">
				<p class="third">Step 1 </br> Request an Account</p>
				<p>
					Contact us to claim your account. Do note that you must be a local resident of <?php echo SUBDIVISION.", ".BARANGAY ?> for us to process your application.
				</p>
			</div><!-- .boxes-col -->
			<div class="boxes-col">
				<p class="third">Step 2 </br> Invest Desired Share/s</p>
				<p>After placing your shares, you may start choosing borrowers that will be placed under your discretion.</p>
			</div><!-- .boxes-col -->
			<div class="boxes-col">
				<p class="third">Step 3 </br> Earn Monthly Interest</p>
				<p>
					Collect monthly interest from your borrowers, and deposit it to the treasurer. Earned profits are then claimed after every business year.
				</p>
			</div><!-- .boxes-col -->
		</div><!-- .row -->
		<a href="contact.php" class="hero-btn red-btn">call us now</a>
	</section><!-- .boxes -->

	<section class="footer">
		<p><a href="../../agreement.php" target="_blank"> read our data privacy </a></p>
		<p>&copy; 2021 <?php echo COOPERATIVE; ?>. All Rights Reserved.</p>
	</section><!-- .footer -->

	<script src="../../js/lead-capture.js"></script>
</body>
</html>