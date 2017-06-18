<?php
echo('
	<div class="row">
		<div class="col-md-7">
			<img src = "' . WEB_PATH . $tshirt[ "image" ] . '" class="img-thumbnail" />
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
');