<section id="<?php echo $position; ?>">
	<?php
		if (strpos($position, "asst") === false)
			$label = ucfirst($position);
		else
			$label = "Asst. Treasurer";
	?>
	<h3><?php echo $label; ?></h3>
	<hr />
	<div class="personal-details">
		<h4>Personal Details</h4>
		<div class="existing-data-subject">
			<div class="flex-wrapper">
				<div class="flex-item short">
					<label for="<?php echo $position; ?>-id">ID<i class="far fa-question-circle"></i></label>
					<input id="<?php echo $position; ?>-id" type="number" name="<?php echo $position; ?>[data-subject-id]" 
						required readonly />
				</div><!-- .flex-item -->
				<div class="flex-item">
					<label for="<?php echo $position; ?>-name">
						<?php echo $label; ?> Name<i class="far fa-question-circle"></i>
					</label>
					<input id="<?php echo $position; ?>-name" type="text" required readonly />
				</div><!-- .flex-item -->
			</div><!-- .flex-wrapper -->
			<table id="<?php echo $position; ?>-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>ID</th>
						<th>First Name</th>
						<th>Middle Name</th>
						<th>Last Name</th>
						<th>Age</th>
						<th>Address</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($data_subjects as $ds): ?>
					<tr data-with-account="<?php echo $user->hasAccount($ds->data_subject_id); ?>">
						<td><?php echo $ds->data_subject_id; ?></td>
						<td><?php echo $ds->fname; ?></td>
						<td><?php echo $ds->mname; ?></td>
						<td><?php echo $ds->lname; ?></td>
						<td><?php echo $converter->bdayToAge($ds->bday); ?></td>
						<td><?php echo $ds->phase_block_lot; ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #[position]-tbl -->
		</div><!-- .existing-data-subject -->
		<div class="new-data-subject">
			<div class="grid-wrapper">
				<div class="grid-item solo">
					<button type="button" class="dt-button">&#8592; Choose Existing</button>
				</div><!-- .grid-item.solo-->
				<div class="grid-item">
					<label for="<?php echo $position; ?>-fname">First Name</label>
					<input id="<?php echo $position; ?>-fname" type="text" name="<?php echo $position; ?>[fname]" />
				</div><!-- .grid-item -->
				<div class="grid-item">
					<label for="<?php echo $position; ?>-mname">Middle Name</label>
					<input id="<?php echo $position; ?>-mname" type="text" name="<?php echo $position; ?>[mname]" />
				</div><!-- .grid-item -->
				<div class="grid-item">
					<label for="<?php echo $position; ?>-lname">Last Name</label>
					<input id="<?php echo $position; ?>-lname" type="text" name="<?php echo $position; ?>[lname]" />
				</div><!-- .grid-item -->
				<div class="grid-item">
					<label for="<?php echo $position; ?>-contact-no">Contact No. (09*********)</label>
					<input id="<?php echo $position; ?>-contact-no" type="text" 
						name="<?php echo $position; ?>[contact-no]" pattern="^09[0-9]{9}" class="small" />
				</div><!-- .grid-item -->
				<div class="grid-item">
					<label for="<?php echo $position; ?>-bday">Birthday</label>
					<input id="<?php echo $position; ?>-bday" type="date" 
						name="<?php echo $position; ?>[bday]" class="medium" />
				</div><!-- .grid-item -->
				<div class="grid-item">
					<label for="<?php echo $position; ?>-address">Phase, Block, and Lot</label>
					<input id="<?php echo $position; ?>-address" type="text" 
						name="<?php echo $position; ?>[address]" class="medium" />
				</div><!-- .grid-item -->
			</div><!-- .grid-wrapper -->
		</div><!-- .new-data-subject -->
	</div><!-- .personal-details -->

	<div class="center-ball-container"><hr class="center-ball" /></div>

	<div class="account-details">
		<h4>Account Details</h4>
		<div class="grid-wrapper">
			<div class="grid-item">
				<label for="<?php echo $position; ?>-username">Username</label>
				<input id="<?php echo $position; ?>-username" type="text" name="<?php echo $position; ?>[username]"
					class="small" required />
			</div><!-- .grid-item -->
			<div class="grid-item">
				<label for="<?php echo $position; ?>-email">Email</label>
				<input id="<?php echo $position; ?>-email" type="email" name="<?php echo $position; ?>[email]" 
					class="medium" required />
			</div><!-- .grid-item -->
			<div class="grid-item">
				<label for="<?php echo $position; ?>-password">Password</label>
				<input id="<?php echo $position; ?>-password" type="password" 
					name="<?php echo $position; ?>[password]" required />
			</div><!-- .grid-item -->
			<div class="grid-item">
				<label for="<?php echo $position; ?>-confirm-password">Confirm Password</label>
				<input id="<?php echo $position; ?>-confirm-password" type="password" 
					name="<?php echo $position; ?>[confirm-password]" required />
			</div><!-- .grid-item -->
			<div class="grid-item solo">
				<input id="<?php echo $position; ?>-show-passwords" type="checkbox" class="show-password" />
				<label for="<?php echo $position; ?>-show-passwords">Show Passwords</label>
			</div><!-- .grid-item -->
		</div><!-- .grid-wrapper -->
		<p>Looks like you already have an account using <b></b></p>
	</div><!-- .account-details -->
</section><!-- #[position] -->