<? // Style Sheet @TODO Add CSS to CSS file ?>
<style>
	.content input[disabled], select[disabled] {
	    background-color: #DCDAD1 !important;
	    color: #8e8e8e !important;
	    padding: 2px;
	    margin: 0 0 0 0;
	    background-image: none;
	}
</style>
<?php $_user_type = $pilot->get('user_type'); ?>
<div class="views">
	<ul>
		<?php if ($_user_type == 'superuser' || $_user_type == 'adminuser') { ?>
		<li><a href="/users/">List</a></li>
		<li><a href="/users/new">New</a></li>
		<?php } ?>
		<li class="current">Edit</li>
	</ul>
	<hr class="light"/>
</div>
<div class="content">
	<form method="post" action="/users/editAction">
		<input type="hidden" name="id" value="<?php echo $tmpUser->get('id'); ?>" />
		<div class="form-item">
			<label for="first_name">First Name</label>
			<input type="text" name="first_name" autocomplete="off" value="<?php echo $tmpUser->get('first_name'); ?>" />
		</div>
		<div class="form-item">
			<label for="last_name">Last Name</label>
			<input type="text" name="last_name" autocomplete="off" value="<?php echo $tmpUser->get('last_name'); ?>" />
		</div>
		<div class="form-item">
			<label for="birth_date">Birth Date</label>
			<input type="date" name="birth_date" min="1920-01-01" autocomplete="off" value="<?php echo $tmpUser->get('birth_date'); ?>" />
			<!-- <input type="text" name="birth_date" autocomplete="off"/> -->
		</div>
		<?php // set $gender to check option selector value ?>
		<?php $gender = $tmpUser->get('gender'); ?>
		<div class="form-item">
			<label for="gender">Gender</label>
			<!-- <select name="gender" disabled="disabled" readonly> -->
			<select name="gender">
				<option value="M" <? if($gender == 'M'){ echo 'selected="selected"'; }?> >Male</option>
				<option value="F" <? if($gender == 'F'){ echo 'selected="selected"'; }?> >Female</option>
			</select>
		</div>
		<div class="form-item">
			<label for="email">Email</label>
			<input type="email" name="email" autocomplete="off" value="<?php echo $tmpUser->get('email'); ?>"/>
		</div>
		<div class="form-item">
			<label for="newpassword">Password</label>
			<input type="password" name="newpassword" autocomplete="new-password"/>
		</div>
		<?php if ($_user_type == 'superuser' || $_user_type == 'adminuser') { ?>
		<div class="form-item">
			<label for="user_type">User Types</label>
			<select name="user_type_id">
			<?php foreach ($user_types->all() as $user_type) {
				if ($tmpUser->get('user_type') == $user_type->id) {
					echo '<option value="'.$user_type->id.'" selected=selected>'.$user_type->label.'</option>';
				} else {
					echo '<option value="'.$user_type->id.'">'.$user_type->label.'</option>';	
				}
			} ?>
			</select>
		</div>
		<?php } ?>
		<div class="form-item">
			<input type="submit" value="Save"/>
		</div>
	</form>
</div>

</div>
<!-- End Row -->
</div>
<!-- End Login Container -->