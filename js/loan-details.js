$("#loan-details-tbl").DataTable({
	dom: "Bfrtip", 
	responsive: true,
	buttons: [
		{
			text: "Principal Payment",
			action: function() {
				window.location.href = "add-guarantor.php";
			},
			attr: {
				id: "principal-payment"
			}
		},
		{
			extend: "print",
			title: "Loan Details"
		},
		{
			extend: "csv",
			title: "Loan Details"
		}
	]/*,
	columnDefs: [
		{
			targets: [1, 6, 9, 11, 15],
			width: 100
		}
	]*/
});