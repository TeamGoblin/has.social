<div class="views">
	<ul>
		<li class="current">List</li>
		<li><a href="/users/new">New</a></li>
	</ul>
	<hr class="light"/>
</div>
<div class="content">
	<table class="content-list" id="user-list">
		<thead>
			<tr>
				<th>First Name</th>
				<th>Last Name</th>
				<th>Email</th>
				<th>Role</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($users->all() as $user) { ?>
				<tr data-id="<?php echo $user->get('id'); ?>">
					<td><?php echo $user->get('first_name'); ?></td>
					<td><?php echo $user->get('last_name'); ?></td>
					<td><?php echo $user->get('email'); ?></td>
					<td><?php echo $user->get('user_type'); ?></td>
					<td><a href="/users/delete/<?php echo $user->get('id'); ?>"><img src="/img/delete.svg" width="20"/></a></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>

</div>
<!-- End Row -->
</div>
<!-- End Login Container -->