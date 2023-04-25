<div class="views">
	<ul>
		<li><a href="/users/">List</a></li>
		<li class="current"><a href="/users/new">New</a></li>
	</ul>
	<hr class="light"/>
</div>
<div class="content">
	<form method="post" action="/users/newAction" autocomplete="off">
		<div class="form-item">
			<label for="first_name">First Name</label>
			<input type="text" name="first_name" autocomplete="off"/>
		</div>
		<div class="form-item">
			<label for="last_name">Last Name</label>
			<input type="text" name="last_name" autocomplete="off"/>
		</div>
		<div class="form-item">
			<label for="birth_date">Birth Date</label>
			<input type="date" name="birth_date" value="0000-00-00" min="1920-01-01" autocomplete="off">
			<!-- <input type="text" name="birth_date" autocomplete="off"/> -->
		</div>
		<div class="form-item">
			<label for="gender">Gender</label>
			<select name="gender">
				<option value="M">Male</option>
				<option value="F">Female</option>
			</select>
		</div>
		<div class="form-item">
			<label for="email">Email</label>
			<input type="email" name="email" autocomplete="off"/>
		</div>
		<div class="form-item">
			<label for="password">Password</label>
			<input type="password" name="password" autocomplete="new-password"/>
		</div>
		<div class="form-item">
			<label for="user_type">User Types</label>
			<select name="user_type_id">
			<?php foreach ($user_types->all() as $user_type) {
				echo '<option value="'.$user_type->id.'">'.$user_type->label.'</option>';
			} ?>
			</select>
		</div>
		<div class="form-item">
			<input type="submit" value="Create"/>
		</div>
	</form>
</div>

</div>
<!-- End Row -->
</div>
<!-- End Login Container -->