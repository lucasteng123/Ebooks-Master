<?php
const VALIDATE_INPUT_OKAY = 1;
const VALIDATE_INPUT_BAD = 2;
function validate_input($field, &$value) {
	switch($field) {
		case "isbn":
			$user_isbn = $_POST['isbn'];
			$isbn_clean = $isbn->hyphens->removeHyphens($user_isbn);
			if (!$isbn->validation->isbn($isbn_clean)) {
				$json['status'] = "form_error";
				$json['message'] = "You entered an invalid ISBN! :/";
				ob_clean();
				echo json_encode($json);
				return;
			}
			if ( ! $isbn->check->is13($isbn_clean) ) {
				$isbn_clean = $isbn->translate->to13($isbn_clean);
			}
			$isbn_clean = $isbn->hyphens->addHyphens($isbn_clean);
	}
}