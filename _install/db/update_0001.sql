-- Update schema of table `users`
ALTER TABLE `users` CHANGE `lock_interval` `lock_duration` int(11);
ALTER TABLE `users` CHANGE `login_attemps` `login_attemps` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `users` CHANGE `lock_duration` `lock_duration` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `users` CHANGE `password` `password` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'blowfish';

-- Create table `remember`
DROP TABLE IF EXISTS `remember`;
CREATE TABLE `remember` (
  `token` varchar(40) NOT NULL,
  `user_id` TINYINT(4) NOT NULL,
  `user_agent` varchar(512) DEFAULT NULL,
  `expire_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `remember` ADD PRIMARY KEY (`token`);
ALTER TABLE `remember` ADD INDEX(`user_id`);
ALTER TABLE `remember` ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Modify existed password to blowfish hash format (raw: 'bh1234')
UPDATE `users` SET `password` = '$2y$10$nP3mpna8r5tHuyBp1KOeweR3hO.b4k9Z8NkGtsYSj.45GMdfHZEvi' WHERE `users`.`id` = 1;
UPDATE `users` SET `password` = '$2y$10$nP3mpna8r5tHuyBp1KOeweR3hO.b4k9Z8NkGtsYSj.45GMdfHZEvi' WHERE `users`.`id` = 2;