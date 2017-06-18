<?php
$methods = array();

$methods[ 'error' ] = function ( $instance ) {
	echo '{"error": "Something went wrong"}';
};

$methods[ 'run' ] = function ( $instance ) {
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
	$tmpl = new Template();
	$tmpl->set_template_file(SITE_PATH . '/templates/tshirts.template.php');
	$tmpl->tshirts = $result;
	$tmpl->run();
};

$page_controller = new Controller( $methods );
unset( $methods );
