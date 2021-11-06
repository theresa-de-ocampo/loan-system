// jshint esversion: 6
$(function() {
	$("#back").on("click", function() {
		window.location.href = "transactions.php#loan-disbursements";
	});

	let summaryDetails = $("#loan-info-holder").html().replace(' class="pattern-bg"', "");

	function addHeaderToLoanDetailsPdf(win) {
		$(win.document.body).prepend(summaryDetails);
	}

	function confirmPayment(e, balance) {
		$(".tingle-modal-box form .fa-pencil-alt").on("click", function(e) {
			let amount = parseFloat($(".tingle-modal-box #amount").val());
			if (checkIfComplete(e))
				checkAmount(e, balance, amount);
		});
	}

	// PRINCIPAL
	let $principalPaymentsTbl = $("#principal-payments-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				text: "Pay",
				action: function(e) {
					$.get("inc/principal-payment-form.html", function(data) {
						createModal(data);
						const balance = $("#loan-balance-holder").text();
						$(".tingle-modal-box #loan-id").val($("#loan-id-holder").text());
						$(".tingle-modal-box #balance").val(balance);
						confirmPayment(e, balance);
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
		order: [[2, "asc"]]
	});
	checkForRows($principalPaymentsTbl, "#principal-payments-tbl");
	if ($("#total-receivables-amount").text() === "0.00")
		$("#principal-payment").hide();

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
		],
		order: [[1, "asc"]]
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
		]
	});
	checkForRows($interestPaymentsTbl, "#interest-payments-tbl");
	
	$.get("inc/interest-payment-form.html", function(data) {
		$("#interests-tbl td a").on("click", function(e) {
			$button = $(this);
			createModal(data);

			let balance = $button.attr("data-interest-balance");
			$(".tingle-modal-box #loan-id").val($button.attr("data-loan-id"));
			$(".tingle-modal-box #interest-id").val($button.attr("data-interest-id"));
			$(".tingle-modal-box #balance").val(balance);
			confirmPayment(e, balance);
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
		],
		order: [[1, "asc"]]
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
		]
	});
	checkForRows($penaltyPaymentsTbl, "#penalty-payments-tbl");

	$.get("inc/penalty-payment-form.html", function(data) {
		$("#penalties-tbl td a").on("click", function(e) {
			$button = $(this);
			createModal(data);

			let balance = $button.attr("data-penalty-balance");
			$(".tingle-modal-box #loan-id").val($button.attr("data-loan-id"));
			$(".tingle-modal-box #penalty-id").val($button.attr("data-penalty-id"));
			$(".tingle-modal-box #balance").val(balance);
			confirmPayment(e, balance);
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
		],
		order: [[1, "asc"]]
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
		]
	});
	checkForRows($processingFeePaymentsTbl, "#processing-fee-payments-tbl");

	$.get("inc/processing-fee-payment-form.html", function(data) {
		$("#processing-fees-tbl td a").on("click", function(e) {
			$button = $(this);
			createModal(data);

			let balance = $button.attr("data-processing-fee-balance");
			$(".tingle-modal-box #loan-id").val($button.attr("data-loan-id"));
			$(".tingle-modal-box #processing-fee-id").val($button.attr("data-processing-fee-id"));
			$(".tingle-modal-box #balance").val(balance);
			confirmPayment(e, balance);
		});
	});
});