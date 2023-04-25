<div class="col-12">
	<div class="logo">
		<img src="/img/agtech-logo-white.svg"/>
	</div>
	<div class="login">
		<h2 class="title">Reset Password</h2>
		<form method="post" action="/user/reset">
		<div class="form-item">
			<label for="password">New Password</label>
			<input type="password" name="password"/>
			<input type="hidden" id="id" name="id" value="<?php echo $tmpUser->get('id'); ?>"/>
			<input type="hidden" id="key" name="key" value="<?php echo $key; ?>"/>
		</div>
		<div class="form-item">
			<input type="submit" value="Reset"/>
		</div>
		</form>
	</div>
</div>

</div>
<!-- End Row -->
</div>
<!-- End Login Container -->