<?php

session_start();

$methods = array();

$methods['run'] = function($instance) {
	if ($_SESSION['logged_in'] === AccountMgr::SESSION_OKAY
		&& $_SESSION['is_admin_user'] === "yes") {

		$con = $instance->tools['con_manager']->get_connection();
		$gc = new GuessContest($con);

		$r = $instance->route;
		$contestID = $r[0];


		$list = $gc->list_contestants_in($contestID);
		{
			if (array_key_exists(1, $r) && $r[1] === 'winners') {
				$phrase = $gc->get_contest_phrase($contestID);
				$newList = array();
				foreach ($list as $item) {
					if ($item['guess'] === $phrase) {
						$newList[] = $item;
					}
				}
				$list = $newList;
			}
		}

		$html = new Template();
		$html->set_template_file(SITE_PATH.'/templates/print.template.php');
		$html->list = $list;
		$html->run();
	} else {
		die("ACCESS DEINED, OR SESSION EXPIRED.");
	}
};

$page_controller = new Controller($methods);
unset($methods);