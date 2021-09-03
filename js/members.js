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

$.get("inc/data-subject-form.html", function(data) {
	function formatDate(date) {
		let d = new Date(date);
		let month = '' + (d.getMonth() + 1);
		let day = '' + d.getDate();
		let year = d.getFullYear();

		if (month.length < 2) 
			month = '0' + month;
		if (day.length < 2) 
			day = '0' + day;

		return [year, month, day].join('-');
	}

	$("#guarantors-tbl").on("click", ".fa-user-edit", function() {
		let $tr = $(this).closest("tr");
		if ($tr.hasClass("child"))
			$tr = $tr.prev();
		let row = $("#guarantors-tbl").DataTable().row($tr).data();
		
		createModal(data);
		$(".tingle-modal-box #id").val(row[0]);
		$(".tingle-modal-box #fname").val(row[1]);
		$(".tingle-modal-box #mname").val(row[2]);
		$(".tingle-modal-box #lname").val(row[3]);
		$(".tingle-modal-box #contact-no").val(row[4]);
		$(".tingle-modal-box #bday").val(formatDate(row[5]));
		$(".tingle-modal-box #address").val(row[7]);
		let $form = $("form");
		let origForm = $form.serialize();

		$(".tingle-modal-box #modal-ok").on("click", function(e) {
			if (origForm == $form.serialize()) {
				e.preventDefault();
				alert("No data were changed!");
			}
		});
	});
});