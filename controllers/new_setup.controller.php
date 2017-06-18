<?php

session_start();

$name = $_POST['name'];

$methods = array();

$methods['run'] = function($instance) {
	if (
		//$_SESSION['is_admin_user'] !== "yes"
		//$_SERVER['REMOTE_ADDR'] != "127.0.0.1"
		false
		) {
			die('ERR: ACCESS VIOLATION: '.$_SERVER['REMOTE_ADDR']);
	}
	$con = $instance->tools['con_manager']->get_connection();

	$shirts = new ShirtMgr($con);

	// Setup for database modifications 2015-10-21
	$shirts->destroy_records();
	$shirts->setup();

	/*
	// Setup for database modifications 2015-09-14
	$sql = file_get_contents(SITE_PATH . "/lib/SiteDB.class/table_creation_3.sql");
	$qr = $con->exec($sql);

	// Fix books stored in the previous way 2015-09-19
	$sql = "SELECT * FROM books";
	$stmt = $con->prepare($sql);
	$stmt->execute();
	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		$bookID = $row['id'];
		$cateID = $row['category'];
		if ($cateID === null) continue;
		$sql = "INSERT IGNORE INTO books_categories (book_id, cat_id) VALUES (:book_id, :cat_id)";
		$stmt2 = $con->prepare($sql);
		$stmt2->bindValue("book_id",     $bookID,   PDO::PARAM_INT);
		$stmt2->bindValue("cat_id",      $cateID,   PDO::PARAM_INT);
		$stmt2->execute();
	}
	*/

	/*
	// Setup for username change
	$accmgr = new AccountMgr($con);
	$accmgr->change_username("onlyevv@gmail.com","Administrator");
	*/

	/* Setup for GuessContest 2015-07-26
	$gc = new GuessContest($con);
	$gc->setup();
	*/

	/* before 2015-07-26
	$sitedb = new SiteDB($con);
	$sitedb->setup_newsletter();
	*/
	/*
	try {
		$accmgr = new AccountMgr($con);
		$accmgr->add_permission("tester","site.admin.op"); // ability to manage admin accounts
	} catch (AccountMgrException $e) {
		echo ("AccountMgrException: ".$e->getMessage());
	} catch (PDOException $e) {
		echo ("PDOException: ".$e->getMessage());
	}
	echo "<br />[done] AccountMgr configured.<br /><br />";

	onlyevv@gmail.com
	*/
	/*
	$accmgr = new AccountMgr($con);

	$accmgr->revoke_all_permissions("fake@email.com");
	*/
	/*
	$accountID = null;
	try {
		$accountID = $accmgr->new_account("admin_acc_one","temp1234","Angela","onlyevv@gmail.com");
	} catch (AccountMgrException $e) {
		echo ("AccountMgrException: ".$e->getMessage());
	} catch (PDOException $e) {
		echo ("PDOException: ".$e->getMessage());
	}*/
	/*
	try {
		$accmgr->add_permission("Justtesting","site.admin");
	} catch (AccountMgrException $e) {
		echo ("AccountMgrException: ".$e->getMessage());
	} catch (PDOException $e) {
		echo ("PDOException: ".$e->getMessage());
	}
	*/

	//$ss = new SiteStrings($con);

};


$page_controller = new Controller($methods);
unset($methods);
