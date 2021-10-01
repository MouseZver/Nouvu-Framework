<!DOCTYPE html>
<html lang="{<{locale}>}">

<head>
	<title>{<{title}>}</title>
	{<{head=meta-charset|meta-viewport|js-jquery|js-popper|js-bootstrap|js-pace|css-pace|css-bootstrap|css-icons|css-app}>}
</head>

<body class="bg-theme bg-theme1">
	<!-- wrapper -->
	<div class="wrapper">
		<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top border-bottom">
			<!-- <a class="navbar-brand" href="javascript:;">
				<img src="/assets/images/logo-img.png" width="160" alt="">
			</a> -->
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">	<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav ml-auto">
					<li class="nav-item active">
						<a class="nav-link" href="/">Home</a>
					</li>
					<!-- <li class="nav-item">
						<a class="nav-link" href="/about">About</a>
					</li> -->
				</ul>
			</div>
		</nav>
		<div class="error-404 d-flex align-items-center justify-content-center">
			<div class="container">
				<div class="card shadow-none bg-transparent">
<!--start row-->

{<{content}>}

<!--end row-->
				</div>
			</div>
		</div>
	</div>
	<!-- end wrapper -->
</body>

</html>