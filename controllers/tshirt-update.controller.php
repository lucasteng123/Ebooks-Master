<?php
$methods = array();

$methods[ 'error' ] = function ( $instance ) {
	echo '{"error": "Something went wrong"}';
};

$methods[ 'insert_tshirt' ] = function ( $instance ) {

	$imgdb = new ImageDB($con);

	$imageID   = $imgdb->add_from_post_request('post_image',"Post Image");
	$imageName = $imgdb->get_image_by_id($imageID)['filename'];

	$image = $_POST[ "image" ];
	$colors = $_POST[ "colors" ];
	$name = $_POST[ "name" ];
	$price = $_POST[ "price" ];
	$size = $_POST[ "size" ];
	$description = $_POST[ "description" ];

	// Add tshirt
	$stmt = $pdo->prepare( $insert_tshirt );
	// Bind variables
	$stmt->bindValue( "img", "/uploads/".$imageName, PDO::PARAM_STR );
	$stmt->bindValue( "nm", $name, PDO::PARAM_STR );
	$stmt->bindValue( "color", $colors, PDO::PARAM_STR );
	$stmt->bindValue( "price", $price, PDO::PARAM_STR );
	$stmt->bindValue( "size", $size, PDO::PARAM_STR );
	$stmt->bindValue( "description", $description, PDO::PARAM_STR );
	// Insert the row
	$stmt->execute();
}

$methods[ 'run' ] = function ( $instance ) {
	$tshirtID = 0;
	// Get tools
	$pdo = $instance->tools[ 'con_manager' ]->get_connection();

	// Get URL variables
	$r = $instance->route;


	$redirect_url = WEB_PATH . "/?location=admin/shirts";
	$message = "No message";

	$insert_tshirt = "INSERT INTO tshirts (image, name, colors, price, size, description) VALUES (:img, :nm, :color, :price, :size, :description)";


	//get POST variables
	if ( $_POST[ "image" ] && $_POST[ "colors" ] && $_POST[ "name" ] && $_POST[ "price" ] ) {
		// Do nothing
	} else {
		$message = "no post";
	}
	$image = $_POST[ "image" ];
	$colors = $_POST[ "colors" ];
	$name = $_POST[ "name" ];
	$price = $_POST[ "price" ];
	//if there are no t-shirt ids passed
	if ( count( $r ) < 1 ) {
		//create the TShirt
		$stmt = $pdo->prepare( $insert_tshirt );
		// Bind variables
		$stmt->bindValue( "img", $image, PDO::PARAM_STR );
		$stmt->bindValue( "nm", $name, PDO::PARAM_STR );
		$stmt->bindValue( "color", $colors, PDO::PARAM_STR );
		$stmt->bindValue( "price", $price, PDO::PARAM_STR );
		$stmt->bindValue( "size", $size, PDO::PARAM_STR );
		$stmt->bindValue( "description", $description, PDO::PARAM_STR );
		// Insert the row
		$stmt->execute();
		$message = "created tshirt";
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
			$stmt = $pdo->prepare( $insert_tshirt );
			// Bind variables
			$stmt->bindValue( "img", $image, PDO::PARAM_STR );
			$stmt->bindValue( "nm", $name, PDO::PARAM_STR );
			$stmt->bindValue( "color", $colors, PDO::PARAM_STR );
			$stmt->bindValue( "price", $price, PDO::PARAM_STR );
			$stmt->bindValue( "size", $size, PDO::PARAM_STR );
			$stmt->bindValue( "description", $description, PDO::PARAM_STR );
			// Insert the row
			$stmt->execute();
			$message = "created tshirt";

		} else {
			//set last version of shirt as inactive
			$insert_tshirt = "UPDATE tshirts SET active = 0 where id = :id";
			$stmt = $pdo->prepare( $insert_tshirt );
			// Bind variables
			$stmt->bindValue( "id", $r[ 0 ], PDO::PARAM_INT );
			// Insert the row
			$stmt->execute();
			$message = "updated old tshirt";
			
			//add updated tshirt
			$stmt = $pdo->prepare( $insert_tshirt );
			// Bind variables
			$stmt->bindValue( "img", $image, PDO::PARAM_STR );
			$stmt->bindValue( "nm", $name, PDO::PARAM_STR );
			$stmt->bindValue( "color", $colors, PDO::PARAM_STR );
			$stmt->bindValue( "price", $price, PDO::PARAM_STR );
			$stmt->bindValue( "size", $size, PDO::PARAM_STR );
			$stmt->bindValue( "description", $description, PDO::PARAM_STR );
			// Insert the row
			$stmt->execute();
			$message = "created new tshirt";
		}
	}
	?>
	<h2><?php echo $message; ?></h2>
	<a href="<?php echo $redirect_url; ?>">Return to admin page</a>
	<?php
};

$page_controller = new Controller( $methods );
unset( $methods );