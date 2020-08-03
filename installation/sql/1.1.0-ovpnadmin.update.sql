ALTER TABLE `user` DROP PRIMARY KEY;

ALTER TABLE `user` ADD `uid` INT(10) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`uid`);

ALTER TABLE `user` ADD `gid` INT(10) NOT NULL AFTER `user_id`;

CREATE TABLE `usergroups` (
  `gid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `gname` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `usergroups` (`gid`, `uid`, `gname`) VALUES
(1, 1, 1);

ALTER TABLE `usergroups`
  ADD PRIMARY KEY (`gid`);

ALTER TABLE `usergroups`
  MODIFY `gid` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

CREATE TABLE `groupnames` (
  `gname` int(10) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `groupnames` (`gname`, `name`) VALUES
(1, 'admin'),
(2, 'user');

ALTER TABLE `groupnames`
  ADD PRIMARY KEY (`gname`);

ALTER TABLE `groupnames`
  MODIFY `gname` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

ALTER TABLE `user` CHANGE `user_id` `user_id` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `log` CHANGE `user_id` `user_id` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

DROP TABLE `admin`, `application`;

