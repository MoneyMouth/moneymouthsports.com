CREATE TABLE `mypicks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `question_id` int(11) unsigned DEFAULT NULL,
  `choice_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `question_id` (`question_id`),
  KEY `choice_id` (`choice_id`),
  CONSTRAINT `mypicks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `mypicks_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `mypicks_ibfk_3` FOREIGN KEY (`choice_id`) REFERENCES `question_choice` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pool` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  CONSTRAINT `pool_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `pool_group` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

CREATE TABLE `pool_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

CREATE TABLE `question` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pool_id` int(11) unsigned NOT NULL,
  `question_group` varchar(255) NOT NULL DEFAULT '',
  `question` varchar(255) NOT NULL DEFAULT '',
  `correct_choice_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pool_id` (`pool_id`),
  KEY `question_group_id` (`question_group`),
  CONSTRAINT `question_ibfk_1` FOREIGN KEY (`pool_id`) REFERENCES `pool` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `question_choice` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(11) unsigned NOT NULL,
  `label` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `question_choice_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_pool` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pool_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pool_id` (`pool_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_pool_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `user_pool_ibfk_2` FOREIGN KEY (`pool_id`) REFERENCES `pool` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `password` (`password`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
