<?php
class Converter {
	public function bdayToAge($bday) {
		$db = new Database();
		$db->query("CALL calculate_age(?, @age)");
		$db->bind(1, $bday);
		$db->execute();

		$db->query("SELECT @age");
		return $db->resultColumn();
	}

	public function shortToLongDate($date) {
		$time_stamp = strtotime($date);
		$format = "M. j, Y";
		$formatted_date = date($format, $time_stamp);

		if (strpos($formatted_date, "May") !== false)
			$formatted_date = str_replace(".", "", $formatted_date);
		else if (strpos($formatted_date, "Jun") !== false) 
			$formatted_date = str_replace(".", "e", $formatted_date);
		else if (strpos($formatted_date, "Jul") !== false)
			$formatted_date = str_replace(".", "y", $formatted_date);
		return $formatted_date;
	}

	public function shortToLongDateTime($datetime) {
		$time_stamp = strtotime($datetime);
		$format = "M. j, Y \\a\\t g:i A";
		$formatted_date = date($format, $time_stamp);

		if (strpos($formatted_date, "May") !== false)
			$formatted_date = str_replace(".", "", $formatted_date);
		else if (strpos($formatted_date, "Jun") !== false) 
			$formatted_date = str_replace(".", "e", $formatted_date);
		else if (strpos($formatted_date, "Jul") !== false)
			$formatted_date = str_replace(".", "y", $formatted_date);
		return $formatted_date;
	}

	public function roundDown($n, $precision = 2) {
		if ($precision < 0)
			$precision = 0;
		$numPointPosition = intval(strpos($n, "."));
		if ($numPointPosition === 0) // If $n is an integer
			return $n;
		return floatval(substr($n, 0, $numPointPosition + $precision + 1));
	}
}