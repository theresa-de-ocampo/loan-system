// jshint esversion: 6
$("form").attr("novalidate", true); // Disable HTML5 validation

function checkIfComplete(e, formQualifier = "") {
	let isComplete = true;
	$(formQualifier + " :required").each(function() {
		if ($(this).val() === "")
			isComplete = false;
	});

	if (!isComplete) {
		e.preventDefault();
		alert("Please fill out all required fields!");
	}
	return isComplete;
}

function checkNumberOfShares(e, formQualifier = "") {
	let numberOfShare = $(formQualifier + " .number-of-share").val();
	console.log(numberOfShare);
	if (numberOfShare < 1 || numberOfShare > 5) {
		e.preventDefault();
		alert("Number of shares must be between 1 and 5 (inclusive).");
	}
}

function checkValue(e, amount) {
	const flag = amount > 0;
	if (!flag) {
		e.preventDefault();
		alert("Invalid amount value!");
	}
	return flag;
}

function checkAmount(e, balance, amount) {
	if (amount > balance) {
		e.preventDefault();
		alert("You're trying to pay more than the balance.");
	}
	else {
		if (checkValue(e, amount)) {
			let ans = confirm("Proceed with payment? You won't be able to make changes after this transaction.");
			if (!ans)
				e.preventDefault();
			return true;
		}
	}
	return false;
}