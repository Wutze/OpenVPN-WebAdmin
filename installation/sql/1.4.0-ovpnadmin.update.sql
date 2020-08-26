DROP TABLE `usergroups`;
ALTER TABLE `groupnames` CHANGE `gname` `gid` INT(10) NOT NULL AUTO_INCREMENT; 

ALTER TABLE `user` CHANGE `user_id` `user_name` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL; 

ALTER TABLE `log` CHANGE `user_id` `user_name` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL; 


/*** vermutlich mit einbauen, Datenbank noch testen ***/
ALTER TABLE `log` ADD UNIQUE(`log_id`); 
ALTER TABLE `log` ADD INDEX(`log_start_time`); 
