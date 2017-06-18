	<div class="panel panel-default tshirt-sale-panel" id="tshirt_sale_container">
		<div class="panel-heading">
			<div class="panel-title">
				<?php echo $gcTitle; ?>
			</div>
		</div>
		<div class="panel-body row tshirt-sale-box">
			<div class="col">
				<div class="jq-page tshirt-canvas coverflow-canvas visible" data-name="featured">
					<div class="canvas-slide prev"><span class="glyphicon glyphicon-chevron-left"></span></div>
					<div class="canvas-slide next"><span class="glyphicon glyphicon-chevron-right"></span></div>
					<div class="canvas-slide-under prev"><div class="artificial-padding"></div>[ITEMS]<div class="artificial-padding"></div></div>
					<div class="canvas-slide-under next"><div class="artificial-padding"></div>[ITEMS]<div class="artificial-padding"></div></div>
					<div class="books-container"><!-- wrapper required for scrolling to work -->
					






<?php for ($i=0; $i<8; $i++) { ?>
			<div class="bookitem">
				<div class="cover-container coverflow-item">
					<div class="cover test-cover" style="background-image: url('./res/img/white-t-shirt.svg'); background-size: contain; background-color: none; background-repeat: no-repeat;"></div>
				</div>
				<div class="text-container">
					<h3 class="text-primary">Harry Potter 4 <small>J.K. Rowling</small></h3>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. In porta diam mauris, lacinia consectetur velit laoreet sit amet. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
					</p>
				</div>
			</div><!-- /.bookitem -->
			<?php } ?>
			<div class="bookitem">
				<div class="cover-container coverflow-item">
					<div class="cover test-cover" style="background-image: url('./res/img/white-t-shirt.svg'); background-size: contain; background-color: none; background-repeat: no-repeat;"></div>
				</div>
				<div class="text-container">
					<h3 class="text-primary">Harry Potter 6 <small>J.K. Rowling</small></h3>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. In porta diam mauris, lacinia consectetur velit laoreet sit amet. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
					</p>
				</div>
			</div><!-- /.bookitem -->
			<div class="bookitem">
				<div class="cover-container coverflow-item">
					<div class="cover test-cover" style="background-image: url('./res/img/white-t-shirt.svg'); background-size: contain; background-color: none; background-repeat: no-repeat;"></div>
				</div>
				<div class="text-container">
					<h3 class="text-primary">Harry Potter 4 <small>J.K. Rowling</small></h3>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. In porta diam mauris, lacinia consectetur velit laoreet sit amet. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
					</p>
				</div>
			</div><!-- /.bookitem -->
			<div class="bookitem">
				<div class="cover-container coverflow-item">
					<div class="cover test-cover" style="background-image: url('./res/img/white-t-shirt.svg'); background-size: contain; background-color: none; background-repeat: no-repeat;"></div>
				</div>
				<div class="text-container">
					<h3 class="text-primary">The Secret Under my Skin <small>Janet McNaughton</small></h3>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. In porta diam mauris, lacinia consectetur velit laoreet sit amet. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
					</p>
				</div>
			</div><!-- /.bookitem -->
			<div class="bookitem">
				<div class="cover-container coverflow-item">
					<div class="cover test-cover" style="background-image: url('./res/img/white-t-shirt.svg'); background-size: contain; background-color: none; background-repeat: no-repeat;"></div>
				</div>
				<div class="text-container">
					<h3 class="text-primary">Harry Potter 6 <small>J.K. Rowling</small></h3>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. In porta diam mauris, lacinia consectetur velit laoreet sit amet. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
					</p>
				</div>
			</div><!-- /.bookitem -->





					
						<div class="artificial-padding"></div>
					</div><!-- /.books-container -->
				</div><!-- /.feature-canvas -->
			</div>
			<div class="col col-lg-5 offset-fix">
				&lt; Shirt preview will go here &gt;<br />
				<div class="tee-canvas">
					<img src="res/img/white-t-shirt.svg" style="width:100%;visibility:hidden" />
					<img class="tee-image" src="https://dummyimage.com/300x400/000/fff.png&text=Tshirt+Image" />
				</div>
			</div>
			<div class="col col-lg-7 offset-fix part-for-form">

				<div class="alert alert-info">
					<strong>Tshirt Title</strong><br />
					Tshirt Description
				</div>

				<div class="alert alert-warning">
					<strong>Tip:</strong> For order issues contact support@bestebooks.ca
				</div>

				<div class="alert alert-danger">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<strong>Error:</strong> I will appear here if an error occurs
				</div>

				<form class="form-horizontal">
				<fieldset>


				<!-- change col-sm-N to reflect how you would like your column spacing (http://getbootstrap.com/css/#forms-control-sizes) -->

				<!-- Button Group http://getbootstrap.com/components/#btn-groups -->
				<div class="form-group">
				  <label class="control-label col-sm-3">Colour</label>
				  <div class="text-right col-sm-9">
				    <div id="button1idGroup" class="btn-group" role="group" aria-label="Button Group">
				      <button type="button" id="button1id" name="button1id"
				      	class="btn btn-default colour-button"
				      	style="background-color: hsl(0,0%,100%);"
				      	aria-label="Left Button">&nbsp;</button>
				      <?php for ($i=0; $i < 360; $i += 30) { ?>
					      	<button type="button" id="button1id" name="button1id"
					      	class="btn btn-default colour-button"
					      	style="background-color: hsl(<?php echo $i; ?>,80%,80%);"
					      	aria-label="Left Button">&nbsp;</button>
				      <?php } ?>
				    </div>
				    <div id="button1idGroup" class="btn-group" role="group" aria-label="Button Group">
				      <button type="button" id="button1id" name="button1id"
				      	class="btn btn-default colour-button"
				      	style="background-color: hsl(0,0%,0%);"
				      	aria-label="Left Button">&nbsp;</button>
				      <?php for ($i=0; $i < 360; $i += 30) { ?>
					      	<button type="button" id="button1id" name="button1id"
					      	class="btn btn-default colour-button"
					      	style="background-color: hsl(<?php echo $i; ?>,80%,20%);"
					      	aria-label="Left Button">&nbsp;</button>
				      <?php } ?>
				    </div>
				    <p class="help-block">Select your desired background colour</p>
				  </div>
				</div>

				<!-- Button Group http://getbootstrap.com/components/#btn-groups -->
				<div class="form-group">
				  <label class="control-label col-sm-3">Size</label>
				  <div class="text-right col-sm-9">
				    <div id="button1idGroup" class="btn-group" role="group" aria-label="Button Group">
				      <?php foreach (array('S','M','L','XL','2XL','3XL','4XL','5XL') as $size) { ?>
				      	<button type="button" id="button1id" name="button1id" class="btn btn-default" aria-label="Left Button"><?php echo $size; ?></button>
				      <?php } ?>
				    </div>
				    <p class="help-block">Select the size you wish to purchase</p>
				  </div>
				</div>

				<div class="form-group">
				  <label class="control-label col-sm-3">Quantity</label>
				  <label class="control-label col-sm-9" style="text-align:left;">Purchase Options</label>
				  <div class="text-right col-sm-3">
				    <select>
				    	<?php for ($i=1; $i <= 100; $i++) { ?>
				    		<option><?php echo $i; ?></option>
				   		<?php } ?>
				    </select>
				  </div>
				  <div class="text-right col-sm-9">
				  	  <!--<button style="width:347px;"
				  	  type="button" id="singlebutton" name="singlebutton" class="btn btn-primary" aria-label="Single Button">Purchase</button>
				  	  <p class="help-block">help</p>-->
				  	  PayPal Button goes here
				  </div>
				</div>


				</fieldset>
				</form>
			</div>
		</div>
	</div>