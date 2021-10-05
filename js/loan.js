// jshint esversion: 6
$(function() {
	const coopInfo = $("#coop-info-holder").html();
	const $tables = $("table");

	function addHeaderToPdf(win) {
		$(win.document.body).prepend(coopInfo);
	}

	for (const table of $tables) {
		let $table = $(table);
		let title = $table.attr("data-guarantor-name");
		let id = "#" + $table.attr("id");
		$table = $(id).DataTable({
			dom: "Bfrtip",
			responsive: true,
			buttons: [
				{
					extend: "print",
					title: title,
					customize: addHeaderToPdf
				},
				{
					extend: "csv",
					title: title
				}
			]
		});
		checkForRows($table, id);
	}
});