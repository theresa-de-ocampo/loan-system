// jshint esversion: 6
function createModal(selector, content) {
	var modal = new tingle.modal({
		footer: false,
		stickyFooter: false,
		closeMethods: ["overlay", "button", "escape"],
		closeLabel: "Close",
		cssClass: ["modal"],
		beforeClose: function() {
			return true; // close the modal
		}
	});
	modal.setContent(content);
	modal.open();
}