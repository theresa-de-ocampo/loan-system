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
			],
			order: [[1, "asc"], [3, "desc"]]
		});
		checkForRows($table, id);
		let grandTotal = $table.column(2).data().sum() + $table.column(3).data().sum();
		$(id + "_wrapper + p span.amount").text(grandTotal.toLocaleString("en"));
	}
});