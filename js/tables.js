function checkForRows($table, $id) {
	if (!$table.data().count()) {
		$($id + "_wrapper .dt-buttons button.buttons-print").hide();
		$($id + "_wrapper .dt-buttons button.buttons-csv").hide();
	}
}