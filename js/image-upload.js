// jshint esversion: 6
$(function() {
	const $dropArea = $("#drop-area");
	let file;
	let $instructions = $("#drop-area > div:nth-of-type(2)");
	let $browseButton = $("#drop-area button");
	let $imageField = $("#drop-area + input");

	function displayPreview() {
		let fileType = file.type;
		let validExtensions = ["image/jpeg", "image/jpg", "image/png"];

		if (validExtensions.includes(fileType)) {
			let fileReader = new FileReader();
			fileReader.onload = () => {
				let fileUrl = fileReader.result;
				let imgTag = `<img src="${fileUrl}" alt="Your uploaded image." />`;
				$dropArea.html(imgTag);
				$(document).on("click", "#drop-area img", function() {
					$imageField.trigger("click");
				});
			};
			fileReader.readAsDataURL(file); // Get base64 format of the image.
		}
		else {
			alert("Please upload image files only.");
			if ($("#drop-area img").length == 0) {
				$dropArea.removeClass("active");
				$instructions.html("Drag &amp; Drop to Upload File");
			}
		}
	}

	$dropArea.on("dragover", function(e) {
		e.preventDefault();
		$dropArea.addClass("active");
		$instructions.text("Release to Upload File");
	});

	$dropArea.on("dragleave", function() {
		$dropArea.removeClass("active");
		$instructions.html("Drag &amp; Drop to Upload File");
	});

	$dropArea.on("drop", function(e) {
		e.preventDefault();
		file = e.originalEvent.dataTransfer.files[0]; // Retrieve the first file only if user selected multiple files
		displayPreview();
	});

	$browseButton.on("click", function() {
		$imageField.trigger("click");
	});

	$imageField.on("change", function() {
		file = this.files[0];
		$dropArea.addClass("active");
		displayPreview();
	});
});