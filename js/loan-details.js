// jshint esversion: 6
$(function() {
	let summaryDetails = $("#loan-info-holder").html();

	function addHeaderToLoanDetailsPdf(win) {
		$(win.document.body).prepend(summaryDetails);
	}

	function confirmPayment() {
		$(".tingle-modal-box form .fa-pencil-alt").on("click", function(e) {
			let ans = confirm("Proceed with payment?");
			if (!ans)
				e.preventDefault();
		});
	}

	// PRINCIPAL
	let $principalPaymentsTbl = $("#principal-payments-tbl").DataTable({
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
				title: "Principal Payments",
				customize: addHeaderToLoanDetailsPdf
			},
			{
				extend: "csv",
				title: "Principal Payments"
			}
		],
		order: []
	});
	checkForRows($principalPaymentsTbl, "#principal-payments-tbl");

	// INTERESTS
	let $interestsTbl = $("#interests-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Interests",
				customize: addHeaderToLoanDetailsPdf
			},
			{
				extend: "csv",
				title: "Interests"
			}
		]
	});
	checkForRows($interestsTbl, "#interests-tbl");

	let $interestPaymentsTbl = $("#interest-payments-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Interest Payments",
				customize: addHeaderToLoanDetailsPdf
			},
			{
				extend: "csv",
				title: "Interest Payments"
			}
		],
		order: []
	});
	checkForRows($interestPaymentsTbl, "#interest-payments-tbl");
	
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

	// PENALTIES
	let $penaltiesTbl = $("#penalties-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Penalties",
				customize: addHeaderToLoanDetailsPdf
			},
			{
				extend: "csv",
				title: "Penalties"
			}
		]
	});
	checkForRows($penaltiesTbl, "#penalties-tbl");

	let $penaltyPaymentsTbl = $("#penalty-payments-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Penalty Payments",
				customize: addHeaderToLoanDetailsPdf
			},
			{
				extend: "csv",
				title: "Penalty Payments"
			}
		],
		order: []
	});
	checkForRows($penaltyPaymentsTbl, "#penalty-payments-tbl");

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

	// PROCESSING FEES
	let $processingFeesTbl = $("#processing-fees-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Processing Fees",
				customize: addHeaderToLoanDetailsPdf
			},
			{
				extend: "csv",
				title: "Processing Fees"
			}
		]
	});
	checkForRows($processingFeesTbl, "#processing-fees-tbl");

	let $processingFeePaymentsTbl = $("#processing-fee-payments-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Processing Fee Payments",
				customize: addHeaderToLoanDetailsPdf
			},
			{
				extend: "csv",
				title: "Processing Fee Payments"
			}
		],
		order: []
	});
	checkForRows($processingFeePaymentsTbl, "#processing-fee-payments-tbl");

	$.get("inc/processing-fee-payment-form.html", function(data) {
		$("#processing-fees-tbl td a").on("click", function() {
			$button = $(this);
			createModal(data);

			$(".tingle-modal-box #loan-id").val($button.attr("data-loan-id"));
			$(".tingle-modal-box #processing-fee-id").val($button.attr("data-processing-fee-id"));
			$(".tingle-modal-box #balance").val($button.attr("data-processing-fee-balance"));
			confirmPayment();
		});
	});
});