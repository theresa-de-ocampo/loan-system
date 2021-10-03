<table>
	<tr>
		<th>Loan ID:</th>
		<td><?php echo $id; ?></td>
	</tr>
	<tr>
		<th>Principal:</th>
		<td><span>&#8369;</span> <?php echo number_format($loan_record->principal, 2, ".", ","); ?></td>
	</tr>
	<tr>
		<th>Borrower:</th>
		<td><?php echo $data_subject->getName($loan_record->borrower_id); ?></td>
	</tr>
	<tr>
		<th>Guarantor:</th>
		<td><?php echo $data_subject->getName($loan_record->guarantor_id); ?></td>
	</tr>
	<tr>
		<th>Loan Date:</th>
		<td><?php echo $converter->shortToLongDateTime($loan_record->loan_date_time); ?></td>
	</tr>
</table>