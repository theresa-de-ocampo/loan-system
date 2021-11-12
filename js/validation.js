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

function checkGuarantorOutstanding(e, guarantorOutstanding, loanAmount) {
	let flag = false;

	if (guarantorOutstanding == 0)
		alert("This guarantor has already maxed out his/her investment.");
	else if (guarantorOutstanding < loanAmount) {
		let encodedMessage = "Insufficient funds! Selected guarantor has only &#8369; ";
		encodedMessage += guarantorOutstanding.toLocaleString("en") + " left.";
		const decodedMessage = $("<div/>").html(encodedMessage).text();
		alert(decodedMessage);
	}
	else 
		flag = true;

	if (!flag)
		e.preventDefault();
	return flag;
}

function checkNewCycle(e, type, message) {
	const positions = ["#auditor", "#treasurer", "#asst-treasurer"];
	const errors = [];
	let i = 0;
	let error;
	for (let qualifier of positions) {
		let $field = $(qualifier + "-" + type);
		if ($field.attr("required") === "required") {
			let errorFlag = false;
			switch (type) {
				case "username":
					errorFlag = $field.val().length > 8;
					break;
				case "email":
					errorFlag = !checkEmail($field.val());
					break;
				case "password":
					errorFlag = !checkPassword($field.val());
					break;
				case "confirm-password":
					errorFlag = $field.val() != $(qualifier + "-password").val();
					break;
			}
			if (errorFlag) {
				error = qualifier.substring(1);
				if (error == "asst-treasurer")
					error = "assistant treasurer";
				errors[i++] = error;
			}
		}
	}

	let formattedError = "";
	let position;
	if (errors.length == 0)
		return true;
	else {
		if (errors.length == 1 )
			alert(message + " (" + errors[0] + ").");
		else {
			for (position of errors.slice(0, -1))
				formattedError += position + ", ";
			alert(message + " (" + formattedError + errors.slice(-1) + ").");
		}
		e.preventDefault();
		return false;
	}
}

function checkUsernames(e) {
	return checkNewCycle(e, "username", "Username must not be longer than 8 characters");
}

function checkEmail(email) {
	const regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	return regex.test(email);
}

function checkEmails(e) {
	return checkNewCycle(e, "email", "Invalid email address");
}

function checkPassword(password) {
	const regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@\-#\s$%^&*]{8,}$/;
	return regex.test(password);
}

function checkPasswords(e) {
	const message = "Password should be a minimum of 8 characters consisting of at least one uppercase letter, one lowercase letter, and a digit";
	return checkNewCycle(e, "password", message);
}

function confirmPasswords(e) {
	return checkNewCycle(e, "confirm-password", "Passwords do not match");
}

function checkNewCycleEntities(e) {
	const $fields = [$("#auditor-id"), $("#treasurer-id"), $("#asst-treasurer-id")];
	let dataSubjectIds = [];

	for (let $field of $fields)
		if ($field.attr("required") === "required")
			dataSubjectIds.push($field.val());

	let valuesSoFar = Object.create(null);
	for (let i = 0; i < dataSubjectIds.length; ++i) {
		let value = dataSubjectIds[i];
		if (value != "undefined")
			if (value in valuesSoFar) {
				e.preventDefault();
				alert("A person may only hold one position per cycle.");
				return false;
			}
		valuesSoFar[value] = true;
	}
	return true;
}