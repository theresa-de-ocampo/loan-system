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
	]
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
	]
});

$("table td a").on("click", function() {

});