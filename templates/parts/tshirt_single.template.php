<?php

$colors = explode(",",$tshirt["colors"]);
		
echo('<div class="col-md-3 col-xs-6">
	<a href="/order/details/' . $tshirt["id"] . '" class="thumbnail tshirt-thumb">
		<img src="' . WEB_PATH . $tshirt["image"] . '">');
	foreach($colors as $color){
		echo('<div class="colorSquare col-md-1 col-xs-1" style="background-color: ' . $color . '" >&nbsp;</div>');
	}
		
		echo('<h3>'.$tshirt["name"].'</h3>
		<h4 class="price">$' . $tshirt["price"] . '</h4>
	</a>
</div>');
