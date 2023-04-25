<div class="container-fluid">
	<div class="row background">
		<div class="container" id="notification-js">
			<div class="row">
				<div class="col-xs-1" id="notification-js-icon">
					<img src="/img/error.png" />
				</div>
				<div class="col-xs-10" id="notification-js-text">
					<ul id="notification-list"></ul>
				</div>
				<div class="col-xs-1" id="notification-js-close">
					<button type="button" class="close" data-dismiss="notification-js"><span aria-hidden="true">&times;</span></button>
				</div>
			</div>
		</div>
		<div class="container inner" id="login-div">
			<div class="row highlight-b row-centered">
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 col-xl-3 txt-center col-centered">
					<form method="POST" action="/user/loginAction">
						<div class="input-group input-group-sm">
							<input class="form-control" id="email" type="email" name="email" placeholder="email" />
						</div>
						<div class="input-group input-group-sm">
							<input class="form-control" id="password" type="password" name="password" placeholder="password" />
						</div>
						<input class="form-control btn btn-primary btn-block" id="login-button" type="submit" name="submit" value="Hog">
					</form>
				</div>
				<!--
				<div class="col-lg-6 txt-center">
					Signup<br/>
					<form method="POST" action="/api/user/signup">
						<input type="name" name="name" placeholder="name"><br/>
						<input type="email" name="email" placeholder="email"><br/>
						<input type="password" name="password" placeholder="password"><br/>
						<input type="password" name="password_confirm" placeholder="confirm password"><br/>
						<input type="submit" name="submit" placeholder="Signup" value="Signup">
					</form>
				</div>
				-->
			</div>
		</div>
	</div>
</div>