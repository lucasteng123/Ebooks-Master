<?php
$methods = array();

$methods[ 'error' ] = function ( $instance ) {
	echo '{"error": "Something went wrong"}';
};

$methods[ 'run' ] = function ( $instance ) {
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
			$sizes = explode(",", $tshirt["size"]);

			echo( '<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="tshirts.css">
	<title>T-Shirt Update</title>
</head>
<body>
<div class="container">
	<div class="row">
		<div class="col-md-7">
			<img src = "' . $tshirt[ "image" ] . '" class="img-thumbnail" />
		</div>
		<div class="col-md-5">
			<h1>' . $tshirt[ "name" ] . '</h1>
			<h4 style="font-weight: 200">' . $tshirt[ "description" ] . '</h4>
			<hr>
			<div class="alert alert-warning"> 
      	<strong>Tip:</strong> For order issues contact support@bestebooks.ca 
      		</div>
			
			<h3><strong>$' . $tshirt[ "price" ] . '</strong><span style="font-weight: 200">/shirt</span></h3>
			<form action="/order/place/' . $id . '" method="post">
				<div class="form-group">
					<label for="quantity"> Quantity </label>
					<input type="text" class="form-control" name="quantity">
				</div>
				<h4>Please choose colour</h4><div class="col-md-12">' );
			foreach ( $colors as $color ) {
				echo( '
				<div class="form-group col-md-2" style="text-align:center; padding:0px 10px; background-color: ' . $color . ';">
					<input type="radio" class="form-control" style="display: inline-block;" value="' . $color . '" name="colors">
				</div>' );
			}
			echo('</div><div class="row">
         <div class="form-group">
          <label class="control-label col-sm-3">Size</label>
          <div class="text-right col-sm-9">
            <div id="button1idGroup" class="btn-group" role="group" aria-label="Button Group">');
			foreach ( $sizes as $size ) {
				echo( '
				<button type="button" id="button1id" name="button1id" class="btn btn-default" aria-label="Left Button">' . $size . '</button>' );
			}
			echo( '
				
				</div>
            <p class="help-block">Select the size you wish to purchase</p>
          </div>
        </div>
		</div>
				<div class="form-group">
					<label for="email"> Email Address </label>
					<input type="text" class="form-control" name="email">
				</div>
				<div class="alert alert-danger">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
					<strong>Error:</strong> I will appear here if an error occurs
				</div>
				<div class="row">
          <div class="col-md-9 col-xs-9">
            <label for="quantity"> Quantity: </label>
            <select name="quantity">');
              for($i=1; $i<=10; $i++){
				  echo('<option>'.$i.'</option>'); 
			  }
			echo('
            </select>
          </div>
				<div class="col-md-3 col-xs-3">
				<div class="form-group">
					<input class="btn btn-primary" type="submit">
				</div>
				</div>
				</div>
			</form>	

		</div>
	</div>

	

		
</div>
</body>
</html>' );
			break;
		case "place":
			//get POST variables
			echo( '<html>' );
			$id = $r[ 1 ];
			$color = $_POST[ "colors" ];
			$pretty = chr( mt_rand( 97, 122 ) ) . substr( md5( time() ), 1 );
			$email = $_POST[ "email" ];
			$quantity = $_POST[ "quantity" ];

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
			$stmt = $pdo->prepare( $sql );
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