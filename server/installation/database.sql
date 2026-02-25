SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE groupnames (
  gid int(10) UNSIGNED NOT NULL,
  name varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE log (
  log_id int(10) UNSIGNED NOT NULL,
  user_name varchar(32) NOT NULL,
  log_trusted_ip varchar(45) DEFAULT NULL,
  log_trusted_port varchar(16) DEFAULT NULL,
  log_remote_ip varchar(45) DEFAULT NULL,
  log_remote_port varchar(16) DEFAULT NULL,
  log_start_time timestamp NOT NULL DEFAULT current_timestamp(),
  log_end_time timestamp NULL DEFAULT NULL,
  log_received bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  log_send bigint(20) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sessions2 (
  sesskey varchar(64) NOT NULL,
  expiry datetime NOT NULL,
  expireref varchar(250) DEFAULT '',
  created datetime NOT NULL,
  modified datetime NOT NULL,
  sessdata longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user` (
  uid int(10) UNSIGNED NOT NULL,
  user_name varchar(32) NOT NULL,
  gid int(10) UNSIGNED NOT NULL,
  user_pass varchar(255) NOT NULL,
  user_mail varchar(64) DEFAULT NULL,
  user_phone varchar(16) DEFAULT NULL,
  user_online tinyint(1) NOT NULL DEFAULT 0,
  user_enable tinyint(1) NOT NULL DEFAULT 1,
  user_start_date date DEFAULT NULL,
  user_end_date date DEFAULT NULL,
  session_id varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_ip (
  ipid int(11) UNSIGNED NOT NULL,
  uid int(10) UNSIGNED NOT NULL,
  user_ip varchar(45) NOT NULL,
  netmask varchar(45) NOT NULL,
  server_ip varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE groupnames
  ADD PRIMARY KEY (gid),
  ADD UNIQUE KEY uniq_name (name);

ALTER TABLE log
  ADD PRIMARY KEY (log_id),
  ADD KEY idx_user_time (user_name,log_start_time);

ALTER TABLE sessions2
  ADD PRIMARY KEY (sesskey);

ALTER TABLE user
  ADD PRIMARY KEY (uid),
  ADD UNIQUE KEY uniq_user_name (user_name),
  ADD KEY gid (gid);

ALTER TABLE user_ip
  ADD PRIMARY KEY (ipid),
  ADD KEY uid (uid);


ALTER TABLE groupnames
  MODIFY gid int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE log
  MODIFY log_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE user
  MODIFY uid int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE user_ip
  MODIFY ipid int(11) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE user
  ADD CONSTRAINT user_ibfk_1 FOREIGN KEY (gid) REFERENCES groupnames (gid) ON DELETE CASCADE;

ALTER TABLE user_ip
  ADD CONSTRAINT user_ip_ibfk_1 FOREIGN KEY (uid) REFERENCES `user` (uid) ON DELETE CASCADE;
