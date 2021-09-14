// jshint esversion: 6
$("#principal-payments-tbl").DataTable({
	dom: "Bfrtip", 
	responsive: true,
	buttons: [
		{
			text: "Pay",
			action: function() {
				$.get("inc/principal-payment-form.html", function(data) {
					createModal(data);
					$(".tingle-modal-box #loan-id").val($("#loan-id-holder").text());
					$(".tingle-modal-box #balance").val($("#loan-balance-holder").text());
					confirmPayment();
				});
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
		confirmPayment();
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
	],
	order: []
});

$.get("inc/penalty-payment-form.html", function(data) {
	$("#penalties-tbl td a").on("click", function() {
		$button = $(this);
		createModal(data);

		$(".tingle-modal-box #loan-id").val($button.attr("data-loan-id"));
		$(".tingle-modal-box #penalty-id").val($button.attr("data-penalty-id"));
		$(".tingle-modal-box #balance").val($button.attr("data-penalty-balance"));
		confirmPayment();
	});
});

function confirmPayment() {
	$(".tingle-modal-box form .fa-pencil-alt").on("click", function(e) {
		let ans = confirm("Proceed with payment?");
		if (!ans)
			e.preventDefault();
	});
}