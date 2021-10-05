// jshint esversion: 6
$(function() {
	let $cycleTbl = $("#cycle-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Cycle",
				customize: addHeaderToPdf
			},
			{
				extend: 'csv',
				title: "Cycle"
			}
		],
		order: [[0, "desc"]]
	});
	checkForRows($cycleTbl, "#cycle-tbl");

	$cycleTbl.on("click", ".fa-door-open", function() {
		let $tr = $(this).closest("tr");
		if ($tr.hasClass("child"))
			$tr = $tr.prev();
		let data = $cycleTbl.row($tr).data();
		$("#cycle-id").val(data[0]);
		$("#cycle-settings button[type='submit']").trigger("click");
	});

	$cycleTbl.on("click", ".fa-door-closed", function() {
		alert("You're already at cycle " + $("#cycle-id").val() + ".");
	});

	$("input[readonly]").on("click", function() {
		createModal("<div class='info'>View cooperative's state at a specific cycle by checking out from the table below.</div>");
	});
});