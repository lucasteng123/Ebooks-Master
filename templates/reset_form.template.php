<div class="h2">Password Reset</div>
<form action="?location=reset_password" method="POST">
	<input class="form-control" name="password" type="password" />
	<input type="hidden" name="id" value="<?php echo $user_id; ?>" />
	<input type="hidden" name="cd" value="<?php echo $user_cd; ?>" />
	<input class="form-control" style="width: 200px;margin-top:15px;" type="submit" value="Change Password" />
</form>