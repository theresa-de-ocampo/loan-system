// jshint esversion: 6
jQuery.fn.dataTable.Api.register("sum()", function () {
	return this.flatten().reduce( function ( a, b ) {
		if ( typeof a === "string" )
			a = a.replace(/[^\d.-]/g, "") * 1;
		if ( typeof b === "string" )
			b = b.replace(/[^\d.-]/g, "") * 1;
		return a + b;
	}, 0 );
});

let coopInfo = $("#coop-info-holder").html();
let cycle = "&copy; Cycle " + $("#cycle-holder").text();

function addHeaderToPdf(win) {
	$(win.document.body).prepend(coopInfo);
}

function checkForRows($table, $id) {
	if (!$table.data().count()) {
		$($id + "_wrapper .dt-buttons button.buttons-print").hide();
		$($id + "_wrapper .dt-buttons button.buttons-csv").hide();
	}
}

function getPerson($table, $tr) {
	if ($tr.hasClass("selected"))
		$tr.removeClass("selected");
	else {
		$table.$("tr.selected").removeClass("selected");
		$tr.addClass("selected");
	}
	
	if ($tr.hasClass("child"))
		$tr = $tr.prev();
	let data = $table.row($tr).data();
	let id = data[0];
	let name = data[1] + " " + data[3];
	return [id, name];
}