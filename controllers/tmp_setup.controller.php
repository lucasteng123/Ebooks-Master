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
	
	$gc = new GuessContest($con);
	$gc->setup();

	// Setup for username change
	// $accmgr = new AccountMgr($con);
	// $accmgr->force_remove_account_by_email("eric.alex.dube@gmail.com");

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
