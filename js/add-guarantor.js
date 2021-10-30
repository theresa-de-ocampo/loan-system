// jshint esversion: 6
$(function() {
	$.get("inc/guarantor-form.html", function(data) {
		let $table = $("#new-guarantor-tbl").DataTable({
			dom: "Bfrtip", 
			responsive: true,
			buttons: [
				{
					text: "New",
					action: function() {
						createModal(data);
					},
					attr: {
						id: "add-guarantor"
					}
				}
			],
			order: [[3, "asc"]]
		});

		$table.on("click", "tbody tr", function() {
			let $tr = $(this);
			let person = getPerson($table, $tr);
			$("#data-subject-id").val(person[0]);
			$("#name").val(person[1]);
		});
	});

	$("input[readonly]").on("click", function() {
		createModal("<div class='info'>Select from the previous data subjects in the table below, or click the <b>New</b> button if the guarantor has no previous records.</div>");
	});

	$("button[type='reset']").on("click", function() {
		window.location.replace("members.php");
	});

	const formSelector = "#new-guarantor form";
	$(formSelector).on("submit", function(e) {
		if (checkIfComplete(e, formSelector))
			checkNumberOfShares(e, formSelector);
	});

	const modalFormSelector = ".tingle-modal form";
	$("body").on("submit", modalFormSelector, function(e) {
		if (checkIfComplete(e, modalFormSelector))
			checkNumberOfShares(e, modalFormSelector);
	});
});