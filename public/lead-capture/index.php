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
	<section class="header">
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
		<div class="text-box">
			<h1><?php echo COOPERATIVE; ?></h1>
			<h1><?php echo TOWN.", ".PROVINCE; ?></h1>
			<p>Providing a convenient, reliable, and secure online micro-lending platform.</p>
			<a href="login.php" class="hero-btn">log in</a>
		</div><!-- .text-box -->
	</section><!-- .header -->

	<section class="boxes">
		<h1>How Our Loaning System Works</h1>
		<p>Know the three basic and hassle-free functions inside our micro-lending process.</p>
		<div class="row">
			<div class="boxes-col">
				<p class="third">borrower</p>
				<p>Borrower contacts their chosen cooperative to apply and register for an account, and apply for a loan to their assigned guarantor. </p>
			</div><!-- .boxes-col -->
			<div class="boxes-col">
				<p class="third">system</p>
				<p>Our system registers the account, and automatically assigns an account for depending on their applied position. </p>
			</div><!-- .boxes-col -->
			<div class="boxes-col">
				<p class="third">guarantor</p>
				<p>Guarantor registers an account in the cooperative where they would invest, and freely fund loan to their chosen subordinates. </p>
			</div><!-- .boxes-col -->
		</div> <!-- .row -->
		<a href="terms.php" class="hero-btn red-btn">read more</a>
	</section><!-- .boxes -->

	<section class="objective">
		<h1>Our Objectives</h1>
		<p>As there is currently a lack of available management systems that mainly caters the microlending businesses done by local cooperatives in the country, our system aims to provide an enhanced and more flexible function and experience to the local cooperatives. </p>

		<div class="row">
			<div class="objective-col">
				<img src="../../img/public//microcredit.jpg">
				<div class="layer">
					<p class="third">microcredit</p>
				</div><!-- .layer -->
			</div><!-- .objective-col -->
			<div class="objective-col">
				<img src="../../img/public/userfriendly.jpg">
				<div class="layer">
					<p class="third">user-friendly</p>
				</div><!-- .layer -->
			</div><!-- .objective-col -->
			<div class="objective-col">
				<img src="../../img/public/rural.jpg">
				<div class="layer">
					<p class="third">rural communities</p>
				</div><!-- .layer -->
			</div><!-- .objective-col -->
		</div><!-- .row -->
	</section><!-- .objective -->

	<section class="apply">
		<h1>Safe, Convenient, and Reliable</h1>
		<p>Peer-to-Peer’s target market is the population that has limited access to financial services and unbanked individuals, these types of loan inherently come with risks for investors. However, unlike other platforms, our system offers its users the right tools and services to curb these risks.</p>
		<div class="row">
			<div class="apply-col">
				<div>
					<p> Our lending platform provides you with the necessary tools and services to lower risks involving P2P investments.</p>
					<p class="third"><a href="invest.php">be a guarantor</a></p>
				</div>
			</div><!-- .apply-col -->
			<div class="apply-col">
				<div>
					<p>Borrow loans that fit your financial purpose without hassle. We offer loans with more flexible term, and interests as low as 10%.</p>
					<p class="third"><a href="borrow.php">be a borrower</a></p>
				</div>
			</div><!-- .apply-col -->
		</div><!-- .row -->
	</section><!-- .apply -->

	<section class="footer">
		<p><a href="../../agreement.php" target="_blank"> read our data privacy </a></p>
		<p>&copy; 2021 <?php echo COOPERATIVE; ?>. All Rights Reserved.</p>
	</section><!-- .footer -->

	<script src="../../js/lead-capture.js"></script>
</body>
</html>