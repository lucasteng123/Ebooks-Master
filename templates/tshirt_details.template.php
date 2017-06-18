
<div class="container">
	<div class="row">
		<h1><?php echo $tshirt[ "name" ]; ?></h1>
		<hr>
	</div>

	<div class="row">
		<div class="col-md-7">
			<img src = "<?php echo $tshirt[ "image" ]; ?>" class="img-thumbnail" />
		</div>
		<div class="col-md-5">
			<h2>Order this shirt</h2>
			<hr>
			<h3><strong><?php echo $tshirt[ "price" ]; ?></strong><span style="font-weight: 200">/shirt</span></h3>
			<form action="/order/place/<?php echo $id; ?>" method="post">
				<div class="form-group">
					<label for="quantity"> Quantity </label>
					<input type="text" class="form-control" name="quantity">
				</div>
				<h4>Please choose colour</h4><div class="col-md-12">' );
			<?php
			foreach ( $colors as $color ) {
				echo( '
				<div class="form-group col-md-2" style="text-align:center; padding:0px 10px; background-color: ' . $color . ';">
					<input type="radio" class="form-control" style="display: inline-block;" value="' . $color . '" name="colors">
				</div>' );
			}
			?>
				
				</div>
				<div class="form-group">
					<label for="email"> Email Address </label>
					<input type="text" class="form-control" name="email">
				</div>
				
				<div class="form-group">
					<input class="btn btn-primary" type="submit">
				</div>
			</form>	

		</div>
	</div>
</div>
