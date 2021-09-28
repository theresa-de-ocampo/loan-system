<?php

class UploadFile {
	public function codeToMessage($code) {
		switch ($code) {
			case UPLOAD_ERR_INI_SIZE: // The uploaded file exceeds the upload_max_filesize directive in php.ini
			case UPLOAD_ERR_FORM_SIZE: // The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form
				$message = "Sorry, please upload a file that is less than 2MB.";
				break;
			case UPLOAD_ERR_PARTIAL:
				$message = "Sorry, an error occurred. The file was only partially uploaded";
				break;
			case UPLOAD_ERR_NO_FILE:
				$message = "No file was uploaded.";
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$message = "Sorry, an error occurred. Missing a temporary folder";
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$message = "Sorry, an error occurred. Failed to write file to disk";
				break;
			case UPLOAD_ERR_EXTENSION:
				$message = "Sorry, an error occurred. File upload stopped by extension";
				break;
			default:
				$message = "Sorry, there was an error uploading your file.";
				break;
		}
		return $message;
	}

	public function isImage($file_tmp_name) {
		$allowed_types = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
		$detected_type = exif_imagetype($file_tmp_name);
		return in_array($detected_type, $allowed_types);
	}

	public function isPDF($file_tmp_name) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $file_tmp_name);
		return $mime == "application/pdf";
	}
}