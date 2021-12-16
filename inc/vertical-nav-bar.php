<?php
	require_once "models/User.php";
	require_once "models/Administrator.php";

	$administrator = new Administrator();
	$admin_verified_id = $_SESSION["admin-verified"];
	$admin = $administrator->getAdmin($admin_verified_id);
	$username = $admin->username;
	$position = $admin->position;
	$profile_picture = "img/profile-pictures/";
	if (is_null($admin->profile_picture))
		$profile_picture .= "default.jpg";
	else
		$profile_picture .= $admin->profile_picture;
?>
<nav class="closed">
	<h1>
		<i class="fas fa-handshake"></i>
		<span>Ciudad Nuevo</span>
	</h1>

	<ul class="nav-links">
		<li>
			<a href="home.php">
				<i class="fas fa-home"></i>
				<span class="link-name">Home</span>
			</a>
			<ul class="sub-menu blank">
				<li><a class="link-name" href="home.php">Home</a></li>
			</ul>
		</li>

		<li>
			<div class="parent-menu">
				<a href="members.php">
					<i class="fas fa-users"></i>
					<span class="link-name">Members</span>
				</a>
				<i class="fas fa-chevron-down"></i>
			</div><!-- .parent-menu -->
			<ul class="sub-menu">
				<li><a class="link-name" href="members.php">Members</a></li>
				<li><a href="members.php#guarantors">Guarantors</a></li>
				<li><a href="members.php#savings">Savings</a></li>
				<li><a href="members.php#data-subjects">All Data Subjects</a>
			</ul><!-- .sub-menu -->
		</li>

		<li>
			<div class="parent-menu">
				<a href="transactions.php">
					<i class="fas fa-layer-group"></i>
					<span class="link-name">Transactions</span>
				</a>
				<i class="fas fa-chevron-down"></i>
			</div><!-- .parent-menu -->
			<ul class="sub-menu">
				<li><a class="link-name" href="transactions.php">Transactions</a></li>
				<li><a href="transactions.php#loan-disbursements">Loan Disbursements</a></li>
				<li><a href="transactions.php#appropriations">Appropriations</a></li>
			</ul><!-- .sub-menu -->
		</li>

		<li>
			<a href="loans.php">
				<i class="fas fa-tasks"></i>
				<span class="link-name">Loans</span>
			</a>
			<ul class="sub-menu blank">
				<li><a class="link-name" href="loans.php">Loans</a></li>
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
				<li><a href="payroll.php#profits">Profits</a></li>
				<li><a href="payroll.php#principal-summation">Principal Summation</a></li>
				<li><a href="payroll.php#interest-summation">Interest Summation</a></li>
				<li><a href="payroll.php#shares">Shares</a></li>
				<li><a href="payroll.php#honorarium">Honorarium</a></li>
			</ul><!-- .sub-menu -->
		</li>

		<li>
			<a href="cycle.php">
				<i class="fas fa-history"></i>
				<span class="link-name">Cycle</span>
			</a>
			<ul class="sub-menu blank">
				<li><a class="link-name" href="cycle.php">Cycle</a></li>
			</ul>
		</li>

		<li>
			<div class="profile-details">
				<a href="user-settings.php">
					<div class="profile-content">
						<img src="<?php echo $profile_picture; ?>" alt="Avatar">
					</div><!-- .profile-content -->
					<div class="name-position">
						<div class="username"><?php echo $username; ?></div>
						<div class="position"><?php echo $position; ?></div>
					</div><!-- .name-position -->
				</a>
				<i class="fas fa-sign-out-alt"></i>
			</div><!-- .profile-details -->
		</li>
	</ul><!-- .nav-links -->
</nav><!-- .closed -->