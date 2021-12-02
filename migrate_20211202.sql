CREATE TABLE `uvu_forgot_pass` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `confirm` varchar(32) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `uvu_logger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `group` text NOT NULL,
  `message` text,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `uvu_rememberme_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `series` text NOT NULL,
  `value` text NOT NULL,
  `lastUsed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `uvu_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` tinytext NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL,
  `roles` json NOT NULL,
  `email_confirmed` int(11) NOT NULL DEFAULT '0',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `uvu_users_pre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `confirm` varchar(32) NOT NULL,
  `email` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;