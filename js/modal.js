// jshint esversion: 6
function createModal(content) {
	let $modal = new tingle.modal({
		footer: false,
		stickyFooter: false,
		closeMethods: ["overlay", "button", "escape"],
		closeLabel: "Close",
		cssClass: ["modal"],
		beforeClose: function() {
			return true; // close the modal
		},
		onClose: function() {
			$modal.destroy();
		}
	});
	$modal.setContent(content);
	$modal.open();
}