// jshint esversion: 6
$.get("inc/guarantor-form.html", function(data) {
	$modal = createModal(data);
	let $table = $("#new-guarantor-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				text: "New",
				action: function() {
					$modal.open();
				},
				attr: {
					id: "add-guarantor"
				}
			}
		]
	});

	$("table").on("click", "tr", function() {
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

$("#new-guarantor form").on("submit", function(e) {
	if (checkIfComplete(e, "#new-guarantor form"))
		checkNumberOfShares(e, "#new-guarantor form");
});

$("body").on("submit", ".tingle-modal form", function(e) {
	if (checkIfComplete(e, ".tingle-modal form"))
		checkNumberOfShares(e, ".tingle-modal form");
});