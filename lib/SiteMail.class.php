<?php
// Very website-specific mail class

class SiteMail {
	private $admin_addr = "bestebooks.ca@gmail.com";
	private $use_fake_mail = false;

	public function set_fake_mail($val) {
		if ($val == true) $this->use_fake_mail = true;
		else $this->use_fake_mail = false;
	}

	public function send_user_email($email,$subject_line,$tmpl) {
		$headers = "From: noreply@bestebooks.ca\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

		ob_start();
		$tmpl->run();
		$html = ob_get_clean();


		return $this->mail_it($email, $subject_line, $html, $headers);
	}
	public function send_admin_notice_upload($email,$activation_code,$tmpl) {
		$subject_line = "BestEBooks Account Activation";

		$headers = "From: noreply@bestebooks.ca\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

		ob_start();
		$tmpl->run();
		$html = ob_get_clean();


		return $this->mail_it($email, $subject_line, $html, $headers);
	}
	private function mail_it($email, $subject, $html, $headers) {
		if ($this->use_fake_mail) {
			return $this->relay_mail_to_dubedev($email, $subject, $html, $headers);
		} else {
			return mail($email, $subject, $html, $headers);
		}
	}
	private function relay_mail_to_dubedev($email, $subject, $html, $headers) {
		$url = "http://dubedev.com/scripts/sendmail.php";
		$data = array(
			'email' => $email,
			'subject' => $subject,
			'html' => $html,
			'headers' => $headers,
		);
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data),
			),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		if ($result == "true") {
			return true;
		}
		echo $result."<br />\r\n";
		return false;
	}
}
