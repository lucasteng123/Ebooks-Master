<?php
if (array_key_exists("submit", $_POST)) {
	try {
		$accmgr->login_with_username($_POST['user'],$_POST['pass']);
		unset($_SESSION['attempts']);
	} catch (AccountMgrException $e) {
		/*switch ($e->getCode()) {
			case AccountMgrException::INCORRECT_PASSWORD:
				if (isset($_SESSION['attempts'])) $_SESSION['attempts'] += 1;
				else $_SESSION['attempts'] = 1;
		}*/
		$json = array(
			'status' => 'error',
			'message' => $e->getMessage()
		);
		ob_clean();
		echo json_encode($json);
		return;
	} catch (PDOException $e) {
		$json = array(
			'status' => 'error',
			'message' => "The following internal error occured: ".$e->getMessage()
		);
		ob_clean();
		echo json_encode($json);
		return;
	}

	$json = array(
		'status' => 'okay',
		'message' => "Login okay!"
	);
	ob_clean();
	echo json_encode($json);
	return;
}
