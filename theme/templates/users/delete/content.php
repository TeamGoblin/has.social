<div class="views">
	<ul>
		<li><a href="/users/">List</a></li>
		<li><a href="/users/new">New</a></li>
		<li class="current">Delete</li>
	</ul>
	<hr class="light"/>
</div>
<div class="content">
	<form method="post" action="/users/deleteAction">
		<input type="hidden" name="id" value="<?php echo $tmpUser->get('id'); ?>"/>
	<div class="form-item">
		<label for="fname">Are you sure you want to delete the following user?</label>
		<?php echo $tmpUser->get('fname') . " " . $tmpUser->get('lname') . " " . $tmpUser->get('email') . "?"; ?>
	</div>
	<div class="form-item">
		<a href="/users" class="btn btn-secondary">Cancel</a>
		<input type="submit" value="Delete"/>
	</div>
	</form>
</div>

</div>
<!-- End Row -->
</div>
<!-- End Login Container -->