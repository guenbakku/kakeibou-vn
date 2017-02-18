ALTER TABLE users CHANGE lock_interval lock_duration int(11);
ALTER TABLE `users` CHANGE `login_attemps` `login_attemps` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `users` CHANGE `lock_duration` `lock_duration` INT(11) NOT NULL DEFAULT '0';
CREATE TABLE `kakeibou`.`remember` ( `id` INT NOT NULL AUTO_INCREMENT, `token` VARCHAR(40) NOT NULL , `user_id` INT NOT NULL , `expire_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, `created_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `user_agent` VARCHAR(512) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, PRIMARY KEY (`id`), UNIQUE (`token`)) ENGINE = InnoDB;

-- Modify existed password to blowfish hash format (raw: 'bh1234')
UPDATE `users` SET `password` = '$2y$10$nP3mpna8r5tHuyBp1KOeweR3hO.b4k9Z8NkGtsYSj.45GMdfHZEvi' WHERE `users`.`id` = 1;
UPDATE `users` SET `password` = '$2y$10$nP3mpna8r5tHuyBp1KOeweR3hO.b4k9Z8NkGtsYSj.45GMdfHZEvi' WHERE `users`.`id` = 2;