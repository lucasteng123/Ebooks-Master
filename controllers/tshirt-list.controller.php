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
	foreach($result as $tshirt){
		$colors = explode(",",$tshirt["colors"]);
		
		echo('<div class="col-md-3 col-xs-6">
			<a href="/order/details/' . $tshirt["id"] . '" class="thumbnail tshirt-thumb">
				<img src="' . $tshirt["image"] . '">');
			foreach($colors as $color){
				echo('<div class="colorSquare col-md-1 col-xs-1" style="background-color: ' . $color . '" >&nbsp;</div>');
			}
				
				echo('<h3>'.$tshirt["name"].'</h3>
				<h4 class="price">$' . $tshirt["price"] . '</h4>
			</a>
		</div>');
			
		
	}
};

$page_controller = new Controller( $methods );
unset( $methods );