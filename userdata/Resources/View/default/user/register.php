<!-- form-template -->
<div class="section-authentication-register d-flex align-items-center justify-content-center">
	<div class="row" style="min-width:540px">
		<div class="col-12 col-lg-10 mx-auto">
			<div class="card radius-15">
				<div class="row no-gutters">
					<div style="width: 100%;">
						<div class="card-body p-md-5">
							{<{errors}>}
							<form action="/registration" method="post" autocomplete="on">
								<div class="text-center">
									<img src="/assets/images/logo-icon.png" width="80" alt="">
									<h3 class="mt-4 font-weight-bold">Create an Account Test</h3>
								</div>
								<div class="login-separater text-center">
									<hr/>
								</div>
								<div class="form-group mt-4">
									<label>Username</label>
									<input type="text" name="username" class="form-control" placeholder="Your username">
								</div>
								<div class="form-group">
									<label>Email Address</label>
									<input type="text" name="email" class="form-control" placeholder="example@mail.com">
								</div>
								<div class="form-group">
									<label>Password</label>
									<div class="input-group" id="show_hide_password">
										<input type="password" name="password[first]" class="form-control border-right-0" placeholder="Password">
										<div class="input-group-append"><a href="javascript:;" class="input-group-text border-left-0"><i class='bx bx-hide'></i></a>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label>Password Confirm</label>
									<input type="password" name="password[second]" class="form-control" placeholder="Password">
								</div>
								<div class="btn-group mt-3 w-100">
									<button type="submit" class="btn btn-light btn-block">Register <i class="lni lni-arrow-right"></i></button>
								</div>
								<hr/>
								<div class="text-center mt-4">
									<p class="mb-0">У вас уже есть учетная запись? <a href="/login">Login</a>
									</p>
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