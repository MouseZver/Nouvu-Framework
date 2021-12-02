<?php

return [
	'form' => [
		/*
			- 
		*/
		'registration' => [
			[ 'username', 'Length', [ 
				'min' => 3, 'max' => 50, 
				'minMessage' => 'Имя пользователя должно содержать не менее {{ limit }} символов',
				'maxMessage' => 'Имя пользователя не может быть длиннее {{ limit }} символов'
			] ],
			[ 'username', 'Regex', [ 
				'pattern' => '/^\w+$/i', 
				'message' => 'Имя пользователя может содержать только [A-Za-z0-9_] символы',
			] ],
			'email|Email|message:Неверный адрес электронной почты',
			'email|NotBlank|message:Адрес электронной почты не заполнен',
			[ 'password.first', 'Length', [ 
				'min' => 10, 
				'max' => 100,
				'minMessage' => 'Пароль должен содержать не менее {{ limit }} символов',
				'maxMessage' => 'Пароль превышает {{ limit }} символов',
			] ],
			'password.second',
			'_username|IsTrue|message:Имя пользователя уже используются',
			'_email|IsTrue|message:Адрес электронной почты уже используются',
			'_password|IsTrue|message:Пароль не совпадает с паролем для подтверждения',
			'_username-email|IsTrue|message:Имя пользователя не должен быть одинаковым с эл. почтой',
		],
		/*
			- 
		*/
		'login' => [
			'_isPasswordValid|IsTrue|message:Неверный логин или пароль',
			'_userFound|IsTrue|message:Пользователь не найден',
			'_account|IsTrue|message:Аккаунт не активирован',
		],
		/*
			- 
		*/
		'forgotPassword' => [
			'email|Email|message:Неверный адрес электронной почты',
			'email|NotBlank|message:Адрес электронной почты не заполнен',
			'_email|IsTrue|message:Адрес электронной почты не найден',
		],
		/*
			- 
		*/
		'lostPassword' => [
			[ 'password.first', 'Length', [ 
				'min' => 10, 
				'max' => 100,
				'minMessage' => 'Пароль должен содержать не менее {{ limit }} символов',
				'maxMessage' => 'Пароль превышает {{ limit }} символов',
			] ],
			'password.second',
			'_password|IsTrue|message:Пароль не совпадает с паролем для подтверждения',
		],
	],
];