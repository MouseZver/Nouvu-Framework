<div id="errors-list">{<{errors}>}</div>
<form action="/registration" method="post" autocomplete="on">
	<h1>Регистрация тест форма</h1>
	<div>
		<div>
			<label for="username">username</label>
		</div>
		<div>
			<input id="username" name="username" type="text" value="{<{username}>}">
		</div>
	</div>
	<div>
		<div>
			<label for="email">email</label>
		</div>
		<div>
			<input id="email" name="email" type="text" value="{<{email}>}">
		</div>
	</div>
	<div>
		<div>
			<label for="password">password</label>
		</div>
		<div>
			<input id="password" name="password[first]" type="password">
		</div>
	</div>
	<div>
		<div>
			<label for="password">confirm password</label>
		</div>
		<div>
			<input id="password" name="password[second]" type="password">
		</div>
	</div>
	<div>
		<div>
			<input name="submit" type="submit">
		</div>
	</div>
</form>