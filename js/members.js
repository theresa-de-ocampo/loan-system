// jshint esversion: 6
$.get("inc/guarantor-form.html", function( data ) {
	$("#guarantors-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				text: "Add",
				action: function() {
					createModal("#add-guarantor", data);
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
});