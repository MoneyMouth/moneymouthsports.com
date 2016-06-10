CREATE TABLE `users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) DEFAULT NULL,
  `password` VARCHAR(255) DEFAULT NULL,
  `name` VARCHAR(255) DEFAULT '',
  `email` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `password` (`password`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `pool_group` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_name` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

CREATE TABLE `pool` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` INT(11) UNSIGNED NOT NULL,
  `type` VARCHAR(255) NOT NULL DEFAULT '',
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  CONSTRAINT `pool_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `pool_group` (`id`) ON UPDATE CASCADE
) ENGINE=INNODB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

CREATE TABLE `question_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `question_type` (`id`, `name`)
VALUES
  (1, 'radio'),
  (2, 'text');

CREATE TABLE `question` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pool_id` INT(11) UNSIGNED NOT NULL,
  `question_group` VARCHAR(255) NOT NULL DEFAULT '',
  `question` VARCHAR(255) NOT NULL DEFAULT '',
  `correct_choice_id` INT(11) DEFAULT NULL,
  `type_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pool_id` (`pool_id`),
  KEY `question_group_id` (`question_group`),
  KEY `type` (`type_id`),
  CONSTRAINT `question_ibfk_1` FOREIGN KEY (`pool_id`) REFERENCES `pool` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `question_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `question_type` (`id`) ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8;


CREATE TABLE `question_choice` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `question_id` INT(11) UNSIGNED NOT NULL,
  `label` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `question_choice_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `user_pool` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pool_id` INT(11) UNSIGNED NOT NULL,
  `user_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pool_id` (`pool_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_pool_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `user_pool_ibfk_2` FOREIGN KEY (`pool_id`) REFERENCES `pool` (`id`) ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `mypicks` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED DEFAULT NULL,
  `question_id` INT(11) UNSIGNED DEFAULT NULL,
  `choice_id` INT(11) UNSIGNED DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_2` (`user_id`,`question_id`),
  KEY `user_id` (`user_id`),
  KEY `question_id` (`question_id`),
  KEY `choice_id` (`choice_id`),
  CONSTRAINT `mypicks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `mypicks_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `mypicks_ibfk_3` FOREIGN KEY (`choice_id`) REFERENCES `question_choice` (`id`) ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8;
