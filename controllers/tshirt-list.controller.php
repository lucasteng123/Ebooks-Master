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
	$tmpl = new Template();
	$tmpl->set_template_file(SITE_PATH.'/templates/full.template.php');
	setup_full_template($tmpl, $sitedb, $ss, $_SESSION);

	// Get tools
	$pdo = $instance->tools[ 'con_manager' ]->get_connection();
	$sql = "SELECT * FROM tshirts";
	// Prepare statement
	$stmt = $pdo->prepare( $sql );
	// Bind values
	$stmt->execute();
	// Fetch results into associative array
	$result = array();
	while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
		$result[] = $row;
	}
	$shirts_tmpl = new Template();
	$shirts_tmpl->set_template_file(SITE_PATH . '/templates/tshirts.template.php');
	$shirts_tmpl->tshirts = $result;
	
	$tmpl->part_bookview = $shirts_tmpl;
	$tmpl->run();
};

$page_controller = new Controller( $methods );
unset( $methods );
