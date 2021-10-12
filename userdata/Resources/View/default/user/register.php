<?php
/* <div>
		<div>
			<label for="username">username</label>
		</div>
		<div>
			<input id="username" name="username" required="required" type="text">
		</div>
	</div>
	<div>
		<div>
			<label for="password">password</label>
		</div>
		<div>
			<input id="password" name="password[first]" required="required" type="password">
		</div>
	</div>
	<div>
		<div>
			<label for="password">confirm password</label>
		</div>
		<div>
			<input id="password" name="password[second]" required="required" type="password">
		</div>
	</div> */
?>
<form action="/registration" method="post" autocomplete="on">
	<h1>Регистрация тест форма</h1>
	
	<div>
		<div>
			<label for="email">email</label>
		</div>
		<div>
			<input id="email" name="user[email]" required="required" type="email">
		</div>
	</div>

	<div>
		<div>
			<input name="submit" type="submit">
		</div>
	</div>
</form>