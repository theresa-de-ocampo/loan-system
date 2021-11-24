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
		<h1> Terms & Conditions </h1>
	</section><!-- .sub-header -->

	<section class="listing">
		<h1>Business Requirements</h1>
		<p>Listed below are the mandatory requirements that are strictly followed by the cooperative members.</p>
		<div class="row">
			<div class="listing-col">
				<p class="listing-text">Business Requirement #1</p>
				<p>Every guarantor is required to be a member and invest in their desired shares that will also act as their membership fee.</p>
			</div><!-- .listing-col -->
			<div class="listing-col">
				<p class="listing-text">Business Requirement #2</p>
				<p>A processing fee is required to be paid during every first transaction, and every 3 months until the loan is paid.</p>
			</div><!-- .listing-col -->
		</div><!-- .row -->
		<div class="row">
			<div class="listing-col">
				<p class="listing-text">Business Requirement #3</p>
				<p>An advanced interest is issued and deducted from the borrowed money.</p>
			</div><!-- .listing-col -->
			<div class="listing-col">
				<p class="listing-text">Business Requirement #4</p>
				<p>A borrower is required to have a guarantor from the same cooperative they will be getting loan from.</p>
			</div><!-- .listing-col -->
		</div><!-- .row -->
	</section><!-- .listing -->

	<section class="boxes">
		<h1>Business Rules</h1>
		<p>Listed below are the directives that mandates the business activities done inside the lending cooperative.</p>
		<div class="row">
			<div class="boxes-col">
				<p class="listing-text">Business Rule #1</p>
				<p>Every guarantor has a responsibility to find nearby and trusted borrowers that will be under their supervision in order to contribute for the cooperative’s net income by the end of the business year. </p>
			</div><!-- .boxes-col -->
			<div class="boxes-col">
				<p class="listing-text">Business Rule #2</p>
				<p>A guarantor has a fixed percentage of share from the total amount of earned interest from the amount of loans they were able to lend.</p>
			</div><!-- .boxes-col -->
			<div class="boxes-col">
				<p class="listing-text">Business Rule #3</p>
				<p>The total net income after the business year will be divided among the total number of cooperative members, and will be multiplied respectively according to number of their shares.</p>
			</div><!-- .boxes-col -->
		</div>
		<div class="row">
			<div class="boxes-col">
				<p class="listing-text">Business Rule #4</p>
				<p>Guarantors are not allowed to pull-out shares or income once business year have commenced. Thus, they are only allowed to do so once the business year has ended.  </p>
			</div><!-- .boxes-col -->
			<div class="boxes-col">
				<p class="listing-text">Business Rule #5</p>
				<p>A minimal amount of penalty will be issued upon delayed payment of monthly interest, and will have a continues increase within a one (1) week limit. Failure to comply within the allotted grace period, will double the interest due for that certain month. </p>
			</div><!-- .boxes-col -->
			<div class="boxes-col">
				<p class="listing-text">Business Rule #6</p>
				<p>Penalty and processing fees are excluded from the cooperative’s annual net income, will then be allocated for miscellaneous needs of the cooperative.</p>
			</div><!-- .boxes-col -->
		</div><!-- .row -->
	</section><!-- .boxes -->

	<section class="footer">
		<p><a href="../../agreement.php" target="_blank"> read our data privacy </a></p>
		<p>&copy; 2021 <?php echo COOPERATIVE; ?>. All Rights Reserved.</p>
	</section><!-- .footer -->

	<script src="../../js/lead-capture.js"></script>
</body>
</html>
