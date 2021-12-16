<?php
	require_once "config/config.php";
	require_once "lib/verification.php";
	require_once "lib/database-handler.php";
	require_once "models/Cycle.php";

	$cycle = new Cycle();
	$cycles = $cycle->getCycles();
	$year = $cycle->getCycleId();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="author" content="Theresa De Ocampo" />
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/all.min.css" />
	<link rel="stylesheet" type="text/css" href="css/datatables.min.css" />
	<link rel="stylesheet" type="text/css" href="css/tingle.min.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/vertical-nav-bar.css" />
	<link rel="stylesheet" type="text/css" href="css/tables.css" />
	<link rel="stylesheet" type="text/css" href="css/forms.css" />
	<link rel="stylesheet" type="text/css" href="css/header-fields.css" />
	<link rel="stylesheet" type="text/css" href="css/media-queries.css" />
	<link rel="shortcut icon" type="image/x-icon" href="img/others/favicon.png" />
	<title><?php echo COOPERATIVE; ?></title>
</head>
<body>
	<?php require_once "inc/vertical-nav-bar.php"; ?>

	<main>
		<header>
			<i class="fas fa-bars"></i>
			<h2 class="text">Cycle</h2>
		</header>

		<!-- Hidden, used as header for printing. -->
		<div id="coop-info-holder"><?php require_once "inc/print-header.php"; ?></div>
		<div id="cycle-holder"><?php echo $year; ?></div>

		<section id="cycle-settings">
			<h3>Settings</h3>
			<form action="src/change-cycle.php" method="post">
				<div class="flex-wrapper">
					<div class="flex-item short">
						<label for="cycle-id">Year<i class="far fa-question-circle"></i></label>
						<input id="cycle-id" type="number" name="cycle-id" required readonly value="<?php echo $year; ?>" />
					</div><!-- .flex-item -->
				</div><!-- .flex-wrapper -->
				<button type="submit" name="submit" class="dt-button">Submit</button>
			</form>
			<hr class="divider" />
			<table id="cycle-tbl" class="display cell-border" width="100%">
				<thead>
					<th>Year</th>
					<th>Membership Fee <span>(&#8369;)</span></th>
					<th>Interest Rate</th>
					<th>Minimum Processing Fee <span>(&#8369;)</span></th>
					<th>Checkout</th>
				</thead>
				<tbody>
					<?php foreach ($cycles as $c): ?>
					<?php if ($c->cycle_id == $year): $class = "fas fa-door-closed"; ?>
						<tr class="selected">
					<?php else: $class = "fas fa-door-open"; ?>
					<tr>
					<?php endif; ?>
						<td><?php echo $c->cycle_id; ?></td>
						<td><?php echo number_format($c->membership_fee, 2, ".", ","); ?></td>
						<td><?php echo $c->interest_rate; ?></td>
						<td><?php echo number_format($c->min_processing_fee, 2, ".", ","); ?></td>
						<td><i class="<?php echo $class; ?>"></i></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #cycle-tbl -->
		</section><!-- #cycle-settings -->
	</main>

	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/vertical-nav-bar.js"></script>
	<script src="js/datatables.min.js"></script>
	<script src="js/tingle.min.js"></script>
	<script src="js/tables.js"></script>
	<script src="js/modal.js"></script>
	<script src="js/cycle.js"></script>
</body>
</html>