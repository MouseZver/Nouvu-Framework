<!-- form-template -->
<div class="section-authentication-login d-flex align-items-center justify-content-center">
	<div class="row" style="min-width:540px">
		<div class="col-12 col-lg-10 mx-auto">
			<div class="card radius-15">
				<div class="row no-gutters">
					<div style="width: 100%;">
						<div class="card-body p-md-5">
							{<{errors}>}
							<form action="/login" method="post" autocomplete="on">
								<div class="text-center">
									<img src="/assets/images/logo-icon.png" width="80" alt="">
									<h3 class="mt-4 font-weight-bold">Login Account Test</h3>
								</div>
								<div class="login-separater text-center">
									<hr/>
								</div>
								<div class="form-group mt-4">
									<label>Username / Email Address</label>
									<input type="text" name="login" value="{<{login}>}" class="form-control" placeholder="Enter your username / email address" >
								</div>
								<div class="form-group">
									<label>Password</label>
									<input type="password" name="password" class="form-control" placeholder="Enter your password">
								</div>
								<div class="form-row">
									<div class="form-group col">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" name="remember_check" class="custom-control-input" id="customSwitch1" checked>
											<label class="custom-control-label" for="customSwitch1">Remember Me</label>
										</div>
									</div>
									<div class="form-group col text-right">
										<a href="/forgot-password"><i class='bx bxs-key mr-2'></i>Забыли пароль?</a>
									</div>
								</div>
								<div class="btn-group mt-3 w-100">
									<button type="submit" class="btn btn-light btn-block">Авторизоваться <i class="lni lni-arrow-right"></i></button>
								</div>
								<hr>
								<div class="text-center">
									<p class="mb-0">У вас нет учетной записи? <a href="/registration">Регистрация</a></p>
								</div>
							</form>
						</div>
					</div>
				</div>
				<!--end row-->
			</div>
		</div>
	</div>
</div>