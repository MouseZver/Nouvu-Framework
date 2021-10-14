<div id="errors-list">{<{errors}>}</div>
<form action="/login" method="post" autocomplete="on">
	<h1>Авторизация тест форма</h1>
	<div>
		<div>
			<label for="login">login</label>
		</div>
		<div>
			<input id="login" name="login" type="text" value="{<{login}>}">
		</div>
	</div>
	<div>
		<div>
			<label for="password">password</label>
		</div>
		<div>
			<input id="password" name="password" type="password">
		</div>
	</div>
	<div>
		<div>
			<input name="submit" type="submit">
		</div>
	</div>
</form>