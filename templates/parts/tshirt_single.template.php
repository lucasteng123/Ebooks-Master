<?php

$colors = explode(",",$tshirt["colors"]);
if (isset($disableLink)) {
echo('<div class="col-md-3 col-xs-6">
	<span class="thumbnail tshirt-thumb">
		<img src="' . WEB_PATH . $tshirt["image"] . '">');
} else {
echo('<div class="col-md-3 col-xs-6">
	<a href="/order/details/' . $tshirt["id"] . '" class="thumbnail tshirt-thumb">
		<img src="' . WEB_PATH . $tshirt["image"] . '">');
}
	?>
	<span class="collapse" data-name="id"><?php echo $tshirt["id"]; ?></span>
	<span class="collapse" data-name="name"><?php echo $tshirt["name"]; ?></span>
	<span class="collapse" data-name="image"><?php echo $tshirt["image"]; ?></span>
	<span class="collapse" data-name="colors"><?php echo $tshirt["colors"]; ?></span>
	<span class="collapse" data-name="price"><?php echo $tshirt["price"]; ?></span>
	<span class="collapse" data-name="size"><?php echo $tshirt["size"]; ?></span>
	<span class="collapse" data-name="description"><?php echo $tshirt["description"]; ?></span>
	<?php
	foreach($colors as $color){
		echo('<div class="colorSquare col-md-1 col-xs-1" style="background-color: ' . $color . '" >&nbsp;</div>');
	}
		
		echo('<h3>'.$tshirt["name"].'</h3>
		<h4 class="price">$' . $tshirt["price"] . '</h4>');
	echo (isset($disableLink)) ? "</span>" : "</a>"
?>
</div>
