// jshint esversion: 6
$("#principal-payments-tbl").DataTable({
	dom: "Bfrtip", 
	responsive: true,
	buttons: [
		{
			text: "Pay",
			action: function() {
				window.location.href = "add-guarantor.php";
			},
			attr: {
				id: "principal-payment"
			}
		},
		{
			extend: "print",
			title: "Principal Payments"
		},
		{
			extend: "csv",
			title: "Principal Payments"
		}
	],
	order: []
});

$("#interests-tbl").DataTable({
	dom: "Bfrtip", 
	responsive: true,
	buttons: [
		{
			extend: "print",
			title: "Interests"
		},
		{
			extend: "csv",
			title: "Interests"
		}
	]
});

$("#interest-payments-tbl").DataTable({
	dom: "Bfrtip", 
	responsive: true,
	buttons: [
		{
			extend: "print",
			title: "Interest Payments"
		},
		{
			extend: "csv",
			title: "Interest Payments"
		}
	],
	order: []
});

$("#processing-fees-tbl").DataTable({
	dom: "Bfrtip", 
	responsive: true,
	buttons: [
		{
			extend: "print",
			title: "Processing Fees"
		},
		{
			extend: "csv",
			title: "Processing Fees"
		}
	]
});

$("#processing-fee-payments-tbl").DataTable({
	dom: "Bfrtip", 
	responsive: true,
	buttons: [
		{
			extend: "print",
			title: "Processing Fee Payments"
		},
		{
			extend: "csv",
			title: "Processing Fee Payments"
		}
	],
	order: []
});

$.get("inc/interest-payment-form.html", function(data) {
	$("#interests-tbl td a").on("click", function() {
		$button = $(this);
		createModal(data);

		$(".tingle-modal-box #loan-id").val($button.attr("data-loan-id"));
		$(".tingle-modal-box #interest-id").val($button.attr("data-interest-id"));
		$(".tingle-modal-box #balance").val($button.attr("data-interest-balance"));

		$(".tingle-modal-box form .fa-pencil-alt").on("click", function(e) {
			let ans = confirm("Proceed with payment?");
			if (!ans)
				e.preventDefault();
		});
	});
});

$("#penalties-tbl").DataTable({
	dom: "Bfrtip", 
	responsive: true,
	buttons: [
		{
			extend: "print",
			title: "Penalties"
		},
		{
			extend: "csv",
			title: "Penalties"
		}
	]
});

$("#penalty-payments-tbl").DataTable({
	dom: "Bfrtip", 
	responsive: true,
	buttons: [
		{
			extend: "print",
			title: "Penalty Payments"
		},
		{
			extend: "csv",
			title: "Penalty Payments"
		}
	]
});