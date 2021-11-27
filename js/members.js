// jshint esversion: 6
$(function() {
	let $guarantorsTbl = $("#guarantors-tbl").DataTable({
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
				customize: addHeaderToPdf,
				messageTop: cycle
			},
			{
				extend: 'csv',
				title: "Guarantors"
			}
		],
		order: [[2, "asc"]]
	});
	checkForRows($guarantorsTbl, "#guarantors-tbl");

	let $savingsTbl = $("#savings-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Savings",
				customize: addHeaderToPdf,
				messageTop: cycle
			},
			{
				extend: 'csv',
				title: "Savings"
			}
		]
	});
	checkForRows($savingsTbl, "#savings-tbl");

	let $dataSubjectsTbl = $("#data-subjects-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Data Subjects",
				exportOptions: {
					columns: [0, 1, 2, 3, 4, 5, 6]
				},
				customize: addHeaderToPdf,
				messageTop: cycle
			},
			{
				extend: 'csv',
				title: "Data Subjects",
				exportOptions: {
					columns: [0, 1, 2, 3, 4, 5, 6]
				},
			}
		],
		columnDefs: [
			{
				targets: [4],
				width: 100
			}
		],
		order: [[2, "asc"]]
	});
	checkForRows($dataSubjectsTbl, "#savings-tbl");

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

		$dataSubjectsTbl.on("click", ".fa-user-edit", function() {
			let $tr = $(this).closest("tr");
			if ($tr.hasClass("child"))
				$tr = $tr.prev();
			let row = $dataSubjectsTbl.row($tr).data();
			
			createModal(data);
			$(".tingle-modal-box #id").val($tr.attr("data-data-subject-id"));
			$(".tingle-modal-box #fname").val(row[0]);
			$(".tingle-modal-box #mname").val(row[1]);
			$(".tingle-modal-box #lname").val(row[2]);
			$(".tingle-modal-box #contact-no").val(row[3]);
			$(".tingle-modal-box #bday").val(formatDate(row[4].display));
			$(".tingle-modal-box #address").val(row[5]);
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

	$.get("inc/new-account-form.html", function(data) {
		$dataSubjectsTbl.on("click", ".fa-plus-square", function() {
			let $tr = $(this).closest("tr");
			if ($tr.hasClass("child"))
				$tr = $tr.prev();
			let row = $dataSubjectsTbl.row($tr).data();
			
			createModal(data);
			$(".tingle-modal-box #id").val($tr.attr("data-data-subject-id"));
			$(".tingle-modal-box #fname").val(row[0]);
			$(".tingle-modal-box #lname").val(row[2]);
			$("#show-passwords").on("click", toggleVisibility);

			$(".tingle-modal-box #modal-ok").on("click", function(e) {
				if (checkIfComplete(e))
					if (checkUsername(e, $("#username").val()))
						if (checkEmail(e, $("#email").val()))
							if (checkPassword(e, $("#password").val()))
								confirmPassword(e, $("#password").val(), $("#confirm-password").val());
			});
		});
	});

	$dataSubjectsTbl.on("click", ".fa-minus-square", function(e) {
		let $tr = $(this).closest("tr");
		if ($tr.hasClass("child"))
			$tr = $tr.prev();
		let row = $dataSubjectsTbl.row($tr).data();
		let fname = row[0];
		let apostropheRule = "'";
		if (fname.slice(fname.length - 1) != "s")
			apostropheRule += "s";
		let ans = confirm("Are you sure you want to delete " + fname + apostropheRule + " account?");
		if (!ans)
			e.preventDefault();
		else {
			const id = $tr.attr("data-data-subject-id");
			window.location.replace("src/delete-account.php?id=" + id);
		}
	});
});