<!-- form-template -->
<div class="authentication-forgot d-flex align-items-center justify-content-center">
	<div class="card shadow-lg forgot-box">
		<div class="card-body p-md-5">
			{<{errors}>}
			<form action="/forgot-password" method="post" autocomplete="on">
				<div class="text-center">
					<img src="/assets/images/icons/forgot-2.png" width="140" alt="" />
				</div>
				<h4 class="mt-5 font-weight-bold text-white">Забыли пароль?</h4>
				<div class="login-separater text-center">
					<hr/>
				</div>
				<p class="">Enter your registered email ID to reset the password</p>
				<div class="form-group">
					<label>Email Adress</label>
					<input type="text" name="email" class="form-control" placeholder="example@mail.com">
				</div>
				<button type="submit" class="btn btn-light btn-block radius-30">Send</button>
				<a href="/login" class="btn btn-link btn-block">Sign up</a>
			</form>
		</div>
	</div>
</div>