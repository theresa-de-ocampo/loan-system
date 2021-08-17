// jshint esversion: 6
$("table").DataTable({
	/*
		Button -> Filtering Input -> Processing Display Element -> Table -> 
		Table Information Summary -> Pagination Control
	*/
	dom: "Bfrtip", 
	responsive: true,
	buttons: [
		{
			text: "Add",
			action: function() {
				createModal("#add-guarantor", "hello");
			},
			attr: {
				id: "add-guarantor"
			}
		},
		{
			extend: "print",
			title: "Guarantors"
		},
		{
			extend: 'csv'
		}
	],
	columnDefs: [
		{
			targets: [5],
			width: 100
		}
	]
});