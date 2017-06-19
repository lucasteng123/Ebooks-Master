<?php

session_start();
require_once(SITE_PATH.'/scripts/setup_full_template.php');

$methods = array();

$methods[ 'error' ] = function ( $instance ) {
	echo '{"error": "Something went wrong"}';
};

$methods[ 'run' ] = function ( $instance ) {
	// Setup site template
	$con = $instance->tools['con_manager']->get_connection();
	$sitedb = new SiteDB($con);
	$ss = new SiteStrings($con);
	$html = new Template();
	$html->set_template_file(SITE_PATH.'/templates/full.template.php');
	setup_full_template($html, $sitedb, $ss, $_SESSION);

	$tshirtID = 0;
	// Get tools
	$pdo = $instance->tools[ 'con_manager' ]->get_connection();

	// Get URL variables
	$r = $instance->route;
	$mode = $r[ 0 ];

	switch ( $mode ) {
		case "add":
			//get POST variables
			$title = $_POST[ "copy" ];
			$price = $_POST[ "price" ];
			
			$insert_paylist = "INSERT INTO pay_list (copy,price,active) VALUES (:cp, :pr, 1)";
			$stmt = $con->prepare( $insert_paylist );
			// Bind variables
			$stmt->bindValue( "cp", $pretty, PDO::PARAM_STR );
			$stmt->bindValue( "pr", $id, PDO::PARAM_STR );
			// Insert the row
			$stmt->execute();
			echo "added paylist item\n";
			break;
	}
};

$page_controller = new Controller( $methods );
unset( $methods );