// jshint esversion: 6
$("#guarantors-tbl").DataTable({
	dom: "Bfrtip", 
	responsive: true,
	buttons: [
		{
			text: "Add",
			action: function() {
				window.location.href = "add-guarantor.php";
			},
			attr: {
				id: "add-guarantor"
			}
		},
		{
			extend: "print",
			title: "Guarantors",
			exportOptions: {
				columns: [0, 1, 2, 3, 4, 5, 6, 7]
			}
		},
		{
			extend: 'csv',
			title: "Guarantors",
			exportOptions: {
				columns: [0, 1, 2, 3, 4, 5, 6, 7]
			}
		}
	],
	columnDefs: [
		{
			targets: [5],
			width: 100
		}
	]
});

$("#savings-tbl").DataTable({
	dom: "Bfrtip", 
	responsive: true,
	buttons: [
		{
			extend: "print",
			title: "Savings"
		},
		{
			extend: 'csv',
			title: "Savings"
		}
	]
});