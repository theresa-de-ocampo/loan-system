<table>
	<tr>
		<th>Loan ID:</th>
		<td><?php echo $id; ?></td>
	</tr>
	<tr>
		<th>Principal:</th>
		<td><span>&#8369;</span> <?php echo $loan->principal; ?></td>
	</tr>
	<tr>
		<th>Borrower:</th>
		<td><?php echo $data_subject->getName($loan->borrower_id); ?></td>
	</tr>
	<tr>
		<th>Guarantor:</th>
		<td><?php echo $data_subject->getName($loan->guarantor_id); ?></td>
	</tr>
	<tr>
		<th>Loan Date:</th>
		<td><?php echo $converter->shortToLongDateTime($loan->loan_date_time); ?></td>
	</tr>
</table>