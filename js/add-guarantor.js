// jshint esversion: 6
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
		]
	});

	$("table").on("click", "tr", function() {
		let $tr = $(this);

		if ($tr.hasClass("selected"))
			$tr.removeClass("selected");
		else {
			$table.$("tr.selected").removeClass("selected");
			$tr.addClass("selected");
		}
		
		if ($tr.hasClass("child"))
			$tr = $tr.prev();
		let data = $table.row($tr).data();
		let personId = data[0];
		let person = data[1] + " " + data[3];
		
		$("#data-subject-id").val(personId);
		$("#name").val(person);
	});
});

$(".far, input[readonly]").on("click", function() {
	createModal("<div class='info'>Select from the previous data subjects in the table below, or click the <b>New</b> button if the guarantor has no previous records.</div>");
});

$("button[type='reset']").on("click", function() {
	window.location.replace("members.php");
});