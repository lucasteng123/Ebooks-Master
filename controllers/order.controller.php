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
		case "details":
			$id = $r[ 1 ];
			$sql = "SELECT * FROM tshirts t WHERE t.id=:tshirtID";
			// Prepare statement
			$stmt = $pdo->prepare( $sql );
			// Bind values
			$stmt->bindValue( "tshirtID", $id, PDO::PARAM_INT );
			$stmt->execute();
			// Fetch results into associative array
			$result = array();
			while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
				$result[] = $row;
			}
			$tshirt = $result[ 0 ];
			$colors = explode( ",", $tshirt[ "colors" ] );

			$tmpl = new Template();
			$tmpl->set_template_file(SITE_PATH . "/templates/tshirt_details.template.php");
			$tmpl->tshirt = $tshirt;
			$tmpl->id = $id;
			$tmpl->colors = $colors;
			$sizes = explode(",", $tshirt["size"]);
			$tmpl->sizes = $sizes;

			$html->part_bookview = $tmpl;
			$html->run();

			break;
		case "place":
			//get POST variables
			echo( '<html>' );
			print_r($_POST);
			$id = $r[ 1 ];
			$color = $_POST[ "colors" ];
			$pretty = chr( mt_rand( 97, 122 ) ) . substr( md5( time() ), 1 );
			$email = $_POST[ "email" ];
			$quantity = $_POST[ "quantity" ];
			$size = $_POST["size"];

			$insert_tshirt = "INSERT INTO orders (pretty_id, tshirt_id, color, email, quantity) VALUES (:pid, :id, :color, :email, :quantity)";
			$stmt = $pdo->prepare( $insert_tshirt );
			// Bind variables
			$stmt->bindValue( "pid", $pretty, PDO::PARAM_STR );
			$stmt->bindValue( "id", $id, PDO::PARAM_STR );
			$stmt->bindValue( "color", $color, PDO::PARAM_STR );
			$stmt->bindValue( "email", $email, PDO::PARAM_STR );
			$stmt->bindValue( "quantity", $quantity, PDO::PARAM_STR );
			// Insert the row
			$stmt->execute();

			echo "added order\n";
			echo "tracking link: <a href = \"http://ebooktesting.lucasteng.com/order/track/" . $pretty . "\" > http://ebooktesting.lucasteng.com/order/track/" . $pretty . "</a>";


			echo( "</html>" );
			break;

		case "track":
			echo( '<html>
			<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="tshirts.css">
	<title>T-Shirt Tracking</title>
</head><body><div class="container"><div class = "row">' );
			$pretty = $r[ 1 ];

			$sql = "SELECT * FROM orders o WHERE o.pretty_id=:id";
			// Prepare statement
			$stmt = $pdo->prepare( $sql );
			// Bind values
			$stmt->bindValue( "id", $pretty, PDO::PARAM_INT );
			$stmt->execute();
			// Fetch results into associative array
			$result = array();
			while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
				$result[] = $row;
			}
			$order = $result[ 0 ];

			echo '<h1>order #' . $order[ "order_id" ] . '</h1><hr>';
			echo '<p><strong>color: </strong> <span style="background-color:' . $order[ "color" ] . ';">'.$order["color"] .'</p>';
			echo '<p><a href="/order/details/' . $order[ "tshirt_id" ] . '"><strong>tshirt: </strong>' . $order[ "tshirt_id" ] . '</a></p>';
			echo '<p><strong>quantity: </strong>' . $order[ "quantity" ] . '</p>';
			echo '<p><strong>email: </strong>' . $order[ "email" ] . '</p>';

			echo( "</body></div></div></html>" );
			break;

		case "track-list":
			echo( '<html>
			<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="tshirts.css">
	<title>T-Shirt Update</title>
</head><body><div class="container">' );

			$sql = "SELECT * FROM orders o";
			// Prepare statement
			$stmt = $con->prepare( $sql );
			// Bind values
			$stmt->execute();
			// Fetch results into associative array
			$result = array();
			while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
				$result[] = $row;
			}
			foreach($result as $order){
				echo '<div class = "row">';
				echo '<h1>order #' . $order[ "order_id" ] . '</h1><hr>';
				echo "<p>tracking link: <a href = \"http://ebooktesting.lucasteng.com/order/track/" . $order["pretty_id"] . "\" > http://ebooktesting.lucasteng.com/order/track/" . $order["pretty_id"] . "</a></p>";
				echo '<p><strong>color: </strong> <span style="background-color:' . $order[ "color" ] . ';">'.$order["color"] .'</p>';
				echo '<p><a href="/order/details/' . $order[ "tshirt_id" ] . '"><strong>tshirt: </strong>' . $order[ "tshirt_id" ] . '</a></p>';
				echo '<p><strong>quantity: </strong>' . $order[ "quantity" ] . '</p>';
				echo '<p><strong>email: </strong>' . $order[ "email" ] . '</p>';
				echo '</div>';
			}
			echo( "</body></div></html>" );
			break;


	}
	/*

	//get POST variables
	if ( $_POST[ "image" ] && $_POST[ "colors" ] && $_POST[ "name" ] && $_POST[ "price" ] ) {
		$image = $_POST[ "image" ];
		$colors = $_POST[ "colors" ];
		$name = $_POST[ "name" ];
		$price = $_POST[ "price" ];
	} else {
		echo "no post";
	}
	//if there are no t-shirt ids passed
	if ( count( $r ) < 1 ) {
		//create the TShirt
		$insert_tshirt = "INSERT INTO tshirts (image, name, colors, price) VALUES (:img, :nm, :color, :price)";
		$stmt = $pdo->prepare( $insert_tshirt );
		// Bind variables
		$stmt->bindValue( "img", $image, PDO::PARAM_STR );
		$stmt->bindValue( "nm", $name, PDO::PARAM_STR );
		$stmt->bindValue( "color", $colors, PDO::PARAM_STR );
		$stmt->bindValue( "price", $price, PDO::PARAM_STR );
		// Insert the row
		$stmt->execute();
		echo "created tshirt";
		//if there is a tshirt id passed
	} else {
		$tshirtID = $r[ 0 ];
		// === check for existing t-shirts === //
		$sql = "SELECT * FROM tshirts t WHERE t.id=:tshirtID";
		// Prepare statement
		$stmt = $pdo->prepare( $sql );
		// Bind values
		$stmt->bindValue( "tshirtID", $tshirtID, PDO::PARAM_INT );
		$stmt->execute();
		// Fetch results into associative array
		$result = array();
		while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
			$result[] = $row;
		}


		//if there is no tshirt with this ID, create it
		if ( count( $result ) != 1 ) {

			$insert_tshirt = "INSERT INTO tshirts (image, name, colors, price) VALUES (:img, :nm, :color, :price)";
			$stmt = $pdo->prepare( $insert_tshirt );
			// Bind variables
			$stmt->bindValue( "img", $image, PDO::PARAM_STR );
			$stmt->bindValue( "nm", $name, PDO::PARAM_STR );
			$stmt->bindValue( "color", $colors, PDO::PARAM_STR );
			$stmt->bindValue( "price", $price, PDO::PARAM_STR );
			// Insert the row
			$stmt->execute();
			echo "created tshirt";

		} else {
			//set last version of shirt as inactive
			$insert_tshirt = "UPDATE tshirts SET active = 0 where id = :id";
			$stmt = $pdo->prepare( $insert_tshirt );
			// Bind variables
			$stmt->bindValue( "id", $r[ 0 ], PDO::PARAM_INT );
			// Insert the row
			$stmt->execute();
			echo "updated old tshirt";
			
			//add updated tshirt
			$insert_tshirt = "INSERT INTO tshirts (image, name, colors, price) VALUES (:img, :nm, :color, :price)";
			$stmt = $pdo->prepare( $insert_tshirt );
			// Bind variables
			$stmt->bindValue( "img", $image, PDO::PARAM_STR );
			$stmt->bindValue( "nm", $name, PDO::PARAM_STR );
			$stmt->bindValue( "color", $colors, PDO::PARAM_STR );
			$stmt->bindValue( "price", $price, PDO::PARAM_STR );
			// Insert the row
			$stmt->execute();
			echo "created new tshirt";
		}
	}*/
};

$page_controller = new Controller( $methods );
unset( $methods );