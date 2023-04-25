<!-- Notification Panel -->
<div class="container-fluid notification-center">
	<div class="row">
		<div class="container-fluid" id="notification-panel">
			<?php if (!empty($_n->getMsg())) { ?>
			<div class="row <?php echo $_n->getType(); ?>" id="notification">
				<div class="col-1 notification-icon">
					<img src="/img/<?php echo $_n->getType(); ?>.svg" width="36"/>
				</div>
				<div class="col-10 notification-msg">
					<?php echo $_n->getMsg(); ?>
				</div>
				<div class="col-1 notification-close">
					<button type="button" class="close new-close" data-dismiss="notification"><span aria-hidden="true">&times;</span></button>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>