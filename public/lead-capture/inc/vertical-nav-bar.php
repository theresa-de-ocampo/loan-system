<?php
	require_once "../../models/User.php";

	$user = new User();
	$user_id = $_SESSION["generic-user-verified"];
	$account = $user->getUser($user_id);
	$username = $account->username;
	$profile_picture = "../../img/profile-pictures/";
	if (is_null($account->profile_picture))
		$profile_picture .= "default.jpg";
	else
		$profile_picture .= $account->profile_picture;
?>
<nav class="closed">
	<h1>
		<i class="fas fa-handshake"></i>
		<span>Ciudad Nuevo</span>
	</h1>

	<ul class="nav-links">
		<li>
			<a href="loans.php">
				<i class="fas fa-receipt"></i>
				<span class="link-name">Loans</span>
			</a>
			<ul class="sub-menu blank">
				<li><a class="link-name" href="loans.php">Loans</a></li>
			</ul>
		</li>

		<li>
			<a href="disbursements.php">
				<i class="fas fa-tasks"></i>
				<span class="link-name">Disbursements</span>
			</a>
			<ul class="sub-menu blank">
				<li><a class="link-name" href="disbursements.php">Disbursements</a></li>
			</ul>
		</li>

		<li>
			<div class="parent-menu">
				<a href="payroll.php">
					<i class="fas fa-cash-register"></i>
					<span class="link-name">Payroll</span>
				</a>
				<i class="fas fa-chevron-down"></i>
			</div><!-- .parent-menu -->
			<ul class="sub-menu">
				<li><a class="link-name" href="payroll.php">Payroll</a></li>
				<li><a href="payroll.php#shares">Shares</a></li>
				<li><a href="payroll.php#salary">Salary</a></li>
			</ul><!-- .sub-menu -->
		</li>

		<li>
			<div class="profile-details">
				<div class="profile-content">
					<img src="<?php echo $profile_picture; ?>" alt="Avatar">
				</div><!-- .profile-content -->
				<div class="name-position">
					<div class="username"><?php echo $username; ?></div>
				</div><!-- .name-position -->
				<i class="fas fa-sign-out-alt"></i>
			</div><!-- .profile-details -->
		</li>
	</ul><!-- .nav-links -->
</nav><!-- .closed -->