<div class="col-12">
	<div class="logo">
		<img src="/img/JET-Logo.svg"/>
	</div>
	<div class="login">
		<h2 class="title">Forgot Password</h2>
		<?php if ($pass) { ?>
			<!-- IF EMAIL FOUND -->
			<p>An e-mail has been sent to your given address with instructions on how to reset your password.</p>
		<?php } else { ?>
			<!-- IF EMAIL NOT FOUND -->
			<p>The given email address was not found.</p>
		<?php } ?>
		<a href="/user/login">Return to login</a>
	</div>
</div>

</div>
<!-- End Row -->
</div>
<!-- End Login Container -->