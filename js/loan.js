// jshint esversion: 6
$(function() {
	const $tables = $("table");
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
					customize: addHeaderToPdf,
					messageTop: cycle
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